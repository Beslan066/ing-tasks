<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <title>Приглашение в компанию</title>
   <style>
        body, .body-root {
            margin: 0 !important;
            padding: 0 !important;
            background-color: #f8fafc !important;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif !important;
            -webkit-font-smoothing: antialiased !important;
            width: 100% !important;
        }

        img {
            max-width: 100%;
        }

        @media screen and (max-width: 500px) {
            .outer-padding {
                padding: 20px 12px !important;
            }
            .email-container {
                width: 100% !important;
                max-width: 100% !important;
                border-radius: 0 !important;
                border-left: none !important;
                border-right: none !important;
            }
            .header-padding {
                padding: 24px 20px !important;
            }
            .header-padding h2 {
                font-size: 18px !important;
            }
            .container-padding {
                padding: 24px 20px !important;
            }
            .footer-padding {
                padding: 20px 20px !important;
            }
            .invite-button {
                display: block !important;
                width: 100% !important;
                box-sizing: border-box !important;
                text-align: center !important;
            }
        }
    </style>
</head>
<body class="body-root">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc;">
        <tr>
            <td align="center" class="outer-padding" style="padding: 40px 20px;">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" class="email-container" style="max-width: 600px; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);">

                    <tr>
                        <td class="header-padding" style="background-color: #10b981; background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 32px 40px; text-align: left;">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="width: 56px; vertical-align: middle;" valign="middle">
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="width: 48px; height: 48px; border-radius: 12px; background-color: #ffffff; background-color: rgba(255,255,255,0.18); text-align: center; vertical-align: middle;" width="48" height="48" valign="middle" align="center">
                                                    <img src="{{ asset('img/logo.svg') }}" width="28" height="28" alt="Логотип" style="display: block; margin: 0 auto; border: 0;">
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="padding-left: 16px; vertical-align: middle;" valign="middle">
                                        <h2 style="margin: 0; color: #ffffff; font-size: 20px; font-weight: 600; letter-spacing: -0.5px;">
                                            Приглашение в компанию
                                        </h2>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td class="container-padding" style="padding: 40px;">

                            <p style="margin: 0 0 16px 0; color: #334155; font-size: 15px; line-height: 1.6;">
                                Здравствуйте!
                            </p>

                            <p style="margin: 0 0 24px 0; color: #334155; font-size: 15px; line-height: 1.6;">
                                <strong style="color: #0f172a;">{{ $inviterName }}</strong> приглашает вас присоединиться к компании <strong style="color: #0f172a;">{{ $companyName }}</strong>.
                            </p>

                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 24px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $invitationUrl }}" class="invite-button" style="background-color: #10b981; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; font-size: 15px; font-weight: 600; text-decoration: none; padding: 14px 32px; border-radius: 8px; display: inline-block;">
                                            Принять приглашение
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 24px;">
                                <tr>
                                    <td style="background-color: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 14px 16px; color: #92400e; font-size: 14px;">
                                        Ссылка действительна до: <strong>{{ $expiresAt }}</strong>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 24px 0; color: #64748b; font-size: 13px; line-height: 1.6;">
                                Если вы не ожидали этого приглашения, просто проигнорируйте это письмо — никаких действий предпринимать не нужно.
                            </p>

                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-top: 1px solid #edf2f7; padding-top: 16px;">
                                <tr>
                                    <td style="color: #94a3b8; font-size: 12px; line-height: 1.6; word-break: break-all;">
                                        Если кнопка не работает, скопируйте и вставьте эту ссылку в браузер:<br>
                                        <a href="{{ $invitationUrl }}" style="color: #059669; text-decoration: underline;">{{ $invitationUrl }}</a>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <tr>
                        <td class="footer-padding" style="background-color: #f8fafc; padding: 24px 40px; text-align: center; border-top: 1px solid #edf2f7;">
                            <p style="margin: 0; font-size: 12px; color: #94a3b8; line-height: 1.5;">
                                С уважением, {{ config('app.name') }}
                            </p>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
</body>
</html>
