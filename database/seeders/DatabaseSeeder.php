<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Appartement;
use App\Models\Reservation;
use App\Models\Depense;
use App\Models\ChargeMensuelle;
use App\Models\Stock;
use App\Models\DegatReparation;
use App\Models\Disponibilite;
use App\Models\TauxOccupation;
use App\Models\ResumeMensuel;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Créer un utilisateur admin
        $user = User::factory()->create([
            'name' => 'Meli eric',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('Eric@456'),
        ]);

        // 2. Créer les appartements
        $appartements = [
            [
                'name' => 'Studio Centre-Ville',
                'description' => 'Charmant studio de 25m² en plein centre-ville, proche des transports',
                'address' => '15 Rue de la République, 75001 Paris',
                'capacity' => 2,
                'surface' => 25.00,
                'active' => true,
            ],
            [
                'name' => 'T2 Quartier Latin',
                'description' => 'Appartement 2 pièces rénové avec vue sur cour, quartier calme',
                'address' => '42 Boulevard Saint-Michel, 75005 Paris',
                'capacity' => 4,
                'surface' => 45.50,
                'active' => true,
            ],
            [
                'name' => 'T3 Montmartre',
                'description' => 'Grand 3 pièces avec balcon, vue sur Sacré-Cœur',
                'address' => '8 Rue des Abbesses, 75018 Paris',
                'capacity' => 6,
                'surface' => 65.00,
                'active' => true,
            ],
            [
                'name' => 'Loft Marais',
                'description' => 'Loft moderne avec mezzanine, style industriel',
                'address' => '23 Rue des Rosiers, 75004 Paris',
                'capacity' => 4,
                'surface' => 55.00,
                'active' => true,
            ],
        ];

        foreach ($appartements as $appartement) {
            Appartement::create($appartement);
        }

        $appartementIds = Appartement::pluck('id')->toArray();

        // 3. Créer les réservations (6 mois de données)
        $reservations = [
            // Janvier 2024
            [
                'date_entree' => '2024-01-05',
                'date_sortie' => '2024-01-12',
                'client' => 'Jean Dupont',
                'email' => 'jean.dupont@email.fr',
                'telephone' => '0123456789',
                'plateforme' => 'airbnb',
                'appartement_id' => $appartementIds[0],
                'prix_nuit' => 85.00,
                'statut_paiement' => 'paid',
                'date_paiement' => '2024-01-03',
            ],
            [
                'date_entree' => '2024-01-15',
                'date_sortie' => '2024-01-22',
                'client' => 'Marie Martin',
                'email' => 'marie.martin@email.fr',
                'telephone' => '0987654321',
                'plateforme' => 'booking',
                'appartement_id' => $appartementIds[1],
                'prix_nuit' => 120.00,
                'statut_paiement' => 'paid',
                'date_paiement' => '2024-01-13',
            ],
            [
                'date_entree' => '2024-01-20',
                'date_sortie' => '2024-01-27',
                'client' => 'Pierre Dubois',
                'email' => 'pierre.dubois@email.fr',
                'telephone' => '0147258369',
                'plateforme' => 'direct',
                'appartement_id' => $appartementIds[2],
                'prix_nuit' => 150.00,
                'statut_paiement' => 'paid',
                'date_paiement' => '2024-01-18',
            ],

            // Février 2024
            [
                'date_entree' => '2024-02-02',
                'date_sortie' => '2024-02-09',
                'client' => 'Sophie Leroy',
                'email' => 'sophie.leroy@email.fr',
                'telephone' => '0156789432',
                'plateforme' => 'airbnb',
                'appartement_id' => $appartementIds[0],
                'prix_nuit' => 90.00,
                'statut_paiement' => 'paid',
                'date_paiement' => '2024-01-30',
            ],
            [
                'date_entree' => '2024-02-14',
                'date_sortie' => '2024-02-18',
                'client' => 'Thomas Bernard',
                'email' => 'thomas.bernard@email.fr',
                'telephone' => '0198765432',
                'plateforme' => 'booking',
                'appartement_id' => $appartementIds[3],
                'prix_nuit' => 135.00,
                'statut_paiement' => 'paid',
                'date_paiement' => '2024-02-12',
            ],

            // Mars 2024
            [
                'date_entree' => '2024-03-01',
                'date_sortie' => '2024-03-08',
                'client' => 'Emma Rousseau',
                'email' => 'emma.rousseau@email.fr',
                'telephone' => '0123987456',
                'plateforme' => 'airbnb',
                'appartement_id' => $appartementIds[1],
                'prix_nuit' => 125.00,
                'statut_paiement' => 'paid',
                'date_paiement' => '2024-02-28',
            ],
            [
                'date_entree' => '2024-03-15',
                'date_sortie' => '2024-03-22',
                'client' => 'Lucas Moreau',
                'email' => 'lucas.moreau@email.fr',
                'telephone' => '0187654321',
                'plateforme' => 'direct',
                'appartement_id' => $appartementIds[2],
                'prix_nuit' => 160.00,
                'statut_paiement' => 'unpaid',
            ],

            // Avril 2024
            [
                'date_entree' => '2024-04-05',
                'date_sortie' => '2024-04-12',
                'client' => 'Camille Petit',
                'email' => 'camille.petit@email.fr',
                'telephone' => '0145632789',
                'plateforme' => 'booking',
                'appartement_id' => $appartementIds[0],
                'prix_nuit' => 95.00,
                'statut_paiement' => 'paid',
                'date_paiement' => '2024-04-03',
            ],
            [
                'date_entree' => '2024-04-20',
                'date_sortie' => '2024-04-25',
                'client' => 'Antoine Garcia',
                'email' => 'antoine.garcia@email.fr',
                'telephone' => '0167894523',
                'plateforme' => 'airbnb',
                'appartement_id' => $appartementIds[3],
                'prix_nuit' => 140.00,
                'statut_paiement' => 'paid',
                'date_paiement' => '2024-04-18',
            ],

            // Mai 2024
            [
                'date_entree' => '2024-05-10',
                'date_sortie' => '2024-05-17',
                'client' => 'Julie Roux',
                'email' => 'julie.roux@email.fr',
                'telephone' => '0134567890',
                'plateforme' => 'booking',
                'appartement_id' => $appartementIds[1],
                'prix_nuit' => 130.00,
                'statut_paiement' => 'paid',
                'date_paiement' => '2024-05-08',
            ],

            // Juin 2024
            [
                'date_entree' => '2024-06-01',
                'date_sortie' => '2024-06-08',
                'client' => 'Maxime Blanc',
                'email' => 'maxime.blanc@email.fr',
                'telephone' => '0189456723',
                'plateforme' => 'direct',
                'appartement_id' => $appartementIds[2],
                'prix_nuit' => 165.00,
                'statut_paiement' => 'paid',
                'date_paiement' => '2024-05-30',
            ],
        ];

        foreach ($reservations as $reservation) {
            Reservation::create($reservation);
        }

        // 4. Créer les dépenses
        $depenses = [
            [
                'date' => '2024-01-15',
                'type_depense' => 'maintenance',
                'description' => 'Réparation robinet cuisine - Studio Centre-Ville',
                'appartement_id' => $appartementIds[0],
                'montant' => 85.50,
            ],
            [
                'date' => '2024-01-20',
                'type_depense' => 'nettoyage',
                'description' => 'Nettoyage approfondi après départ client',
                'appartement_id' => $appartementIds[1],
                'montant' => 120.00,
            ],
            [
                'date' => '2024-02-05',
                'type_depense' => 'equipement',
                'description' => 'Achat nouveau lave-linge pour T3 Montmartre',
                'appartement_id' => $appartementIds[2],
                'montant' => 450.00,
            ],
            [
                'date' => '2024-02-12',
                'type_depense' => 'reparation',
                'description' => 'Changement serrure entrée - Loft Marais',
                'appartement_id' => $appartementIds[3],
                'montant' => 180.00,
            ],
            [
                'date' => '2024-03-08',
                'type_depense' => 'amenagement',
                'description' => 'Peinture salon et chambre - T2 Quartier Latin',
                'appartement_id' => $appartementIds[1],
                'montant' => 320.00,
            ],
            [
                'date' => '2024-03-15',
                'type_depense' => 'maintenance',
                'description' => 'Révision chaudière annuelle',
                'appartement_id' => $appartementIds[2],
                'montant' => 95.00,
            ],
            [
                'date' => '2024-04-02',
                'type_depense' => 'nettoyage',
                'description' => 'Nettoyage vitres et sols - Studio Centre-Ville',
                'appartement_id' => $appartementIds[0],
                'montant' => 75.00,
            ],
            [
                'date' => '2024-04-18',
                'type_depense' => 'equipement',
                'description' => 'Remplacement micro-ondes défaillant',
                'appartement_id' => $appartementIds[3],
                'montant' => 125.00,
            ],
            [
                'date' => '2024-05-05',
                'type_depense' => 'autre',
                'description' => 'Assurance habitation annuelle - Tous logements',
                'appartement_id' => null,
                'montant' => 680.00,
            ],
            [
                'date' => '2024-05-20',
                'type_depense' => 'maintenance',
                'description' => 'Débouchage canalisation salle de bain',
                'appartement_id' => $appartementIds[1],
                'montant' => 110.00,
            ],
        ];

        foreach ($depenses as $depense) {
            Depense::create($depense);
        }

        // 5. Créer les charges mensuelles
        $chargesMensuelles = [
            // Charges générales
            [
                'service' => 'EDF - Électricité générale',
                'appartement_id' => null,
                'date_paiement' => '2024-01-05',
                'montant' => 180.50,
                'recurrent' => true,
                'notes' => 'Facture mensuelle électricité parties communes',
            ],
            [
                'service' => 'Gaz de France',
                'appartement_id' => null,
                'date_paiement' => '2024-01-10',
                'montant' => 95.30,
                'recurrent' => true,
                'notes' => 'Chauffage collectif',
            ],
            [
                'service' => 'Orange Internet Fibre',
                'appartement_id' => null,
                'date_paiement' => '2024-01-15',
                'montant' => 39.99,
                'recurrent' => true,
                'notes' => 'Internet haut débit pour tous les logements',
            ],

            // Charges par appartement
            [
                'service' => 'EDF - Studio Centre-Ville',
                'appartement_id' => $appartementIds[0],
                'date_paiement' => '2024-01-08',
                'montant' => 45.20,
                'recurrent' => true,
                'notes' => 'Électricité studio',
            ],
            [
                'service' => 'EDF - T2 Quartier Latin',
                'appartement_id' => $appartementIds[1],
                'date_paiement' => '2024-01-08',
                'montant' => 62.80,
                'recurrent' => true,
                'notes' => 'Électricité T2',
            ],
            [
                'service' => 'EDF - T3 Montmartre',
                'appartement_id' => $appartementIds[2],
                'date_paiement' => '2024-01-08',
                'montant' => 78.90,
                'recurrent' => true,
                'notes' => 'Électricité T3',
            ],
            [
                'service' => 'EDF - Loft Marais',
                'appartement_id' => $appartementIds[3],
                'date_paiement' => '2024-01-08',
                'montant' => 68.50,
                'recurrent' => true,
                'notes' => 'Électricité loft',
            ],

            // Charges ponctuelles
            [
                'service' => 'Syndic - Charges copropriété',
                'appartement_id' => null,
                'date_paiement' => '2024-01-20',
                'montant' => 450.00,
                'recurrent' => false,
                'notes' => 'Charges trimestrielles copropriété',
            ],
            [
                'service' => 'Taxe foncière',
                'appartement_id' => null,
                'date_paiement' => '2024-02-15',
                'montant' => 1250.00,
                'recurrent' => false,
                'notes' => 'Taxe foncière annuelle',
            ],
        ];

        foreach ($chargesMensuelles as $charge) {
            ChargeMensuelle::create($charge);
        }

        // 6. Créer les stocks
        $stocks = [
            // Studio Centre-Ville
            [
                'element' => 'Papier toilette',
                'quantite_mois' => 12,
                'utilise' => 8,
                'seuil' => 3,
                'appartement_id' => $appartementIds[0],
            ],
            [
                'element' => 'Serviettes de bain',
                'quantite_mois' => 6,
                'utilise' => 2,
                'seuil' => 2,
                'appartement_id' => $appartementIds[0],
            ],
            [
                'element' => 'Draps',
                'quantite_mois' => 4,
                'utilise' => 1,
                'seuil' => 1,
                'appartement_id' => $appartementIds[0],
            ],

            // T2 Quartier Latin
            [
                'element' => 'Papier toilette',
                'quantite_mois' => 15,
                'utilise' => 12,
                'seuil' => 4,
                'appartement_id' => $appartementIds[1],
            ],
            [
                'element' => 'Produits ménage',
                'quantite_mois' => 8,
                'utilise' => 6,
                'seuil' => 2,
                'appartement_id' => $appartementIds[1],
            ],
            [
                'element' => 'Serviettes de bain',
                'quantite_mois' => 8,
                'utilise' => 3,
                'seuil' => 2,
                'appartement_id' => $appartementIds[1],
            ],

            // T3 Montmartre
            [
                'element' => 'Papier toilette',
                'quantite_mois' => 20,
                'utilise' => 15,
                'seuil' => 5,
                'appartement_id' => $appartementIds[2],
            ],
            [
                'element' => 'Gel douche',
                'quantite_mois' => 6,
                'utilise' => 4,
                'seuil' => 2,
                'appartement_id' => $appartementIds[2],
            ],
            [
                'element' => 'Draps',
                'quantite_mois' => 6,
                'utilise' => 2,
                'seuil' => 2,
                'appartement_id' => $appartementIds[2],
            ],

            // Loft Marais
            [
                'element' => 'Papier toilette',
                'quantite_mois' => 18,
                'utilise' => 16,
                'seuil' => 4,
                'appartement_id' => $appartementIds[3],
            ],
            [
                'element' => 'Produits ménage',
                'quantite_mois' => 10,
                'utilise' => 8,
                'seuil' => 3,
                'appartement_id' => $appartementIds[3],
            ],

            // Stock général
            [
                'element' => 'Ampoules LED',
                'quantite_mois' => 20,
                'utilise' => 5,
                'seuil' => 5,
                'appartement_id' => null,
            ],
            [
                'element' => 'Piles AA',
                'quantite_mois' => 24,
                'utilise' => 8,
                'seuil' => 6,
                'appartement_id' => null,
            ],
        ];

        foreach ($stocks as $stock) {
            Stock::create($stock);
        }

        // 7. Créer les dégâts et réparations
        $degats = [
            [
                'date' => '2024-01-10',
                'appartement_id' => $appartementIds[0],
                'type_degat' => 'plomberie',
                'description' => 'Fuite robinet cuisine, goutte à goutte persistante',
                'solution' => 'Remplacement joint et cartouche robinet',
                'cout' => 85.50,
                'statut' => 'termine',
                'date_reparation' => '2024-01-15',
                'reparateur' => 'Plomberie Dupont',
            ],
            [
                'date' => '2024-02-03',
                'appartement_id' => $appartementIds[1],
                'type_degat' => 'electricite',
                'description' => 'Prise électrique salon ne fonctionne plus',
                'solution' => 'Changement prise défectueuse et vérification circuit',
                'cout' => 65.00,
                'statut' => 'termine',
                'date_reparation' => '2024-02-05',
                'reparateur' => 'Électricité Martin',
            ],
            [
                'date' => '2024-02-20',
                'appartement_id' => $appartementIds[2],
                'type_degat' => 'chauffage',
                'description' => 'Radiateur chambre principale ne chauffe pas',
                'solution' => 'Purge radiateur et vérification vanne thermostatique',
                'cout' => 45.00,
                'statut' => 'termine',
                'date_reparation' => '2024-02-22',
                'reparateur' => 'Chauffage Pro',
            ],
            [
                'date' => '2024-03-12',
                'appartement_id' => $appartementIds[3],
                'type_degat' => 'serrurerie',
                'description' => 'Serrure porte d\'entrée difficile à ouvrir',
                'solution' => 'Lubrification mécanisme et ajustement',
                'cout' => 35.00,
                'statut' => 'termine',
                'date_reparation' => '2024-03-14',
                'reparateur' => 'Serrurerie Rapide',
            ],
            [
                'date' => '2024-04-08',
                'appartement_id' => $appartementIds[1],
                'type_degat' => 'electromenager',
                'description' => 'Lave-vaisselle fait du bruit anormal',
                'solution' => 'En cours de diagnostic par technicien',
                'cout' => null,
                'statut' => 'en_cours',
                'date_reparation' => null,
                'reparateur' => 'Service Électroménager',
            ],
            [
                'date' => '2024-05-15',
                'appartement_id' => $appartementIds[0],
                'type_degat' => 'peinture',
                'description' => 'Tache d\'humidité au plafond salle de bain',
                'solution' => null,
                'cout' => null,
                'statut' => 'signale',
                'date_reparation' => null,
                'reparateur' => null,
            ],
        ];

        foreach ($degats as $degat) {
            DegatReparation::create($degat);
        }

        // 8. Créer les disponibilités (exemple pour janvier 2024)
        $disponibilites = [];
        $startDate = Carbon::create(2024, 1, 1);
        $endDate = Carbon::create(2024, 6, 30);

        foreach ($appartementIds as $appartementId) {
            $current = $startDate->copy();
            while ($current <= $endDate) {
                // Vérifier s'il y a une réservation pour cette date
                $reservation = Reservation::where('appartement_id', $appartementId)
                    ->where('date_entree', '<=', $current->format('Y-m-d'))
                    ->where('date_sortie', '>', $current->format('Y-m-d'))
                    ->first();

                $statut = 'available';
                $reservationId = null;

                if ($reservation) {
                    $statut = 'occupied';
                    $reservationId = $reservation->id;
                }

                // Ajouter quelques jours de maintenance aléatoires
                if (!$reservation && rand(1, 30) == 1) {
                    $statut = 'maintenance';
                }

                $disponibilites[] = [
                    'appartement_id' => $appartementId,
                    'date' => $current->format('Y-m-d'),
                    'statut' => $statut,
                    'reservation_id' => $reservationId,
                    'notes' => $statut === 'maintenance' ? 'Maintenance préventive' : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $current->addDay();
            }
        }

        // Insérer par chunks pour éviter les problèmes de mémoire
        collect($disponibilites)->chunk(1000)->each(function ($chunk) {
            Disponibilite::insert($chunk->toArray());
        });

    }
}
