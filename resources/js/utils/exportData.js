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
    greenLight: 'FFD6F0E0',
    red: 'FFC0392B',
    redLight: 'FFFDE8E8',
    greyText: 'FF8899AA',
    greyBg: 'FFF0F3F8',
    amber: 'FFCA6F1E',
    amberLight: 'FFFFF3CD',
    purple: 'FF6B3FA0',
    teal: 'FF0F7B6C',
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
const boldBorder = (color = null) => {
    const c = color || C.accentGold.slice(2)
    return {
        top: { style: 'medium', color: { rgb: c } },
        bottom: { style: 'medium', color: { rgb: c } },
        left: { style: 'thin', color: { rgb: 'BEC7D8' } },
        right: { style: 'thin', color: { rgb: 'BEC7D8' } },
    }
}

const moneyFmt = '#,##0 "F";[Red]-#,##0 "F";"-"'
const pctFmt = '0.0%'
const pctDiff = '[Green]+0.0%;[Red]-0.0%;"—"'

// ─── Encode lettre de colonne depuis index 0-based ────────────────────────────
function colLetter(idx) {
    let result = ''
    let n = idx
    while (n >= 0) {
        result = String.fromCharCode(65 + (n % 26)) + result
        n = Math.floor(n / 26) - 1
    }
    return result
}

// ─── Formate un mois 'YYYY-MM' en label français ──────────────────────────────
function fmtMonth(ym) {
    if (!ym) return ''
    const [y, m] = ym.split('-')
    const months = [
        'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
        'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre',
    ]
    return `${months[parseInt(m, 10) - 1]} ${y}`
}

// ─── Fusionne des cellules ────────────────────────────────────────────────────
function merge(ws, from, to) {
    if (!ws['!merges']) ws['!merges'] = []
    const s = XLSX.utils.decode_cell(from)
    const e = XLSX.utils.decode_cell(to)
    ws['!merges'].push({ s, e })
}

// ════════════════════════════════════════════════════════════════════════════════
//  EXPORT PRINCIPAL — point d'entrée
// ════════════════════════════════════════════════════════════════════════════════
/**
 * @param {Object} options
 * @param {Array}        options.monthlyData    Données mensuelles plates
 * @param {Array}        options.apartments     Liste [{id, name}, ...]
 * @param {Boolean}      options.isDaily        Mode journalier
 * @param {Array}        options.dailyData      Données journalières si isDaily
 * @param {String}       options.monthName      Nom du mois si isDaily
 * @param {String|null}  options.apartmentId    ID appartement filtré (null = tous)
 */
export const exportData = ({
    monthlyData = [],
    apartments = [],
    isDaily = false,
    dailyData = [],
    monthName = '',
    apartmentId = null,
} = {}) => {
    const wb = XLSX.utils.book_new()
    const now = new Date()

    if (isDaily) {
        // Mode journalier : comportement inchangé
        buildDailySheet(wb, dailyData, monthName)
    } else if (apartmentId) {
        // Un seul appartement filtré : ancienne logique classique
        buildMonthlySheet(wb, monthlyData)
        buildSummarySheet(wb, monthlyData)
    } else {
        // Tous les appartements : NOUVELLE LOGIQUE
        buildMultiApartmentReport(wb, monthlyData, apartments)
    }

    const base = isDaily
        ? `rapport-journalier-${(monthName || '').replace(/\s+/g, '-').toLowerCase()}`
        : `rapport-mensuel-${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`

    XLSX.writeFile(wb, `${base}.xlsx`)
}

// ════════════════════════════════════════════════════════════════════════════════
//  RAPPORT MULTI-APPARTEMENTS
//  Génère :
//    1. Feuille "Vue d'ensemble" — 3 tableaux mensuels + tableau évolution
//    2. Une feuille de détail par appartement
// ════════════════════════════════════════════════════════════════════════════════
function buildMultiApartmentReport(wb, monthlyData, apartments) {
    // Identifier les 3 derniers mois présents dans les données
    const allMonths = [...new Set(monthlyData.map(d => d.month))].sort()
    const last3 = allMonths.slice(-3)

    // Construire un index : mois → apartment_id → données
    const idx = {}
    for (const row of monthlyData) {
        const m = row.month
        const id = String(row.apartment_id ?? 'all')
        if (!idx[m]) idx[m] = {}
        idx[m][id] = row
    }

    // Feuille principale Vue d'ensemble
    buildOverviewSheet(wb, last3, apartments, idx)

    // Une feuille de détail par appartement
    for (const apt of apartments) {
        const aptData = monthlyData
            .filter(d => String(d.apartment_id) === String(apt.id))
            .sort((a, b) => a.month.localeCompare(b.month))
        buildApartmentDetailSheet(wb, apt, aptData)
    }
}

