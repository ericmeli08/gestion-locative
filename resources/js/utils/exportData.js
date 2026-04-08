import XLSX from 'xlsx-js-style'

// ─── Palette couleurs (ARGB sans #) ──────────────────────────────────────────
const C = {
    darkNavy: 'FF0F2044',
    midBlue: 'FF1E3A6E',
    accentGold: 'FFC9922A',
    accentLight: 'FFE8B84B',
    bgLight: 'FFF4F7FB',
    bgAlt: 'FFEBF0FA',
    white: 'FFFFFFFF',
    green: 'FF1A7A4A',
    red: 'FFC0392B',
    greyText: 'FF8899AA',
}

// ─── Helpers styles ───────────────────────────────────────────────────────────
const font = (opts = {}) => ({ name: 'Arial', sz: 10, color: { rgb: C.darkNavy.slice(2) }, ...opts })
const fill = (fgColor) => ({ patternType: 'solid', fgColor: { rgb: fgColor.slice(2) } })
const align = (h = 'center', v = 'center', wrap = false) => ({ horizontal: h, vertical: v, wrapText: wrap })
const thinBorder = (color = 'BEC7D8') => ({
    top: { style: 'thin', color: { rgb: color } },
    bottom: { style: 'thin', color: { rgb: color } },
    left: { style: 'thin', color: { rgb: color } },
    right: { style: 'thin', color: { rgb: color } },
})
const moneyFmt = '#,##0 "F";[Red]-#,##0 "F";"-"'
const pctFmt = '0.0%'

// ─── Applique un style à une cellule ─────────────────────────────────────────
function styleCell(ws, addr, style) {
    if (!ws[addr]) ws[addr] = { t: 'z' }
    ws[addr].s = style
}

// ─── Fusionne des cellules (SheetJS Pro requis pour rendu mais on pose !merges) ─
function merge(ws, from, to) {
    if (!ws['!merges']) ws['!merges'] = []
    const s = XLSX.utils.decode_cell(from)
    const e = XLSX.utils.decode_cell(to)
    ws['!merges'].push({ s, e })
}

// ═══════════════════════════════════════════════════════════════════════════════
// EXPORT PRINCIPAL
// ═══════════════════════════════════════════════════════════════════════════════
export const exportData = (displayData, isDaily, monthName) => {
    const data = displayData || []
    const wb = XLSX.utils.book_new()

    if (isDaily) {
        buildDailySheet(wb, data, monthName)
    } else {
        buildMonthlySheet(wb, data)
        buildSummarySheet(wb, data)
    }

    const now = new Date()
    const fileName = isDaily
        ? `rapport-journalier-${(monthName || '').replace(' ', '-')}.xlsx`
        : `rapport-mensuel-${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}.xlsx`

    XLSX.writeFile(wb, fileName)
}

