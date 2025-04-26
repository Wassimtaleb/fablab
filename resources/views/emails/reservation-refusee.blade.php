@component('mail::message')
# Réservation refusée

Bonjour {{ $reservation->user->prenom ?? '' }} {{ $reservation->user->nom ?? '' }},

Nous vous informons que votre demande de réservation pour la machine **{{ $reservation->machine->nom ?? '' }}** le **{{ $reservation->date_reservation->format('d/m/Y H:i') }}** a été refusée.

Pour plus d'informations, merci de vous présenter au FabLab.

Merci de votre compréhension.

Cordialement,
L'équipe du FabLab
@endcomponent
