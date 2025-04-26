<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture {{ $facture_num }}</title>
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
        .facture-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .facture-info-left, .facture-info-right {
            width: 48%;
        }
        .facture-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #3490dc;
        }
        .client-info, .facture-details {
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
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .payment-info {
            margin-top: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">FabLab</div>
            <div>Centre de fabrication numérique</div>
        </div>
        
        <div class="facture-title">FACTURE</div>
        
        <div class="facture-info">
            <div class="facture-info-left">
                <div class="client-info">
                    <strong>Facturé à:</strong><br>
                    {{ $user->nom }} {{ $user->prenom }}<br>
                    {{ $user->email }}<br>
                </div>
            </div>
            
            <div class="facture-info-right">
                <div class="facture-details">
                    <strong>Facture N°:</strong> {{ $facture_num }}<br>
                    <strong>Date d'émission:</strong> {{ $date_emission }}<br>
                    <strong>Date de début:</strong> {{ $abonnement->date_debut->format('d/m/Y') }}<br>
                    <strong>Date de fin:</strong> {{ $abonnement->date_fin->format('d/m/Y') }}<br>
                </div>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Période</th>
                    <th>Prix</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Abonnement {{ $abonnement->type && isset(config('abonnements.types')[$abonnement->type]) ? config('abonnements.types')[$abonnement->type]['name'] : ($abonnement->typeAbonnement ? $abonnement->typeAbonnement->nom : ucfirst($abonnement->type)) }}</td>
                    <td>{{ $abonnement->date_debut->format('d/m/Y') }} au {{ $abonnement->date_fin->format('d/m/Y') }}</td>
                    <td>{{ number_format($abonnement->prix, 2) }} DT</td>
                </tr>
                <tr class="total-row">
                    <td colspan="2" style="text-align: right;"><strong>Total</strong></td>
                    <td><strong>{{ number_format($abonnement->prix, 2) }} DT</strong></td>
                </tr>
            </tbody>
        </table>
        
        <div class="payment-info">
            <strong>Informations de paiement:</strong><br>
            Statut: {{ ucfirst($abonnement->statut) }}<br>
            Date de paiement: {{ $abonnement->date_validation ? $abonnement->date_validation->format('d/m/Y') : 'Non payé' }}
        </div>
        
        <div class="footer">
            <p>FabLab - Centre de fabrication numérique</p>
            <p>Cette facture est générée automatiquement et ne nécessite pas de signature.</p>
            @if(isset($reservation) && isset($reservation->admin) && $reservation->admin)
                <p style="margin-top:10px;font-size:12px;color:#444;">
                    Validé par : {{ $reservation->admin->prenom }} {{ $reservation->admin->nom }} (Admin)
                </p>
            @endif
        </div>
    </div>
</body>
</html>
