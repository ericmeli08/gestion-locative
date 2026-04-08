<?php

namespace App\Http\Requests;

use App\Models\Reservation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class StoreReservationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'client' => 'required|string|max:255',
            'plateforme' => 'required|string|in:airbnb,booking,direct',
            'appartement_id' => 'required|exists:appartements,id',
            'prix_nuit' => 'required|numeric|min:0',
            'date_entree' => 'required|date',
            'date_sortie' => 'required|date|after:date_entree',
            'statut_paiement' => 'required|in:paid,unpaid',
            'date_paiement' => 'nullable|date|required_if:statut_paiement,paid',
        ];
    }

        private function parseDate($value)
{
    $formats = [
        'Y-m-d H:i',
        'Y-m-d\TH:i',
    ];

    foreach ($formats as $format) {
        try {
            return Carbon::createFromFormat($format, $value);
        } catch (\Exception $e) {
            // On ignore et on teste le format suivant
        }
    }

    throw new \Exception("Format de date invalide : {$value}");
}


    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $appartementId = $this->input('appartement_id');
           $start = $this->parseDate($this->input('date_entree'));
        $end   = $this->parseDate($this->input('date_sortie'));
            if (!$appartementId || !$start || !$end) {
                return;
            }

            $conflict = Reservation::where('appartement_id', $appartementId)
            ->where('date_entree', '<', $end)
            ->where('date_sortie', '>', $start)
            ->first();

            if ($conflict) {
                $periode = 'du ' . date('d/m/Y H:i', strtotime($conflict->date_entree)) . ' au ' . date('d/m/Y', strtotime($conflict->date_sortie));

                $validator->errors()->add('date_entree H:i', "L'appartement est déjà réservé $periode.");
            }
        });
    }

    public function messages()
    {
        return [
            'client.required' => 'Le nom du client est obligatoire.',
            'plateforme.required' => 'La plateforme est obligatoire.',
            'appartement_id.required' => 'L\'appartement est obligatoire.',
            'prix_nuit.required' => 'Le prix par nuit est obligatoire.',
            'date_entree.required' => 'La date d\'entrée est obligatoire.',
            'date_sortie.required' => 'La date de sortie est obligatoire.',
            'date_sortie.after' => 'La date de sortie doit être après la date d\'entrée.',
            'date_paiement.required_if' => 'La date de paiement est obligatoire si le statut est "Payé".',
        ];
    }
}
