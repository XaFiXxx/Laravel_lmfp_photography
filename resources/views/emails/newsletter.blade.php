<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $subjectLine }}</title>
</head>
<body style="margin:0; padding:0; background:#f3f4f6; font-family:Arial, sans-serif;">

    <div style="max-width:600px; margin:40px auto; background:#ffffff; border-radius:12px; overflow:hidden; border:1px solid #e5e7eb;">

        <!-- HEADER -->
        <div style="background:#0f172a; color:#ffffff; padding:32px; text-align:center;">
            <h1 style="margin:0; font-size:28px;">LMFP</h1>
            <p style="margin:8px 0 0; font-size:14px; color:#cbd5e1;">
                Newsletter
            </p>
        </div>

        <!-- CONTENT -->
        <div style="padding:32px;">
            <h2 style="margin-top:0; margin-bottom:16px; color:#111827;">
                {{ $subjectLine }}
            </h2>

            <div style="color:#4b5563; line-height:1.7; white-space:pre-line; font-size:15px;">
                {{ $contentText }}
            </div>

            <!-- CTA BUTTON -->
            <div style="margin-top:32px; text-align:center;">
                <a
                    href="{{ config('app.frontend_url') }}"
                    style="display:inline-block; background:#2563eb; color:#ffffff; text-decoration:none; padding:12px 20px; border-radius:8px; font-weight:bold;"
                >
                    Voir le site
                </a>
            </div>
        </div>

        <!-- FOOTER -->
        <div style="padding:24px 32px; background:#f9fafb; border-top:1px solid #e5e7eb; text-align:center;">
            <p style="margin:0 0 10px; font-size:13px; color:#6b7280;">
                Vous recevez cet email car vous êtes inscrit à la newsletter.
            </p>

            <a
                href="{{ $unsubscribeUrl }}"
                style="font-size:13px; color:#dc2626; text-decoration:none;"
            >
                Se désinscrire
            </a>
        </div>

    </div>

</body>
</html>