// ═══════════════════════════════════════════════════════════════════════════════
// FEUILLE RAPPORT MENSUEL
// ═══════════════════════════════════════════════════════════════════════════════
function buildMonthlySheet(wb, data) {
    const now = new Date()
    const rows = []
    const COL_COUNT = 6

    // ── Ligne vide (espacement) ───────────────────────────────────────────────
    rows.push(new Array(COL_COUNT).fill(''))

    // ── Bannière titre ────────────────────────────────────────────────────────
    rows.push(['RAPPORT FINANCIER MENSUEL', '', '', '', '', ''])

    // ── Ligne dorée (simulée via border top épaisse sur la ligne suivante) ────
    rows.push(new Array(COL_COUNT).fill(''))

    // ── Sous-titre + date ─────────────────────────────────────────────────────
    rows.push([`Exercice ${now.getFullYear()}`, '', '', '', `Généré le ${now.toLocaleDateString('fr-FR')}`, ''])

    // ── En-têtes colonnes ─────────────────────────────────────────────────────
    rows.push(['Mois', 'Revenus (F CFA)', 'Dépenses (F CFA)', 'Charges (F CFA)', 'Bénéfice (F CFA)', 'Marge (%)'])

    // ── Données ───────────────────────────────────────────────────────────────
    const DATA_START_IDX = rows.length // index base 0 dans rows[]
    data.forEach(item => {
        rows.push([
            item.month,
            { t: 'n', v: parseFloat(item.revenue) || 0, z: moneyFmt },
            { t: 'n', v: parseFloat(item.expenses) || 0, z: moneyFmt },
            { t: 'n', v: parseFloat(item.charges) || 0, z: moneyFmt },
            { t: 'n', v: parseFloat(item.profit) || 0, z: moneyFmt },
            { t: 'n', v: (parseFloat(item.margin) || 0) / 100, z: pctFmt },
        ])
    })

    // ── Ligne total ───────────────────────────────────────────────────────────
    const DR = DATA_START_IDX + 1             // row Excel (1-based)
    const D_E = DATA_START_IDX + data.length   // row Excel last data

    const totalRevenue = data.reduce((s, i) => s + (parseFloat(i.revenue) || 0), 0)
    const totalExpenses = data.reduce((s, i) => s + (parseFloat(i.expenses) || 0), 0)
    const totalCharges = data.reduce((s, i) => s + (parseFloat(i.charges) || 0), 0)
    const totalProfit = totalRevenue - totalExpenses - totalCharges
    const avgMargin = totalRevenue ? totalProfit / totalRevenue : 0

    rows.push([
        'TOTAL ANNUEL',
        { t: 'n', v: totalRevenue, z: moneyFmt },
        { t: 'n', v: totalExpenses, z: moneyFmt },
        { t: 'n', v: totalCharges, z: moneyFmt },
        { t: 'n', v: totalProfit, z: moneyFmt },
        { t: 'n', v: avgMargin, z: pctFmt },
    ])

    // ── Construction feuille ─────────────────────────────────────────────────
    const ws = XLSX.utils.aoa_to_sheet(rows)

    // ── Dimensions colonnes ───────────────────────────────────────────────────
    ws['!cols'] = [
        { wch: 18 }, { wch: 20 }, { wch: 20 }, { wch: 20 }, { wch: 20 }, { wch: 12 },
    ]

    // ── Hauteurs de lignes ────────────────────────────────────────────────────
    ws['!rows'] = [
        { hpt: 12 },                               // R1 – vide
        { hpt: 40 },                               // R2 – bannière
        { hpt: 6 },                               // R3 – barre or
        { hpt: 18 },                               // R4 – sous-titre
        { hpt: 30 },                               // R5 – headers
        ...data.map(() => ({ hpt: 20 })),           // data rows
        { hpt: 26 },                               // total
    ]

    // ── Fusions ───────────────────────────────────────────────────────────────
    merge(ws, 'A2', 'F2')   // Bannière
    merge(ws, 'A3', 'F3')   // Barre or
    merge(ws, 'A4', 'D4')   // Exercice
    merge(ws, 'E4', 'F4')   // Date

    // ── Style bannière ────────────────────────────────────────────────────────
    ws['A2'].s = {
        font: font({ bold: true, sz: 18, color: { rgb: 'FFFFFF' } }),
        fill: fill(C.darkNavy),
        alignment: align('center', 'center'),
    }

        // ── Style sous-titre ──────────────────────────────────────────────────────
        ;['A4'].forEach(addr => {
            if (ws[addr]) ws[addr].s = {
                font: font({ sz: 10, color: { rgb: C.greyText.slice(2) } }),
                fill: fill(C.bgLight),
                alignment: align('left', 'center'),
            }
        })
    if (ws['E4']) ws['E4'].s = {
        font: font({ sz: 9, italic: true, color: { rgb: C.greyText.slice(2) } }),
        fill: fill(C.bgLight),
        alignment: align('right', 'center'),
    }

    // ── Style en-têtes colonnes ───────────────────────────────────────────────
    const HEADER_ROW = 5
        ;['A', 'B', 'C', 'D', 'E', 'F'].forEach(col => {
            const addr = `${col}${HEADER_ROW}`
            if (ws[addr]) ws[addr].s = {
                font: font({ bold: true, sz: 10, color: { rgb: 'FFFFFF' } }),
                fill: fill(C.midBlue),
                alignment: align('center', 'center', true),
                border: {
                    bottom: { style: 'medium', color: { rgb: C.accentGold.slice(2) } },
                },
            }
        })

    // ── Style données ─────────────────────────────────────────────────────────
    data.forEach((_, idx) => {
        const r = HEADER_ROW + 1 + idx
        const alt = idx % 2 === 1
        const bg = alt ? C.bgAlt : C.white

            ;['A', 'B', 'C', 'D', 'E', 'F'].forEach(col => {
                const addr = `${col}${r}`
                if (!ws[addr]) return
                const isProfit = col === 'E'
                const isMarge = col === 'F'
                const isMonth = col === 'A'
                ws[addr].s = {
                    font: font({
                        bold: isMonth || isProfit,
                        color: {
                            rgb: isProfit ? C.green.slice(2)
                                : isMarge ? C.midBlue.slice(2)
                                    : C.darkNavy.slice(2)
                        },
                    }),
                    fill: fill(bg),
                    alignment: align(isMonth ? 'left' : (isMarge ? 'center' : 'right'), 'center'),
                    border: thinBorder(),
                }
            })
    })

    // ── Style ligne total ─────────────────────────────────────────────────────
    const TOTAL_ROW = HEADER_ROW + 1 + data.length
        ;['A', 'B', 'C', 'D', 'E', 'F'].forEach(col => {
            const addr = `${col}${TOTAL_ROW}`
            if (!ws[addr]) return
            ws[addr].s = {
                font: font({ bold: true, sz: 10, color: { rgb: C.accentLight.slice(2) } }),
                fill: fill(C.darkNavy),
                alignment: align(col === 'A' ? 'left' : (col === 'F' ? 'center' : 'right'), 'center'),
                border: {
                    top: { style: 'medium', color: { rgb: C.accentGold.slice(2) } },
                    bottom: { style: 'medium', color: { rgb: C.accentGold.slice(2) } },
                    left: { style: 'thin', color: { rgb: C.darkNavy.slice(2) } },
                    right: { style: 'thin', color: { rgb: C.darkNavy.slice(2) } },
                },
            }
        })

    // ── Freeze panes ──────────────────────────────────────────────────────────
    ws['!freeze'] = { xSplit: 0, ySplit: 5 }

    XLSX.utils.book_append_sheet(wb, ws, 'Rapport Mensuel')
}

