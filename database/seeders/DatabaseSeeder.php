<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Appartement;
use App\Models\Reservation;
use App\Models\Stock;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Créer un utilisateur de test
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);
         User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Créer des appartements de test
        $appartements = [
            [
                'name' => 'Appartement 1',
                'description' => 'Bel appartement 2 pièces centre ville',
                'address' => '123 Rue de la Paix, Paris',
                'capacity' => 4,
                'surface' => 45.50,
                'active' => true,
            ],
            [
                'name' => 'Appartement 2',
                'description' => 'Studio moderne avec vue',
                'address' => '456 Avenue des Champs, Lyon',
                'capacity' => 2,
                'surface' => 25.00,
                'active' => true,
            ],
        ];

        foreach ($appartements as $appartement) {
            Appartement::create($appartement);
        }

        // Créer quelques réservations de test
        $reservations = [
            [
                'date_entree' => '2024-01-15',
                'date_sortie' => '2024-01-20',
                'client' => 'Jean Dupont',
                'email' => 'jean@example.com',
                'plateforme' => 'airbnb',
                'appartement_id' => 1,
                'prix_nuit' => 80.00,
                'statut_paiement' => 'paid',
                'date_paiement' => '2024-01-10',
            ],
            [
                'date_entree' => '2024-01-25',
                'date_sortie' => '2024-01-28',
                'client' => 'Marie Martin',
                'email' => 'marie@example.com',
                'plateforme' => 'booking',
                'appartement_id' => 2,
                'prix_nuit' => 65.00,
                'statut_paiement' => 'unpaid',
            ],
        ];

        foreach ($reservations as $reservation) {
            Reservation::create($reservation);
        }

        // Créer des stocks de test
        $stocks = [
            [
                'element' => 'Papier toilette',
                'quantite_mois' => 20,
                'utilise' => 18,
                'seuil' => 5,
                'appartement_id' => 1,
            ],
            [
                'element' => 'Serviettes',
                'quantite_mois' => 10,
                'utilise' => 2,
                'seuil' => 3,
                'appartement_id' => 1,
            ],
            [
                'element' => 'Produits ménage',
                'quantite_mois' => 5,
                'utilise' => 4,
                'seuil' => 2,
                'appartement_id' => 2,
            ],
        ];

        foreach ($stocks as $stock) {
            Stock::create($stock);
        }
    }
}