// ════════════════════════════════════════════════════════════════════════════════
//  FEUILLE VUE D'ENSEMBLE
//  Structure (toujours dans la même feuille) :
//    Bannière principale
//    Pour chaque mois (jusqu'à 3) :
//      → Titre du mois
//      → Tableau : une ligne par appartement + ligne total
//    Séparateur
//    Tableau d'évolution comparatif inter-mois par appartement
// ════════════════════════════════════════════════════════════════════════════════
function buildOverviewSheet(wb, last3, apartments, idx) {
    const now = new Date()
    const COLS = 7   // Appartement | Revenus | Dépenses | Charges | Bénéfice | Marge | Statut

    const rows = []
    const metas = []   // positions pour le style

    // ── Ligne vide + Bannière principale ─────────────────────────────────────
    rows.push(new Array(COLS).fill(''))   // R1 vide
    rows.push([                            // R2 bannière
        'RAPPORT FINANCIER — VUE D\'ENSEMBLE MULTI-APPARTEMENTS',
        ...new Array(COLS - 1).fill(''),
    ])
    rows.push(new Array(COLS).fill(''))   // R3 barre or
    rows.push([                            // R4 sous-titre
        `Période analysée : ${last3.map(fmtMonth).join(' · ')}`,
        ...new Array(COLS - 3).fill(''),
        '',
        `Généré le ${now.toLocaleDateString('fr-FR')}`,
        '',
    ])
    rows.push(new Array(COLS).fill(''))   // R5 espace

    const HEADER_LABELS = [
        'Appartement',
        'Revenus (F CFA)',
        'Dépenses (F CFA)',
        'Charges (F CFA)',
        'Bénéfice (F CFA)',
        'Marge (%)',
        'Statut',
    ]

    // ── Un tableau par mois ───────────────────────────────────────────────────
    for (const month of last3) {
        rows.push(new Array(COLS).fill(''))           // ligne espace

        const monthTitleRowIdx = rows.length          // 0-based dans rows[]
        rows.push([
            `📅  ${fmtMonth(month).toUpperCase()}`,
            ...new Array(COLS - 1).fill(''),
        ])

        const headerRowIdx = rows.length
        rows.push(HEADER_LABELS.slice())

        const dataStartIdx = rows.length
        let totalRev = 0, totalExp = 0, totalChg = 0

        for (const apt of apartments) {
            const d = idx[month]?.[String(apt.id)] || {}
            const rev = parseFloat(d.revenue) || 0
            const exp = parseFloat(d.expenses) || 0
            const chg = parseFloat(d.charges) || 0
            const prf = parseFloat(d.profit) ?? (rev - exp - chg)
            const mrg = rev > 0 ? prf / rev : 0
            totalRev += rev
            totalExp += exp
            totalChg += chg

            rows.push([
                apt.name,
                { t: 'n', v: rev, z: moneyFmt },
                { t: 'n', v: exp, z: moneyFmt },
                { t: 'n', v: chg, z: moneyFmt },
                { t: 'n', v: prf, z: moneyFmt },
                { t: 'n', v: mrg, z: pctFmt },
                prf >= 0 ? '✅  Bénéficiaire' : '⚠️  Déficitaire',
            ])
        }

        const totalPrf = totalRev - totalExp - totalChg
        const totalMrg = totalRev > 0 ? totalPrf / totalRev : 0
        const totalRowIdx = rows.length

        rows.push([
            'TOTAL DU MOIS',
            { t: 'n', v: totalRev, z: moneyFmt },
            { t: 'n', v: totalExp, z: moneyFmt },
            { t: 'n', v: totalChg, z: moneyFmt },
            { t: 'n', v: totalPrf, z: moneyFmt },
            { t: 'n', v: totalMrg, z: pctFmt },
            '',
        ])

        // mémoriser positions (1-based pour Excel)
        metas.push({
            month,
            monthTitleRow: monthTitleRowIdx + 1,
            headerRow: headerRowIdx + 1,
            dataStartRow: dataStartIdx + 1,
            dataEndRow: totalRowIdx,               // dernier data row (avant total)
            totalRow: totalRowIdx + 1,
        })
    }

    // ── Séparateur avant tableau évolution ────────────────────────────────────
    rows.push(new Array(COLS).fill(''))
    rows.push(new Array(COLS).fill(''))

    // ── Tableau d'évolution ───────────────────────────────────────────────────
    // Colonnes : Appartement | [Rev M1, Profit M1] | [Rev M2, Profit M2] | ... | Δ Rev M1→M2 | Δ Profit M1→M2 | ...
    const nbMonths = last3.length
    const nbDeltas = nbMonths - 1
    const EVO_COLS = 1 + nbMonths * 2 + nbDeltas * 2

    const evoTitleRowIdx = rows.length
    rows.push([
        '📊  ANALYSE D\'ÉVOLUTION PAR APPARTEMENT',
        ...new Array(Math.max(EVO_COLS - 1, COLS - 1)).fill(''),
    ])

    // Ligne d'en-tête niveau 1 (groupes)
    const evoH1 = ['Appartement']
    for (const m of last3) {
        evoH1.push(fmtMonth(m), '')
    }
    for (let i = 0; i < nbDeltas; i++) {
        const label = `Évolution ${fmtMonth(last3[i]).substring(0, 3)} → ${fmtMonth(last3[i + 1]).substring(0, 3)}`
        evoH1.push(label, '')
    }

    // Ligne d'en-tête niveau 2 (sous-colonnes)
    const evoH2 = ['']
    for (let i = 0; i < nbMonths; i++) {
        evoH2.push('Revenus', 'Bénéfice')
    }
    for (let i = 0; i < nbDeltas; i++) {
        evoH2.push('Δ Revenus', 'Δ Bénéfice')
    }

    const evoH1RowIdx = rows.length
    rows.push(evoH1)
    const evoH2RowIdx = rows.length
    rows.push(evoH2)

    const evoDataStartIdx = rows.length

    for (const apt of apartments) {
        const row = [apt.name]
        const revByMonth = []
        const profByMonth = []

        for (const m of last3) {
            const d = idx[m]?.[String(apt.id)] || {}
            const rev = parseFloat(d.revenue) || 0
            const prf = parseFloat(d.profit) || 0
            revByMonth.push(rev)
            profByMonth.push(prf)
            row.push(
                { t: 'n', v: rev, z: moneyFmt },
                { t: 'n', v: prf, z: moneyFmt },
            )
        }

        for (let i = 0; i < nbDeltas; i++) {
            const dRev = revByMonth[i] > 0
                ? (revByMonth[i + 1] - revByMonth[i]) / revByMonth[i]
                : 0
            const dProf = profByMonth[i] !== 0
                ? (profByMonth[i + 1] - profByMonth[i]) / Math.abs(profByMonth[i])
                : 0
            row.push(
                { t: 'n', v: dRev, z: pctDiff },
                { t: 'n', v: dProf, z: pctDiff },
            )
        }

        rows.push(row)
    }

    // ── Construire la feuille ─────────────────────────────────────────────────
    const ws = XLSX.utils.aoa_to_sheet(rows)

    // ── Largeurs de colonnes ──────────────────────────────────────────────────
    ws['!cols'] = [
        { wch: 26 }, { wch: 20 }, { wch: 20 }, { wch: 20 },
        { wch: 20 }, { wch: 12 }, { wch: 18 },
    ]

    // ── Hauteurs ──────────────────────────────────────────────────────────────
    ws['!rows'] = [
        { hpt: 10 },  // R1 vide
        { hpt: 38 },  // R2 bannière
        { hpt: 6 },  // R3 barre or
        { hpt: 18 },  // R4 sous-titre
        { hpt: 10 },  // R5 espace
    ]

    // ── Style bannière ────────────────────────────────────────────────────────
    merge(ws, `A2`, `${colLetter(COLS - 1)}2`)
    merge(ws, `A3`, `${colLetter(COLS - 1)}3`)
    merge(ws, `A4`, `${colLetter(COLS - 3)}4`)
    merge(ws, `${colLetter(COLS - 2)}4`, `${colLetter(COLS - 1)}4`)

    if (ws['A2']) ws['A2'].s = {
        font: font({ bold: true, sz: 16, color: { rgb: 'FFFFFF' } }),
        fill: fill(C.darkNavy),
        alignment: align('center', 'center'),
    }
    if (ws['A3']) ws['A3'].s = { fill: fill(C.accentGold) }
    if (ws['A4']) ws['A4'].s = {
        font: font({ sz: 10, color: { rgb: C.greyText.slice(2) } }),
        fill: fill(C.bgLight),
        alignment: align('left', 'center'),
    }
    const dateAddr = `${colLetter(COLS - 2)}4`
    if (ws[dateAddr]) ws[dateAddr].s = {
        font: font({ sz: 9, italic: true, color: { rgb: C.greyText.slice(2) } }),
        fill: fill(C.bgLight),
        alignment: align('right', 'center'),
    }

    // ── Styler chaque tableau mensuel ─────────────────────────────────────────
    for (const meta of metas) {
        const { monthTitleRow, headerRow, dataStartRow, dataEndRow, totalRow } = meta

        // Fusion + style titre mois
        merge(ws, `A${monthTitleRow}`, `${colLetter(COLS - 1)}${monthTitleRow}`)
        const mCell = `A${monthTitleRow}`
        if (ws[mCell]) ws[mCell].s = {
            font: font({ bold: true, sz: 12, color: { rgb: 'FFFFFF' } }),
            fill: fill(C.midBlue),
            alignment: align('left', 'center'),
        }

        // En-têtes colonnes
        for (let ci = 0; ci < COLS; ci++) {
            const addr = `${colLetter(ci)}${headerRow}`
            if (ws[addr]) ws[addr].s = {
                font: font({ bold: true, sz: 10, color: { rgb: 'FFFFFF' } }),
                fill: fill(C.darkNavy),
                alignment: align('center', 'center', true),
                border: {
                    bottom: { style: 'medium', color: { rgb: C.accentGold.slice(2) } },
                    top: { style: 'thin', color: { rgb: 'BEC7D8' } },
                    left: { style: 'thin', color: { rgb: 'BEC7D8' } },
                    right: { style: 'thin', color: { rgb: 'BEC7D8' } },
                },
            }
        }

        // Lignes de données appartements
        for (let r = dataStartRow; r <= dataEndRow; r++) {
            const alt = (r - dataStartRow) % 2 === 1
            const bg = alt ? C.bgAlt : C.white
            const profCell = ws[`${colLetter(4)}${r}`]
            const profVal = profCell ? (profCell.v ?? 0) : 0

            for (let ci = 0; ci < COLS; ci++) {
                const addr = `${colLetter(ci)}${r}`
                if (!ws[addr]) continue

                const isApt = ci === 0
                const isProfit = ci === 4
                const isMarge = ci === 5
                const isStatus = ci === 6

                let textColor = C.darkNavy.slice(2)
                if (isProfit) textColor = profVal >= 0 ? C.green.slice(2) : C.red.slice(2)
                if (isMarge) textColor = C.midBlue.slice(2)
                if (isStatus) textColor = profVal >= 0 ? C.green.slice(2) : C.amber.slice(2)

                ws[addr].s = {
                    font: font({
                        bold: isApt || isProfit,
                        color: { rgb: textColor },
                    }),
                    fill: fill(bg),
                    alignment: align(
                        isApt || isStatus ? 'left' : isMarge ? 'center' : 'right',
                        'center'
                    ),
                    border: thinBorder(),
                }
            }
        }

        // Ligne TOTAL
        for (let ci = 0; ci < COLS; ci++) {
            const addr = `${colLetter(ci)}${totalRow}`
            if (!ws[addr]) continue
            ws[addr].s = {
                font: font({ bold: true, sz: 10, color: { rgb: C.accentLight.slice(2) } }),
                fill: fill(C.darkNavy),
                alignment: align(ci === 0 ? 'left' : ci === 5 ? 'center' : 'right', 'center'),
                border: boldBorder(),
            }
        }
    }

    // ── Style titre tableau évolution ─────────────────────────────────────────
    const evoTR = evoTitleRowIdx + 1
    merge(ws, `A${evoTR}`, `${colLetter(Math.max(EVO_COLS - 1, COLS - 1))}${evoTR}`)
    if (ws[`A${evoTR}`]) ws[`A${evoTR}`].s = {
        font: font({ bold: true, sz: 13, color: { rgb: 'FFFFFF' } }),
        fill: fill(C.darkNavy),
        alignment: align('left', 'center'),
    }

    // ── Style en-têtes évolution (2 niveaux) ──────────────────────────────────
    const evoH1R = evoH1RowIdx + 1
    const evoH2R = evoH2RowIdx + 1

    // Fusions groupes de colonnes en-tête niveau 1
    let evoCI = 1
    for (let i = 0; i < nbMonths; i++) {
        merge(ws, `${colLetter(evoCI)}${evoH1R}`, `${colLetter(evoCI + 1)}${evoH1R}`)
        evoCI += 2
    }
    for (let i = 0; i < nbDeltas; i++) {
        merge(ws, `${colLetter(evoCI)}${evoH1R}`, `${colLetter(evoCI + 1)}${evoH1R}`)
        evoCI += 2
    }

    // Couleurs par section
    const sectionFills = []
    sectionFills.push(C.darkNavy) // colonne Appartement
    for (let i = 0; i < nbMonths; i++) {
        sectionFills.push(C.midBlue, C.midBlue)
    }
    for (let i = 0; i < nbDeltas; i++) {
        sectionFills.push(C.teal, C.teal)
    }

    for (let ci = 0; ci < EVO_COLS; ci++) {
        for (const r of [evoH1R, evoH2R]) {
            const addr = `${colLetter(ci)}${r}`
            if (!ws[addr]) continue
            ws[addr].s = {
                font: font({ bold: true, sz: 10, color: { rgb: 'FFFFFF' } }),
                fill: fill(sectionFills[ci] || C.midBlue),
                alignment: align('center', 'center', true),
                border: thinBorder(),
            }
        }
    }

    // Style cellule Appartement H1/H2
    if (ws[`A${evoH1R}`]) ws[`A${evoH1R}`].s = {
        ...ws[`A${evoH1R}`]?.s,
        font: font({ bold: true, color: { rgb: 'FFFFFF' } }),
        fill: fill(C.darkNavy),
        alignment: align('left', 'center'),
    }
    if (ws[`A${evoH2R}`]) ws[`A${evoH2R}`].s = {
        ...ws[`A${evoH2R}`]?.s,
        font: font({ bold: true, color: { rgb: 'FFFFFF' } }),
        fill: fill(C.darkNavy),
        alignment: align('left', 'center'),
    }

    // ── Style données évolution ───────────────────────────────────────────────
    apartments.forEach((apt, aptIdx) => {
        const r = evoDataStartIdx + 1 + aptIdx
        const bg = aptIdx % 2 === 0 ? C.white : C.bgAlt

        for (let ci = 0; ci < EVO_COLS; ci++) {
            const addr = `${colLetter(ci)}${r}`
            if (!ws[addr]) continue

            const isDelta = ci >= 1 + nbMonths * 2
            const isRevenue = !isDelta && ci > 0 && (ci - 1) % 2 === 0
            const isProfit = !isDelta && ci > 0 && (ci - 1) % 2 === 1
            const cellVal = ws[addr].v

            let textColor = C.darkNavy.slice(2)
            if (isDelta && typeof cellVal === 'number') {
                textColor = cellVal > 0.001 ? C.green.slice(2)
                    : cellVal < -0.001 ? C.red.slice(2)
                        : C.greyText.slice(2)
            }
            if (isProfit && typeof cellVal === 'number') {
                textColor = cellVal >= 0 ? C.green.slice(2) : C.red.slice(2)
            }

            ws[addr].s = {
                font: font({
                    bold: ci === 0 || isDelta,
                    color: { rgb: textColor },
                }),
                fill: fill(bg),
                alignment: align(ci === 0 ? 'left' : 'right', 'center'),
                border: thinBorder(),
            }
        }
    })

    // ── Freeze panes ──────────────────────────────────────────────────────────
    ws['!freeze'] = { xSplit: 0, ySplit: 5 }

    XLSX.utils.book_append_sheet(wb, ws, 'Vue d\'ensemble')
}

