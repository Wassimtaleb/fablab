<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de réservation {{ $ticket_num }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #3490dc;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #3490dc;
            margin-bottom: 10px;
        }
        .ticket-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .ticket-info-left, .ticket-info-right {
            width: 48%;
        }
        .ticket-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #3490dc;
        }
        .client-info, .ticket-details {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .important-note {
            margin-top: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
            border-left: 4px solid #3490dc;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .qr-code {
            text-align: center;
            margin: 30px 0;
        }
        .qr-code img {
            width: 150px;
            height: 150px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">FabLab</div>
            <div>Centre de fabrication numérique</div>
        </div>
        
        <div class="ticket-title">TICKET DE RÉSERVATION</div>
        
        <div class="ticket-info">
            <div class="ticket-info-left">
                <div class="client-info">
                    <strong>Réservé par:</strong><br>
                    {{ $user->nom }} {{ $user->prenom }}<br>
                    {{ $user->email }}<br>
                </div>
            </div>
            
            <div class="ticket-info-right">
                <div class="ticket-details">
                    <strong>Ticket N°:</strong> {{ $ticket_num }}<br>
                    <strong>Date d'émission:</strong> {{ $date_emission }}<br>
                    <strong>Date de réservation:</strong> 
                    @if($reservation->date_reservation)
                        {{ $reservation->date_reservation->format('d/m/Y') }}
                    @else
                        Non spécifiée
                    @endif
                    <br>
                    <strong>Heure de début:</strong> {{ $reservation->heure_debut }}<br>
                    <strong>Durée:</strong> {{ $reservation->duree }}h<br>
                </div>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Machine</th>
                    <th>Date</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $reservation->machine->nom }}</td>
                    <td>
                        @if($reservation->date_reservation)
                            {{ $reservation->date_reservation->format('d/m/Y') }}
                        @else
                            Non spécifiée
                        @endif
                    </td>
                    <td>{{ ucfirst($reservation->statut) }}</td>
                </tr>
            </tbody>
        </table>
        
        <div class="important-note">
            <strong>Informations importantes:</strong><br>
            <p>Veuillez vous présenter 15 minutes avant l'heure de votre réservation avec ce ticket.</p>
            <p>En cas d'empêchement, merci de nous prévenir au moins 24 heures à l'avance.</p>
        </div>
        
        <div class="qr-code">
            <p><strong>Présentez ce code à votre arrivée</strong></p>
            <!-- Placeholder pour un QR code -->
            <div style="width: 150px; height: 150px; border: 1px solid #ddd; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                QR Code: {{ $ticket_num }}
            </div>
        </div>
        
        <div class="footer">
            <p>FabLab - Centre de fabrication numérique</p>
            <p>Ce ticket est votre preuve de réservation. Veuillez le présenter lors de votre arrivée.</p>
            @if(isset($reservation->admin) && $reservation->admin)
                <p style="margin-top:10px;font-size:12px;color:#444;">
                    Validé par : {{ $reservation->admin->prenom }} {{ $reservation->admin->nom }} (Admin)
                </p>
            @endif
        </div>
    </div>
</body>
</html>
