<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Export global FabLab</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; }
        h2 { background: #e0e0e0; padding: 6px; margin-top: 30px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #aaa; padding: 4px 6px; text-align: left; }
        th { background: #f5f5f5; }
    </style>
</head>
<body>
    <h1>Export global FabLab</h1>

    <h2>Abonnements</h2>
    <table>
        <thead>
            <tr>
                <th>Utilisateur</th>
                <th>Email</th>
                <th>Type</th>
                <th>Prix</th>
                <th>Statut</th>
                <th>Date début</th>
                <th>Date fin</th>
                <th>Date création</th>
            </tr>
        </thead>
        <tbody>
        @foreach($abonnements as $abonnement)
            <tr>
                <td>{{ $abonnement->user ? $abonnement->user->nom . ' ' . $abonnement->user->prenom : '' }}</td>
                <td>{{ $abonnement->user ? $abonnement->user->email : '' }}</td>
                <td>{{ $types[$abonnement->type] ?? $abonnement->type }}</td>
                <td>{{ $abonnement->prix }}</td>
                <td>{{ ucfirst($abonnement->statut) }}</td>
                <td>{{ $abonnement->date_debut }}</td>
                <td>{{ $abonnement->date_fin }}</td>
                <td>{{ $abonnement->created_at }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h2>Paiements</h2>
    <table>
        <thead>
            <tr>
                <th>Utilisateur</th>
                <th>Type</th>
                <th>Montant</th>
                <th>Date paiement</th>
            </tr>
        </thead>
        <tbody>
        @foreach($paiements as $paiement)
            <tr>
                <td>{{ $paiement->user ? $paiement->user->nom . ' ' . $paiement->user->prenom : '' }}</td>
                <td>{{ $types[$paiement->type] ?? $paiement->type }}</td>
                <td>{{ $paiement->prix }}</td>
                <td>{{ $paiement->date_validation }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h2>Réservations machines</h2>
    <table>
        <thead>
            <tr>
                <th>Utilisateur</th>
                <th>Machine</th>
                <th>Date début</th>
                <th>Date fin</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
        @foreach($reservations as $reservation)
            <tr>
                <td>{{ $reservation->user ? $reservation->user->nom . ' ' . $reservation->user->prenom : '' }}</td>
                <td>{{ $reservation->machine ? $reservation->machine->nom : '' }}</td>
                <td>{{ $reservation->date_debut }}</td>
                <td>{{ $reservation->date_fin }}</td>
                <td>{{ ucfirst($reservation->statut) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h2>Liste des machines</h2>
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Catégorie</th>
                <th>Statut</th>
                <th>Caractéristiques</th>
            </tr>
        </thead>
        <tbody>
        @foreach($machines as $machine)
            <tr>
                <td>{{ $machine->nom }}</td>
                <td>{{ $machine->categorie }}</td>
                <td>{{ ucfirst($machine->statut) }}</td>
                <td>{{ $machine->caracteristiques }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h2>Statistiques d'utilisation</h2>
    <table>
        <thead>
            <tr>
                <th>Machine</th>
                <th>Nombre de réservations</th>
            </tr>
        </thead>
        <tbody>
        @foreach($statistiques as $stat)
            <tr>
                <td>{{ $stat->machine ? $stat->machine->nom : '' }}</td>
                <td>{{ $stat->nb_reservations }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h2>Rapport financier</h2>
    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>Total recettes</th>
            </tr>
        </thead>
        <tbody>
        @foreach($finances as $finance)
            <tr>
                <td>{{ $types[$finance->type] ?? $finance->type }}</td>
                <td>{{ $finance->total }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

</body>
</html>