// ════════════════════════════════════════════════════════════════════════════════
//  FEUILLE DÉTAIL PAR APPARTEMENT (historique complet)
// ════════════════════════════════════════════════════════════════════════════════
function buildApartmentDetailSheet(wb, apt, data) {
    const now = new Date()
    const COLS = 6
    const rows = []

    rows.push(new Array(COLS).fill(''))
    rows.push([apt.name.toUpperCase(), ...new Array(COLS - 1).fill('')])
    rows.push(new Array(COLS).fill(''))
    rows.push([
        `Historique complet · ${data.length} mois analysés`,
        ...new Array(COLS - 3).fill(''),
        '',
        `Généré le ${now.toLocaleDateString('fr-FR')}`,
        '',
    ])
    rows.push([
        'Mois',
        'Revenus (F CFA)',
        'Dépenses (F CFA)',
        'Charges (F CFA)',
        'Bénéfice (F CFA)',
        'Marge (%)',
    ])

    const DATA_START_IDX = rows.length
    for (const item of data) {
        rows.push([
            fmtMonth(item.month),
            { t: 'n', v: parseFloat(item.revenue) || 0, z: moneyFmt },
            { t: 'n', v: parseFloat(item.expenses) || 0, z: moneyFmt },
            { t: 'n', v: parseFloat(item.charges) || 0, z: moneyFmt },
            { t: 'n', v: parseFloat(item.profit) || 0, z: moneyFmt },
            { t: 'n', v: (parseFloat(item.margin) || 0) / 100, z: pctFmt },
        ])
    }

    const tRev = data.reduce((s, i) => s + (parseFloat(i.revenue) || 0), 0)
    const tExp = data.reduce((s, i) => s + (parseFloat(i.expenses) || 0), 0)
    const tChg = data.reduce((s, i) => s + (parseFloat(i.charges) || 0), 0)
    const tPrf = tRev - tExp - tChg
    const tMrg = tRev > 0 ? tPrf / tRev : 0

    rows.push([
        'TOTAL',
        { t: 'n', v: tRev, z: moneyFmt },
        { t: 'n', v: tExp, z: moneyFmt },
        { t: 'n', v: tChg, z: moneyFmt },
        { t: 'n', v: tPrf, z: moneyFmt },
        { t: 'n', v: tMrg, z: pctFmt },
    ])

    const ws = XLSX.utils.aoa_to_sheet(rows)

    ws['!cols'] = [
        { wch: 20 }, { wch: 20 }, { wch: 20 },
        { wch: 20 }, { wch: 20 }, { wch: 12 },
    ]
    ws['!rows'] = [
        { hpt: 12 }, { hpt: 38 }, { hpt: 6 }, { hpt: 18 }, { hpt: 28 },
        ...data.map(() => ({ hpt: 20 })),
        { hpt: 26 },
    ]

    merge(ws, 'A2', `${colLetter(COLS - 1)}2`)
    merge(ws, 'A3', `${colLetter(COLS - 1)}3`)
    merge(ws, 'A4', `${colLetter(COLS - 3)}4`)
    merge(ws, `${colLetter(COLS - 2)}4`, `${colLetter(COLS - 1)}4`)

    if (ws['A2']) ws['A2'].s = {
        font: font({ bold: true, sz: 15, color: { rgb: 'FFFFFF' } }),
        fill: fill(C.midBlue),
        alignment: align('center', 'center'),
    }
    if (ws['A3']) ws['A3'].s = { fill: fill(C.accentGold) }
    if (ws['A4']) ws['A4'].s = {
        font: font({ sz: 10, color: { rgb: C.greyText.slice(2) } }),
        fill: fill(C.bgLight),
        alignment: align('left', 'center'),
    }
    const dCell = `${colLetter(COLS - 2)}4`
    if (ws[dCell]) ws[dCell].s = {
        font: font({ sz: 9, italic: true, color: { rgb: C.greyText.slice(2) } }),
        fill: fill(C.bgLight),
        alignment: align('right', 'center'),
    }

    // En-têtes
    const HROW = 5
    for (let ci = 0; ci < COLS; ci++) {
        const addr = `${colLetter(ci)}${HROW}`
        if (ws[addr]) ws[addr].s = {
            font: font({ bold: true, sz: 10, color: { rgb: 'FFFFFF' } }),
            fill: fill(C.darkNavy),
            alignment: align('center', 'center', true),
            border: { bottom: { style: 'medium', color: { rgb: C.accentGold.slice(2) } } },
        }
    }

    // Données
    data.forEach((_, i) => {
        const r = HROW + 1 + i
        const bg = i % 2 === 0 ? C.white : C.bgAlt
        const profCell = ws[`${colLetter(4)}${r}`]
        const profVal = profCell ? (profCell.v ?? 0) : 0

        for (let ci = 0; ci < COLS; ci++) {
            const addr = `${colLetter(ci)}${r}`
            if (!ws[addr]) continue
            ws[addr].s = {
                font: font({
                    bold: ci === 0 || ci === 4,
                    color: {
                        rgb: ci === 4
                            ? (profVal >= 0 ? C.green.slice(2) : C.red.slice(2))
                            : ci === 5
                                ? C.midBlue.slice(2)
                                : C.darkNavy.slice(2),
                    },
                }),
                fill: fill(bg),
                alignment: align(ci === 0 ? 'left' : ci === 5 ? 'center' : 'right', 'center'),
                border: thinBorder(),
            }
        }
    })

    // Total
    const TR = HROW + 1 + data.length
    for (let ci = 0; ci < COLS; ci++) {
        const addr = `${colLetter(ci)}${TR}`
        if (!ws[addr]) continue
        ws[addr].s = {
            font: font({ bold: true, sz: 10, color: { rgb: C.accentLight.slice(2) } }),
            fill: fill(C.darkNavy),
            alignment: align(ci === 0 ? 'left' : ci === 5 ? 'center' : 'right', 'center'),
            border: boldBorder(),
        }
    }

    ws['!freeze'] = { xSplit: 0, ySplit: 5 }

    // Nom de feuille max 31 chars (limite Excel)
    const sheetName = apt.name.replace(/[:\\/?*\[\]]/g, '').slice(0, 31)
    XLSX.utils.book_append_sheet(wb, ws, sheetName)
}

