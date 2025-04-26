<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Refus de votre abonnement</title>
</head>
<body style="font-family: Arial, sans-serif; color: #222; background: #fff;">
    <div style="max-width: 480px; margin: 30px auto; padding: 24px; border-radius: 8px; border: 1px solid #eee; background: #fff;">
        <h2 style="color: #e3342f;">Votre demande d'abonnement a été refusée</h2>
        <p>Bonjour {{ trim(($user->prenom ?? '') . ' ' . ($user->nom ?? $user->name ?? '')) }},</p>
        <p>{{ $motif }}</p>
        <p>Vous pouvez soumettre une nouvelle demande d'abonnement à tout moment si vous le souhaitez.</p>
        <p>Pour toute question, contactez le FabLab Mahdia.</p>
        <p style="margin-top: 24px; font-size: 0.95em; color: #888;">Ceci est un email automatique, merci de ne pas y répondre.</p>
    </div>
</body>
</html>
