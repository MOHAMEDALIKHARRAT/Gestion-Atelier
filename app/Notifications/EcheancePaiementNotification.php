<?php

namespace App\Notifications;

use App\Models\Paiement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EcheancePaiementNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Paiement $paiement,
        public string $messageRappel
    ) {}

    public function via(object $notifiable): array
{
    return ['mail'];
}

    public function toDatabase(object $notifiable): array
    {
        return [
            'paiement_id' => $this->paiement->id,
            'facture_id' => $this->paiement->facture_id,
            'type' => $this->paiement->type,
            'montant' => $this->paiement->montant,
            'reference' => $this->paiement->reference,
            'message' => $this->messageRappel,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Rappel de paiement - ' . $this->paiement->reference)
            ->greeting('Rappel pour votre atelier')
            ->line($this->messageRappel)
            ->line('Référence : ' . ($this->paiement->reference ?? 'Non renseignée'))
            ->line('Montant : ' . number_format((float) $this->paiement->montant, 3) . ' TND')
            ->line('Facture : ' . $this->paiement->facture->numero);
    }
}