// ═══════════════════════════════════════════════════════════════════════════════
// FEUILLE SYNTHÈSE KPI
// ═══════════════════════════════════════════════════════════════════════════════
function buildSummarySheet(wb, data) {
    const totalRevenue = data.reduce((s, i) => s + (parseFloat(i.revenue) || 0), 0)
    const totalExpenses = data.reduce((s, i) => s + (parseFloat(i.expenses) || 0), 0)
    const totalCharges = data.reduce((s, i) => s + (parseFloat(i.charges) || 0), 0)
    const totalProfit = totalRevenue - totalExpenses - totalCharges
    const avgMargin = totalRevenue ? (totalProfit / totalRevenue) : 0
    const bestMonth = data.reduce((best, i) =>
        (parseFloat(i.profit) || 0) > (parseFloat(best.profit) || 0) ? i : best, data[0] || {})

    const rows = [
        [''],
        ['SYNTHÈSE & INDICATEURS CLÉS', '', '', ''],
        [''],
        ['Indicateur', 'Valeur', '', 'Note'],
        ['Revenus totaux', { t: 'n', v: totalRevenue, z: moneyFmt }, '', ''],
        ['Dépenses totales', { t: 'n', v: totalExpenses, z: moneyFmt }, '', ''],
        ['Charges totales', { t: 'n', v: totalCharges, z: moneyFmt }, '', ''],
        ['Bénéfice net', { t: 'n', v: totalProfit, z: moneyFmt }, '', totalProfit >= 0 ? '✓ Positif' : '⚠ Négatif'],
        ['Marge nette moyenne', { t: 'n', v: avgMargin, z: pctFmt }, '', ''],
        ['Meilleur mois', bestMonth?.month || '-', '', ''],
        ['Nombre de mois analysés', data.length, '', ''],
    ]

    const ws = XLSX.utils.aoa_to_sheet(rows)

    ws['!cols'] = [{ wch: 28 }, { wch: 22 }, { wch: 4 }, { wch: 20 }]
    ws['!rows'] = [
        { hpt: 10 }, { hpt: 38 }, { hpt: 8 }, { hpt: 26 },
        ...rows.slice(4).map(() => ({ hpt: 22 })),
    ]

    merge(ws, 'A2', 'D2')

    // Bannière
    if (ws['A2']) ws['A2'].s = {
        font: font({ bold: true, sz: 16, color: { rgb: 'FFFFFF' } }),
        fill: fill(C.darkNavy),
        alignment: align('center', 'center'),
    }

        // Headers tableau
        ;['A4', 'B4', 'D4'].forEach(addr => {
            if (ws[addr]) ws[addr].s = {
                font: font({ bold: true, color: { rgb: 'FFFFFF' } }),
                fill: fill(C.midBlue),
                alignment: align('center', 'center'),
                border: thinBorder(),
            }
        })

    // Données KPI
    const kpiColors = ['', '', '', C.green, '', '', '']
    rows.slice(4).forEach((_, idx) => {
        const r = 5 + idx
            ;['A', 'B'].forEach(col => {
                const addr = `${col}${r}`
                if (!ws[addr]) return
                const isBenefit = idx === 3
                ws[addr].s = {
                    font: font({
                        bold: col === 'A' || isBenefit,
                        color: {
                            rgb: (isBenefit && col === 'B')
                                ? (totalProfit >= 0 ? C.green.slice(2) : C.red.slice(2))
                                : C.darkNavy.slice(2)
                        },
                    }),
                    fill: fill(idx % 2 === 0 ? C.white : C.bgAlt),
                    alignment: align(col === 'A' ? 'left' : 'right', 'center'),
                    border: thinBorder(),
                }
            })
        const noteAddr = `D${r}`
        if (ws[noteAddr] && ws[noteAddr].v) {
            ws[noteAddr].s = {
                font: font({ bold: true, color: { rgb: totalProfit >= 0 ? C.green.slice(2) : C.red.slice(2) } }),
                fill: fill(idx % 2 === 0 ? C.white : C.bgAlt),
                alignment: align('center', 'center'),
                border: thinBorder(),
            }
        }
    })

    XLSX.utils.book_append_sheet(wb, ws, 'Synthèse')
}

