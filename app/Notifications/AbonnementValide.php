<?php

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AbonnementValide extends Notification
{
    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Abonnement Validé')
            ->greeting('Bonjour ' . $notifiable->name)
            ->line('Votre demande d’abonnement a été validée avec succès.')
            ->action('Voir votre espace', url('/abonnement'))
            ->line('Merci d’avoir rejoint le FabLab Mahdia !');
    }
}

