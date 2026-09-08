<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <title>Сброс пароля</title>
    <style>
        body, .body-root {
            margin: 0 !important;
            padding: 0 !important;
            background-color: #f8fafc !important;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif !important;
            -webkit-font-smoothing: antialiased !important;
            width: 100% !important;
        }

        table {
            border-collapse: collapse;
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
                border-radius: 6px !important;
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
            .reset-button {
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
                                                <td style="width: 48px; height: 48px; border-radius: 12px; background-color: rgba(255,255,255,0.18); text-align: center; vertical-align: middle;" width="48" height="48" valign="middle" align="center">
                                                    <img src="{{ asset('img/logo.svg') }}" width="28" height="28" alt="Логотип" style="display: block; margin: 0 auto; border: 0;">
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="padding-left: 16px; vertical-align: middle;" valign="middle">
                                        <h2 style="margin: 0; color: #ffffff; font-size: 20px; font-weight: 600; letter-spacing: -0.5px;">
                                            Сброс пароля
                                        </h2>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td class="container-padding" style="padding: 40px; text-align: center;">

                            <p style="margin: 0 0 8px 0; color: #0f172a; font-size: 17px; font-weight: 600;">
                                Здравствуйте, {{ $user->name }}!
                            </p>

                            <p style="margin: 0 0 28px 0; color: #334155; font-size: 15px; line-height: 1.6;">
                                Мы получили запрос на сброс пароля для вашей учётной записи. Нажмите кнопку ниже, чтобы задать новый пароль. Если вы не запрашивали сброс — просто проигнорируйте это письмо.
                            </p>

                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="margin: 0 auto 20px auto;">
                                <tr>
                                    <td style="border-radius: 8px; background-color: #10b981; background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);">
                                        <a href="{{ $url }}" class="reset-button" style="display: inline-block; padding: 14px 32px; font-size: 15px; font-weight: 600; color: #ffffff !important; text-decoration: none; border-radius: 8px;">
                                            Сбросить пароль
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 24px;">
                                <tr>
                                    <td style="background-color: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 12px 16px; color: #92400e; font-size: 13px; text-align: left;">
                                        Ссылка действительна в течение {{ $count ?? 60 }} минут.
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 8px;">
                                <tr>
                                    <td style="padding-bottom: 6px; font-size: 12px; font-weight: 700; color: #059669; text-transform: uppercase; letter-spacing: 0.5px; text-align: left;">Ссылка для сброса</td>
                                </tr>
                                <tr>
                                    <td style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 16px; color: #334155; font-size: 13px; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; word-break: break-all; text-align: left;">
                                        <a href="{{ $url }}" style="color: #059669; text-decoration: none;">{{ $url }}</a>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <tr>
                        <td class="footer-padding" style="background-color: #f8fafc; padding: 24px 40px; text-align: center; border-top: 1px solid #edf2f7;">
                            <p style="margin: 0 0 6px 0; font-size: 12px; color: #94a3b8; line-height: 1.5;">
                                Если вы не запрашивали сброс пароля, никаких действий предпринимать не нужно.
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #94a3b8; line-height: 1.5;">
                                &copy; {{ date('Y') }} Ваша Компания. Все права защищены.
                            </p>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
</body>
</html>
