<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <title>Ответ на ваше обращение</title>
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
            .reply-button {
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
                            <h2 style="margin: 0; color: #ffffff; font-size: 20px; font-weight: 600; letter-spacing: -0.5px;">
                                Ответ на ваше обращение
                            </h2>
                        </td>
                    </tr>

                    <tr>
                        <td class="container-padding" style="padding: 40px;">

                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 24px;">
                                <tr>
                                    <td style="background-color: #f8fafc; border-left: 4px solid #10b981; border-radius: 8px; padding: 14px 16px; color: #334155; font-size: 14px; line-height: 1.7;">
                                        <div><strong style="color: #0f172a;">Тема:</strong> {{ $ticket->subject }}</div>
                                        <div><strong style="color: #0f172a;">Дата обращения:</strong> {{ $ticket->created_at->format('d.m.Y H:i') }}</div>
                                        <div><strong style="color: #0f172a;">Номер обращения:</strong> #{{ $ticket->id }}</div>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 24px;">
                                <tr>
                                    <td style="padding-bottom: 6px; font-size: 12px; font-weight: 700; color: #059669; text-transform: uppercase; letter-spacing: 0.5px;">{{ $adminName }} ответил(а)</td>
                                </tr>
                                <tr>
                                    <td style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; color: #334155; font-size: 15px; line-height: 1.6; white-space: pre-wrap; word-break: break-word;">{{ $reply->message }}</td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 24px 0; color: #64748b; font-size: 14px; line-height: 1.6;">
                                Вы можете ответить на это письмо, чтобы продолжить диалог.
                            </p>

                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-top: 1px solid #edf2f7; padding-top: 20px;">
                                <tr>
                                    <td style="padding-bottom: 6px; font-size: 12px; font-weight: 700; color: #059669; text-transform: uppercase; letter-spacing: 0.5px;">Ваше сообщение</td>
                                </tr>
                                <tr>
                                    <td style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; color: #64748b; font-size: 14px; line-height: 1.6; white-space: pre-wrap; word-break: break-word;">{{ $ticket->message }}</td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <tr>
                        <td class="footer-padding" style="background-color: #f8fafc; padding: 24px 40px; text-align: center; border-top: 1px solid #edf2f7;">
                            <p style="margin: 0; font-size: 12px; color: #94a3b8; line-height: 1.5;">
                                © {{ date('Y') }} Служба поддержки. Все права защищены.
                            </p>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
</body>
</html>