// ════════════════════════════════════════════════════════════════════════════════
//  FEUILLE RAPPORT MENSUEL — mode un seul appartement (inchangée)
// ════════════════════════════════════════════════════════════════════════════════
function buildMonthlySheet(wb, data) {
    const now = new Date()
    const COL_COUNT = 6
    const rows = []

    rows.push(new Array(COL_COUNT).fill(''))
    rows.push(['RAPPORT FINANCIER MENSUEL', '', '', '', '', ''])
    rows.push(new Array(COL_COUNT).fill(''))
    rows.push([`Exercice ${now.getFullYear()}`, '', '', '', `Généré le ${now.toLocaleDateString('fr-FR')}`, ''])
    rows.push(['Mois', 'Revenus (F CFA)', 'Dépenses (F CFA)', 'Charges (F CFA)', 'Bénéfice (F CFA)', 'Marge (%)'])

    const DATA_START_IDX = rows.length
    data.forEach(item => {
        rows.push([
            fmtMonth(item.month),
            { t: 'n', v: parseFloat(item.revenue) || 0, z: moneyFmt },
            { t: 'n', v: parseFloat(item.expenses) || 0, z: moneyFmt },
            { t: 'n', v: parseFloat(item.charges) || 0, z: moneyFmt },
            { t: 'n', v: parseFloat(item.profit) || 0, z: moneyFmt },
            { t: 'n', v: (parseFloat(item.margin) || 0) / 100, z: pctFmt },
        ])
    })

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

    const ws = XLSX.utils.aoa_to_sheet(rows)
    ws['!cols'] = [{ wch: 18 }, { wch: 20 }, { wch: 20 }, { wch: 20 }, { wch: 20 }, { wch: 12 }]
    ws['!rows'] = [
        { hpt: 12 }, { hpt: 40 }, { hpt: 6 }, { hpt: 18 }, { hpt: 30 },
        ...data.map(() => ({ hpt: 20 })),
        { hpt: 26 },
    ]

    merge(ws, 'A2', 'F2')
    merge(ws, 'A3', 'F3')
    merge(ws, 'A4', 'D4')
    merge(ws, 'E4', 'F4')

    if (ws['A2']) ws['A2'].s = {
        font: font({ bold: true, sz: 18, color: { rgb: 'FFFFFF' } }),
        fill: fill(C.darkNavy),
        alignment: align('center', 'center'),
    }
    if (ws['A3']) ws['A3'].s = { fill: fill(C.accentGold) }
    if (ws['A4']) ws['A4'].s = {
        font: font({ sz: 10, color: { rgb: C.greyText.slice(2) } }),
        fill: fill(C.bgLight),
        alignment: align('left', 'center'),
    }
    if (ws['E4']) ws['E4'].s = {
        font: font({ sz: 9, italic: true, color: { rgb: C.greyText.slice(2) } }),
        fill: fill(C.bgLight),
        alignment: align('right', 'center'),
    }

    const HEADER_ROW = 5
    for (let ci = 0; ci < COL_COUNT; ci++) {
        const addr = `${colLetter(ci)}${HEADER_ROW}`
        if (ws[addr]) ws[addr].s = {
            font: font({ bold: true, sz: 10, color: { rgb: 'FFFFFF' } }),
            fill: fill(C.midBlue),
            alignment: align('center', 'center', true),
            border: { bottom: { style: 'medium', color: { rgb: C.accentGold.slice(2) } } },
        }
    }

    data.forEach((_, idx) => {
        const r = HEADER_ROW + 1 + idx
        const bg = idx % 2 === 1 ? C.bgAlt : C.white
        const profCell = ws[`${colLetter(4)}${r}`]
        const profVal = profCell ? (profCell.v ?? 0) : 0

        for (let ci = 0; ci < COL_COUNT; ci++) {
            const addr = `${colLetter(ci)}${r}`
            if (!ws[addr]) continue
            ws[addr].s = {
                font: font({
                    bold: ci === 0 || ci === 4,
                    color: {
                        rgb: ci === 4
                            ? C.green.slice(2)
                            : ci === 5
                                ? C.midBlue.slice(2)
                                : C.darkNavy.slice(2),
                    },
                }),
                fill: fill(bg),
                alignment: align(ci === 0 ? 'left' : ci === 5 ? 'center' : 'right', 'center'),
                border: thinBorder(),
            }
        }
    })

    const TOTAL_ROW = HEADER_ROW + 1 + data.length
    for (let ci = 0; ci < COL_COUNT; ci++) {
        const addr = `${colLetter(ci)}${TOTAL_ROW}`
        if (!ws[addr]) continue
        ws[addr].s = {
            font: font({ bold: true, sz: 10, color: { rgb: C.accentLight.slice(2) } }),
            fill: fill(C.darkNavy),
            alignment: align(ci === 0 ? 'left' : ci === 5 ? 'center' : 'right', 'center'),
            border: boldBorder(),
        }
    }

    ws['!freeze'] = { xSplit: 0, ySplit: 5 }
    XLSX.utils.book_append_sheet(wb, ws, 'Rapport Mensuel')
}

