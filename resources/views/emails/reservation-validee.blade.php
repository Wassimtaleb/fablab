@php($user = $reservation->user)
@php($machine = $reservation->machine ?? null)

<p>Bonjour {{ $user->nom }} {{ $user->prenom }},</p>
<p>Votre réservation a été <strong>validée</strong> avec succès !</p>
@if($machine)
<p><strong>Machine :</strong> {{ $machine->nom }}</p>
@endif
<p><strong>Date :</strong> {{ $reservation->date_reservation->format('d/m/Y') }} à {{ $reservation->heure_debut }}<br>
<strong>Durée :</strong> {{ $reservation->duree }} minutes</p>
<p>Vous pouvez consulter les détails de votre réservation dans votre espace personnel.</p>
<p>Merci d'utiliser le FabLab Mahdia !</p>
