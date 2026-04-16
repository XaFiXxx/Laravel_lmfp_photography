<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouveau message</title>
</head>
<body style="margin:0; padding:0; background:#f3f4f6; font-family:Arial, Helvetica, sans-serif;">

    <div style="width:100%; padding:32px 16px;">
        <div style="max-width:600px; margin:0 auto; background:#ffffff; border-radius:16px; overflow:hidden; border:1px solid #e5e7eb;">

            <!-- HEADER -->
            <div style="background:#0f172a; text-align:center; padding:24px;">
                <h2 style="margin:0; color:#ffffff; font-size:20px;">
                    📩 Nouveau message de contact
                </h2>
            </div>

            <!-- INFOS -->
            <div style="padding:24px 28px; font-size:14px; color:#374151;">
                <p style="margin:0 0 10px;">
                    <strong>Nom :</strong> {{ $name }}
                </p>

                <p style="margin:0 0 10px;">
                    <strong>Email :</strong> {{ $email }}
                </p>

                <p style="margin:0 0 10px;">
                    <strong>Localité :</strong> {{ $location }}
                </p>

                @if(!empty($social_link))
                    <p style="margin:0 0 10px;">
                        <strong>Réseau social :</strong> {{ $social_link }}
                    </p>
                @endif

                <p style="margin:0 0 10px;">
                    <strong>Sujet :</strong> {{ $subjectLine }}
                </p>

                <hr style="margin:20px 0; border:none; border-top:1px solid #e5e7eb;">

                <p style="margin:0 0 10px;">
                    <strong>Message :</strong>
                </p>

                <div style="line-height:1.7; color:#4b5563; white-space:pre-line;">
                    {{ $messageContent }}
                </div>
            </div>

            <!-- FOOTER -->
            <div style="background:#f9fafb; padding:16px 24px; text-align:center; font-size:12px; color:#9ca3af;">
                Message envoyé depuis le formulaire de contact du site.
            </div>

        </div>
    </div>

</body>
</html>