// ════════════════════════════════════════════════════════════════════════════════
//  FEUILLE SYNTHÈSE KPI — mode un seul appartement (inchangée)
// ════════════════════════════════════════════════════════════════════════════════
function buildSummarySheet(wb, data) {
    const totalRevenue = data.reduce((s, i) => s + (parseFloat(i.revenue) || 0), 0)
    const totalExpenses = data.reduce((s, i) => s + (parseFloat(i.expenses) || 0), 0)
    const totalCharges = data.reduce((s, i) => s + (parseFloat(i.charges) || 0), 0)
    const totalProfit = totalRevenue - totalExpenses - totalCharges
    const avgMargin = totalRevenue ? totalProfit / totalRevenue : 0
    const bestMonth = data.reduce(
        (best, i) => (parseFloat(i.profit) || 0) > (parseFloat(best.profit) || 0) ? i : best,
        data[0] || {}
    )

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
        ['Meilleur mois', fmtMonth(bestMonth?.month) || '-', '', ''],
        ['Nombre de mois analysés', data.length, '', ''],
    ]

    const ws = XLSX.utils.aoa_to_sheet(rows)
    ws['!cols'] = [{ wch: 28 }, { wch: 22 }, { wch: 4 }, { wch: 20 }]
    ws['!rows'] = [
        { hpt: 10 }, { hpt: 38 }, { hpt: 8 }, { hpt: 26 },
        ...rows.slice(4).map(() => ({ hpt: 22 })),
    ]

    merge(ws, 'A2', 'D2')

    if (ws['A2']) ws['A2'].s = {
        font: font({ bold: true, sz: 16, color: { rgb: 'FFFFFF' } }),
        fill: fill(C.darkNavy),
        alignment: align('center', 'center'),
    }

    for (const addr of ['A4', 'B4', 'D4']) {
        if (ws[addr]) ws[addr].s = {
            font: font({ bold: true, color: { rgb: 'FFFFFF' } }),
            fill: fill(C.midBlue),
            alignment: align('center', 'center'),
            border: thinBorder(),
        }
    }

    rows.slice(4).forEach((_, idx) => {
        const r = 5 + idx
        const isBenefit = idx === 3
        const profVal = ws['B8']?.v ?? 0

        for (const c of ['A', 'B']) {
            const addr = `${c}${r}`
            if (!ws[addr]) continue
            ws[addr].s = {
                font: font({
                    bold: c === 'A' || isBenefit,
                    color: {
                        rgb: isBenefit && c === 'B'
                            ? (totalProfit >= 0 ? C.green.slice(2) : C.red.slice(2))
                            : C.darkNavy.slice(2),
                    },
                }),
                fill: fill(idx % 2 === 0 ? C.white : C.bgAlt),
                alignment: align(c === 'A' ? 'left' : 'right', 'center'),
                border: thinBorder(),
            }
        }

        const noteAddr = `D${r}`
        if (ws[noteAddr] && ws[noteAddr].v) {
            ws[noteAddr].s = {
                font: font({
                    bold: true,
                    color: { rgb: totalProfit >= 0 ? C.green.slice(2) : C.red.slice(2) },
                }),
                fill: fill(idx % 2 === 0 ? C.white : C.bgAlt),
                alignment: align('center', 'center'),
                border: thinBorder(),
            }
        }
    })

    XLSX.utils.book_append_sheet(wb, ws, 'Synthèse')
}

