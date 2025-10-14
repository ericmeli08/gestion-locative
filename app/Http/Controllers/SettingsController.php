<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class SettingsController extends Controller
{
    public function pushSqlite()
    {
        $message = '';
        $status = 'success';

        try {
            $dbPath = base_path('database/database.sqlite');

            if (!file_exists($dbPath)) {
                $message = 'La base SQLite est introuvable.';
                $status = 'error';
            } else {
                $commands = [
                    ['git', 'add', $dbPath],
                    ['git', 'commit', '-m', 'Mise à jour de la base SQLite'],
                    ['git', 'push -u origin dev'],
                ];

                foreach ($commands as $command) {
                    $process = new Process($command, base_path());
                    $process->run();

                    if (!$process->isSuccessful()) {
                        $message = 'Erreur Git : ' . $process->getErrorOutput();
                        $status = 'error';
                        break; // arrêter si une commande échoue
                    }
                }

                if ($status === 'success') {
                    $message = 'Base SQLite poussée avec succès ✅';
                }
            }
        } catch (\Exception $e) {
            $message = 'Exception : ' . $e->getMessage();
            $status = 'error';
        }

        return Inertia::render('Settings', [
            'flash' => [
                'status' => $status,
                'message' => $message,
            ]
        ]);
    }
}
