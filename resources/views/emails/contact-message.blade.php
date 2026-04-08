<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouveau message</title>
</head>
<body style="font-family: Arial; background:#f5f5f5; padding:30px;">
    <div style="max-width:600px; margin:auto; background:#fff; padding:30px; border-radius:12px;">
        <h2 style="margin-top:0;">📩 Nouveau message de contact</h2>

        <p><strong>Nom :</strong> {{ $name }}</p>
        <p><strong>Email :</strong> {{ $email }}</p>
        <p><strong>Sujet :</strong> {{ $subjectLine }}</p>

        <hr style="margin:20px 0;">

        <p><strong>Message :</strong></p>
        <p style="line-height:1.6;">{{ $messageContent }}</p>
    </div>
</body>
</html>