// ════════════════════════════════════════════════════════════════════════════════
//  FEUILLE RAPPORT JOURNALIER (inchangée)
// ════════════════════════════════════════════════════════════════════════════════
function buildDailySheet(wb, data, monthName) {
    const now = new Date()
    const rows = []

    rows.push(new Array(5).fill(''))
    rows.push(['RAPPORT JOURNALIER — ' + (monthName || '').toUpperCase(), '', '', '', ''])
    rows.push(new Array(5).fill(''))
    rows.push(['', '', '', '', `Généré le ${now.toLocaleDateString('fr-FR')}`])
    rows.push(['Jour', 'Revenus (F CFA)', 'Dépenses (F CFA)', 'Bénéfice (F CFA)', 'Marge (%)'])

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

    for (let ci = 0; ci < 5; ci++) {
        const addr = `${colLetter(ci)}5`
        if (ws[addr]) ws[addr].s = {
            font: font({ bold: true, color: { rgb: 'FFFFFF' } }),
            fill: fill(C.midBlue),
            alignment: align('center', 'center', true),
            border: { bottom: { style: 'medium', color: { rgb: C.accentGold.slice(2) } } },
        }
    }

    data.forEach((_, idx) => {
        const r = 6 + idx
        const bg = idx % 2 === 1 ? C.bgAlt : C.white
        for (let ci = 0; ci < 5; ci++) {
            const addr = `${colLetter(ci)}${r}`
            if (!ws[addr]) continue
            ws[addr].s = {
                font: font({ bold: ci === 0 || ci === 3 }),
                fill: fill(bg),
                alignment: align(ci === 0 ? 'left' : ci === 4 ? 'center' : 'right', 'center'),
                border: thinBorder(),
            }
        }
    })

    const TR = 6 + data.length
    for (let ci = 0; ci < 5; ci++) {
        const addr = `${colLetter(ci)}${TR}`
        if (!ws[addr]) continue
        ws[addr].s = {
            font: font({ bold: true, color: { rgb: C.accentLight.slice(2) } }),
            fill: fill(C.darkNavy),
            alignment: align(ci === 0 ? 'left' : ci === 4 ? 'center' : 'right', 'center'),
            border: boldBorder(),
        }
    }

    ws['!freeze'] = { xSplit: 0, ySplit: 5 }
    XLSX.utils.book_append_sheet(wb, ws, 'Rapport Journalier')
}
