<?php

namespace App\Console\Commands;

use App\Models\Paiement;
use App\Models\User;
use App\Notifications\EcheancePaiementNotification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Notifications\Notification;

#[Signature('app:verifier-echeances-paiements')]
#[Description('Command description')]
class VerifierEcheancesPaiements extends Command
{
    protected $signature = 'app:verifier-echeances-paiements';

    protected $description = 'Envoyer les rappels des chèques et cambiales par e-mail';

    public function handle(): int
    {
        $email = config('services.atelier.email_rappels');

        if (!$email) {
            $this->error('Configure ATELIER_EMAIL_RAPPELS dans le fichier .env.');
            return self::FAILURE;
        }

        $aujourdhui = now()->startOfDay();
        $demain = now()->addDay()->startOfDay();

        $paiements = Paiement::with('facture')
            ->whereIn('type', ['cheque', 'cambiale'])
            ->whereIn('statut', ['a_traiter', 'depose'])
            ->get();

        foreach ($paiements as $paiement) {
            $date = $paiement->type === 'cheque'
                ? $paiement->date_depot_prevue
                : $paiement->date_echeance;

            if (!$date) {
                continue;
            }

            $date = $date->copy()->startOfDay();
            $message = null;
            $champRappel = null;

            if ($date->equalTo($demain)
                && !$paiement->rappel_veille_envoye) {
                $message = 'Rappel : une échéance est prévue demain.';
                $champRappel = 'rappel_veille_envoye';
            } elseif ($date->equalTo($aujourdhui)
                && !$paiement->rappel_jour_envoye) {
                $message = 'Attention : une échéance est prévue aujourd’hui.';
                $champRappel = 'rappel_jour_envoye';
            } elseif ($date->lt($aujourdhui)
                && !$paiement->rappel_retard_envoye) {
                $message = 'Attention : une échéance est en retard.';
                $champRappel = 'rappel_retard_envoye';
            }

            if (!$message) {
                continue;
            }

            try {
                Notification::route('mail', $email)->notify(
                    new EcheancePaiementNotification($paiement, $message)
                );

                $paiement->{$champRappel} = true;
                $paiement->save();

                $this->info(
                    "E-mail envoyé pour le paiement #{$paiement->id} à {$email}"
                );
            } catch (\Throwable $e) {
                $this->error(
                    "Échec pour le paiement #{$paiement->id} : {$e->getMessage()}"
                );
            }
        }

        $this->info('Vérification des échéances terminée.');

        return self::SUCCESS;
    }
}
