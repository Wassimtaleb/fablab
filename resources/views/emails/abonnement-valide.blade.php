<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Confirmation de votre abonnement</title>
</head>
<body style="font-family: Arial, sans-serif; color: #222; background: #fff;">
    <div style="max-width: 480px; margin: 30px auto; padding: 24px; border-radius: 8px; border: 1px solid #eee; background: #fff;">
        <h2 style="color: #007bff;">Confirmation de votre abonnement</h2>
        <p>Bonjour {{ trim(($user->prenom ?? '') . ' ' . ($user->nom ?? $user->name ?? '')) }},</p>
        <p>Votre demande d'abonnement a été <strong>acceptée</strong> et votre paiement a été effectué avec succès.</p>
        <p>Vous pouvez désormais réserver des machines et profiter de tous les services du FabLab Mahdia.</p>
        <p>Merci d'avoir rejoint le FabLab&nbsp;!</p>
    
    </div>
</body>
</html>
