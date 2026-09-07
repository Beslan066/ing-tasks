<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <title>Доступ отозван</title>
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
            .site-button {
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
                        <td class="header-padding" style="background-color: #dc2626; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); padding: 32px 40px; text-align: left;">
                            <h2 style="margin: 0; color: #ffffff; font-size: 20px; font-weight: 600; letter-spacing: -0.5px;">
                                Ваш доступ к компании был отозван
                            </h2>
                        </td>
                    </tr>

                    <tr>
                        <td class="container-padding" style="padding: 40px;">

                            <p style="margin: 0 0 16px 0; color: #334155; font-size: 15px; line-height: 1.6;">
                                Здравствуйте, {{ $userName }}!
                            </p>

                            <p style="margin: 0 0 24px 0; color: #334155; font-size: 15px; line-height: 1.6;">
                                Администратор компании <strong style="color: #0f172a;">{{ $companyName }}</strong> отозвал ваш доступ к системе таск-менеджера.
                            </p>

                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 24px;">
                                <tr>
                                    <td style="padding-bottom: 6px; font-size: 12px; font-weight: 700; color: #dc2626; text-transform: uppercase; letter-spacing: 0.5px;">Что это значит</td>
                                </tr>
                                <tr>
                                    <td style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px;">
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td style="color: #334155; font-size: 14px; line-height: 1.7; padding-bottom: 10px;">
                                                    &bull;&nbsp; Вы больше не сможете входить в систему таск-менеджера этой компании
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color: #334155; font-size: 14px; line-height: 1.7; padding-bottom: 10px;">
                                                    &bull;&nbsp; Ваши выполненные задачи остаются в истории компании
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color: #334155; font-size: 14px; line-height: 1.7;">
                                                    &bull;&nbsp; Ваши текущие невыполненные задачи будут переназначены
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 24px;">
                                <tr>
                                    <td style="background-color: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 14px 16px; color: #92400e; font-size: 14px; line-height: 1.6;">
                                        Если вы считаете, что это произошло по ошибке, пожалуйста, свяжитесь с администратором компании.
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <a href="{{ config('app.url') }}" class="site-button" style="background-color: #0f172a; color: #ffffff; font-size: 15px; font-weight: 600; text-decoration: none; padding: 14px 32px; border-radius: 8px; display: inline-block;">
                                            Перейти на сайт
                                        </a>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <tr>
                        <td class="footer-padding" style="background-color: #f8fafc; padding: 24px 40px; text-align: center; border-top: 1px solid #edf2f7;">
                            <p style="margin: 0; font-size: 12px; color: #94a3b8; line-height: 1.5;">
                                С уважением, команда {{ config('app.name') }}
                            </p>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
</body>
</html>
