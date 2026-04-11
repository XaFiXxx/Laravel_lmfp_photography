<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $subjectLine }}</title>
</head>
<body style="margin:0; padding:0; background:#f3f4f6; font-family:Arial, Helvetica, sans-serif;">

    <div style="width:100%; background:#f3f4f6; padding:32px 16px;">
        <div style="max-width:600px; margin:0 auto; background:#ffffff; border-radius:16px; overflow:hidden; border:1px solid #e5e7eb;">

            <!-- HEADER SOMBRE -->
            <div style="background:#0f172a; text-align:center; padding:32px 24px;">
                <img
                    src="{{ config('app.url') }}/storage/images/front/logo_front2.png"
                    alt="LMFP Photography"
                    style="max-height:60px; margin-bottom:12px;"
                />

                <p style="
                    margin:0;
                    font-size:11px;
                    letter-spacing:0.2em;
                    text-transform:uppercase;
                    color:#cbd5e1;
                ">
                    Newsletter
                </p>

                <h1 style="
                    margin:10px 0 0;
                    font-size:22px;
                    color:#ffffff;
                    font-weight:600;
                ">
                    LMFP Photography
                </h1>
            </div>

            <!-- CONTENT -->
            <div style="padding:36px 32px;">
                <h2 style="margin:0 0 18px; font-size:24px; line-height:1.4; font-weight:700; color:#111827;">
                    {{ $subjectLine }}
                </h2>

                <div style="font-size:15px; line-height:1.8; color:#4b5563; white-space:pre-line;">
                    {{ $contentText }}
                </div>

                <!-- CTA -->
                <div style="margin-top:32px; text-align:center;">
                    <a
                        href="{{ config('app.frontend_url') }}"
                        style="display:inline-block; background:#111827; color:#ffffff; text-decoration:none; padding:14px 24px; border-radius:999px; font-size:14px; font-weight:600;"
                    >
                        Voir le site
                    </a>
                </div>
            </div>

            <!-- FOOTER -->
            <div style="border-top:1px solid #e5e7eb; background:#f9fafb; padding:24px 32px; text-align:center;">
                <p style="margin:0 0 12px; font-size:13px; line-height:1.6; color:#6b7280;">
                    Vous recevez cet email car vous êtes inscrit à la newsletter LMFP Photography.
                </p>

                <a
                    href="{{ $unsubscribeUrl }}"
                    style="font-size:13px; color:#dc2626; text-decoration:none; font-weight:600;"
                >
                    Se désinscrire
                </a>

                <p style="margin:16px 0 0; font-size:12px; color:#9ca3af;">
                    © {{ date('Y') }} LMFP Photography
                </p>
            </div>

        </div>
    </div>

</body>
</html>