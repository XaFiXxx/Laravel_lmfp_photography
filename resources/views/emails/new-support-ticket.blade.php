<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouveau ticket support</title>
</head>
<body style="margin:0; padding:0; background:#f3f4f6; font-family:Arial, Helvetica, sans-serif;">

    <div style="width:100%; background:#f3f4f6; padding:32px 16px;">
        <div style="max-width:600px; margin:0 auto; background:#ffffff; border-radius:16px; overflow:hidden; border:1px solid #e5e7eb;">

            <!-- HEADER -->
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
                    Support
                </p>

                <h1 style="
                    margin:10px 0 0;
                    font-size:22px;
                    color:#ffffff;
                    font-weight:600;
                ">
                    Nouveau ticket
                </h1>
            </div>

            <!-- CONTENT -->
            <div style="padding:36px 32px;">
                <h2 style="margin:0 0 18px; font-size:22px; line-height:1.4; font-weight:700; color:#111827;">
                    Un utilisateur a créé un ticket support
                </h2>

                <div style="font-size:15px; line-height:1.8; color:#4b5563;">

                    <p style="margin:0 0 12px;">
                        <strong>Ticket :</strong> #{{ $conversation->id }}
                    </p>

                    <p style="margin:0 0 12px;">
                        <strong>Utilisateur :</strong><br>
                        {{ $conversation->user?->firstname }} {{ $conversation->user?->lastname }}<br>
                        {{ $conversation->user?->email }}
                    </p>

                    <p style="margin:16px 0 8px;">
                        <strong>Message :</strong>
                    </p>

                    <div style="
                        padding:16px;
                        background:#f3f4f6;
                        border-radius:10px;
                        font-size:14px;
                        line-height:1.6;
                        color:#111827;
                        white-space:pre-line;
                    ">
                        {{ $supportMessage->message }}
                    </div>
                </div>

                <!-- CTA -->
                <div style="margin-top:32px; text-align:center;">
                    <a
                        href="{{ $chatUrl }}"
                        style="display:inline-block; background:#111827; color:#ffffff; text-decoration:none; padding:14px 24px; border-radius:999px; font-size:14px; font-weight:600;"
                    >
                        Voir le ticket
                    </a>
                </div>
            </div>

            <!-- FOOTER -->
            <div style="border-top:1px solid #e5e7eb; background:#f9fafb; padding:24px 32px; text-align:center;">
                <p style="margin:0 0 12px; font-size:13px; line-height:1.6; color:#6b7280;">
                    Notification automatique du système de support LMFP Photography.
                </p>

                <p style="margin:16px 0 0; font-size:12px; color:#9ca3af;">
                    © {{ date('Y') }} LMFP Photography
                </p>
            </div>

        </div>
    </div>

</body>
</html>