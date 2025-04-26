<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AbonnementRefuse extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $abonnement;
    public $motif;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $abonnement, $motif)
    {
        $this->user = $user;
        $this->abonnement = $abonnement;
        $this->motif = $motif;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Refus de votre demande d\'abonnement FabLab')
            ->view('emails.abonnement-refuse');
    }
}