// ═══════════════════════════════════════════════════════════════════════════════
// FEUILLE RAPPORT JOURNALIER
// ═══════════════════════════════════════════════════════════════════════════════
function buildDailySheet(wb, data, monthName) {
    const now = new Date()
    const rows = []

    rows.push(new Array(5).fill(''))
    rows.push(['RAPPORT JOURNALIER — ' + (monthName || '').toUpperCase(), '', '', '', ''])
    rows.push(new Array(5).fill(''))
    rows.push(['', '', '', '', `Généré le ${now.toLocaleDateString('fr-FR')}`])
    rows.push(['Jour', 'Revenus (F CFA)', 'Dépenses (F CFA)', 'Bénéfice (F CFA)', 'Marge (%)'])

    const DATA_START = rows.length
    data.forEach(item => {
        rows.push([
            item.day_name,
            { t: 'n', v: parseFloat(item.revenue) || 0, z: moneyFmt },
            { t: 'n', v: parseFloat(item.expenses) || 0, z: moneyFmt },
            { t: 'n', v: parseFloat(item.profit) || 0, z: moneyFmt },
            { t: 'n', v: (parseFloat(item.margin) || 0) / 100, z: pctFmt },
        ])
    })

    const tRev = data.reduce((s, i) => s + (parseFloat(i.revenue) || 0), 0)
    const tExp = data.reduce((s, i) => s + (parseFloat(i.expenses) || 0), 0)
    const tProf = tRev - tExp
    const marg = tRev ? tProf / tRev : 0
    rows.push([
        'TOTAL',
        { t: 'n', v: tRev, z: moneyFmt },
        { t: 'n', v: tExp, z: moneyFmt },
        { t: 'n', v: tProf, z: moneyFmt },
        { t: 'n', v: marg, z: pctFmt },
    ])

    const ws = XLSX.utils.aoa_to_sheet(rows)
    ws['!cols'] = [{ wch: 22 }, { wch: 20 }, { wch: 20 }, { wch: 20 }, { wch: 12 }]
    ws['!rows'] = [
        { hpt: 12 }, { hpt: 40 }, { hpt: 6 }, { hpt: 18 }, { hpt: 30 },
        ...data.map(() => ({ hpt: 20 })),
        { hpt: 26 },
    ]

    merge(ws, 'A2', 'E2')
    merge(ws, 'A3', 'E3')
    merge(ws, 'A4', 'D4')

    // Bannière
    if (ws['A2']) ws['A2'].s = {
        font: font({ bold: true, sz: 18, color: { rgb: 'FFFFFF' } }),
        fill: fill(C.darkNavy),
        alignment: align('center', 'center'),
    }
    if (ws['A3']) ws['A3'].s = { fill: fill(C.accentGold) }
    if (ws['E4']) ws['E4'].s = {
        font: font({ sz: 9, italic: true, color: { rgb: C.greyText.slice(2) } }),
        fill: fill(C.bgLight),
        alignment: align('right', 'center'),
    }

        // En-têtes
        ;['A', 'B', 'C', 'D', 'E'].forEach(col => {
            const addr = `${col}5`
            if (ws[addr]) ws[addr].s = {
                font: font({ bold: true, color: { rgb: 'FFFFFF' } }),
                fill: fill(C.midBlue),
                alignment: align('center', 'center', true),
                border: { bottom: { style: 'medium', color: { rgb: C.accentGold.slice(2) } } },
            }
        })

    // Données
    data.forEach((_, idx) => {
        const r = 6 + idx
        const bg = idx % 2 === 1 ? C.bgAlt : C.white
            ;['A', 'B', 'C', 'D', 'E'].forEach(col => {
                const addr = `${col}${r}`
                if (!ws[addr]) return
                ws[addr].s = {
                    font: font({ bold: col === 'A' || col === 'D' }),
                    fill: fill(bg),
                    alignment: align(col === 'A' ? 'left' : (col === 'E' ? 'center' : 'right'), 'center'),
                    border: thinBorder(),
                }
            })
    })

    // Total row
    const TR = 6 + data.length
        ;['A', 'B', 'C', 'D', 'E'].forEach(col => {
            const addr = `${col}${TR}`
            if (!ws[addr]) return
            ws[addr].s = {
                font: font({ bold: true, color: { rgb: C.accentLight.slice(2) } }),
                fill: fill(C.darkNavy),
                alignment: align(col === 'A' ? 'left' : (col === 'E' ? 'center' : 'right'), 'center'),
                border: {
                    top: { style: 'medium', color: { rgb: C.accentGold.slice(2) } },
                    bottom: { style: 'medium', color: { rgb: C.accentGold.slice(2) } },
                    left: thinBorder().left,
                    right: thinBorder().right,
                },
            }
        })

    ws['!freeze'] = { xSplit: 0, ySplit: 5 }
    XLSX.utils.book_append_sheet(wb, ws, 'Rapport Journalier')
}
