<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <title>Новое обращение в поддержку</title>
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
            .responsive-table {
                width: 100% !important;
            }
            .responsive-table,
            .responsive-table tbody,
            .responsive-table tr {
                display: block !important;
                width: 100% !important;
            }
            .responsive-cell {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
                box-sizing: border-box !important;
            }
            .mobile-margin {
                margin-bottom: 16px !important;
            }
            .mobile-align-left {
                text-align: left !important;
                display: block !important;
                width: 100% !important;
            }
            .mobile-block-margin {
                margin-bottom: 8px !important;
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
                                Новое обращение в поддержку
                            </h2>
                        </td>
                    </tr>

                    <tr>
                        <td class="container-padding" style="padding: 40px;">

                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 24px;">
                                <tr>
                                    <td style="padding-bottom: 6px; font-size: 12px; font-weight: 700; color: #059669; text-transform: uppercase; letter-spacing: 0.5px;">Отправитель</td>
                                </tr>
                                <tr>
                                    <td style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 16px; color: #334155; font-size: 15px;">
                                        <strong style="color: #0f172a;">{{ $ticketData['name'] ?? 'Не указан' }}</strong>
                                        <span style="color: #64748b; font-size: 14px; margin-left: 4px;">({{ $ticketData['email'] ?? 'Не указан' }})</span>
                                    </td>
                                </tr>
                            </table>

                            <table class="responsive-table" role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 24px;">
                                <tr>
                                    <td class="responsive-cell mobile-margin" valign="top" style="padding-right: 12px;">
                                        <div style="padding-bottom: 6px; font-size: 12px; font-weight: 700; color: #059669; text-transform: uppercase; letter-spacing: 0.5px;">Тема</div>
                                        <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 16px; color: #0f172a; font-size: 15px; font-weight: 600; word-break: break-word;">
                                            {{ $ticketData['subject'] ?? 'Без темы' }}
                                        </div>
                                    </td>
                                    <td class="responsive-cell" valign="top" width="140" style="padding-left: 12px;">
                                        <div style="padding-bottom: 6px; font-size: 12px; font-weight: 700; color: #059669; text-transform: uppercase; letter-spacing: 0.5px;">ID Обращения</div>
                                        <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 16px; color: #334155; font-size: 15px; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-weight: 600; word-break: break-word;">
                                            #{{ $ticketData['ticket_id'] ?? 'Нет' }}
                                        </div>
                                    </td>
                                </tr>
                            </table>


                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 24px;">
                                <tr>
                                    <td style="padding-bottom: 6px; font-size: 12px; font-weight: 700; color: #059669; text-transform: uppercase; letter-spacing: 0.5px;">Сообщение</td>
                                </tr>
                                <tr>
                                    <td style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; color: #334155; font-size: 15px; line-height: 1.6; white-space: pre-wrap; word-break: break-word;">{{ $ticketData['message'] ?? 'Нет сообщения' }}</td>
                                </tr>
                            </table>

                            @if(!empty($ticketData['attachment_original_name']))
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 24px;">
                                <tr>
                                    <td style="padding-bottom: 6px; font-size: 12px; font-weight: 700; color: #059669; text-transform: uppercase; letter-spacing: 0.5px;">Вложение</td>
                                </tr>
                                <tr>
                                    <td style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 14px 16px; color: #1e293b; font-size: 14px; word-break: break-word;">
                                        <span style="font-weight: 600; color: #166534;">{{ $ticketData['attachment_original_name'] }}</span>
                                        <span style="color: #64748b; font-size: 12px; margin-left: 8px;">({{ $ticketData['attachment_size'] ?? '0 B' }})</span>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-top: 1px solid #edf2f7; padding-top: 20px; margin-bottom: 32px;">
                                <tr>
                                    <td class="mobile-align-left mobile-block-margin" style="color: #64748b; font-size: 13px; padding-top: 20px;">
                                        <span style="color: #94a3b8;">Время отправки:</span> {{ now()->format('d.m.Y H:i:s') }}
                                    </td>
                                    <td class="mobile-align-left" align="right" style="color: #64748b; font-size: 13px; padding-top: 20px;">
                                        <span style="color: #94a3b8;">IP адрес:</span> {{ $ticketData['user_ip'] ?? 'Не определен' }}
                                    </td>
                                </tr>
                            </table>

                            @if(!empty($ticketData['email']))
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <a href="mailto:{{ $ticketData['email'] }}" class="reply-button" style="background-color: #f3f4f6; border: 1px solid #e5e7eb; color: #1f2937; font-size: 14px; font-weight: 500; text-decoration: none; padding: 12px 28px; border-radius: 8px; display: inline-block;">
                                            Ответить автору на email
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            @endif

                        </td>
                    </tr>

                    <tr>
                        <td class="footer-padding" style="background-color: #f8fafc; padding: 24px 40px; text-align: center; border-top: 1px solid #edf2f7;">
                            <p style="margin: 0 0 6px 0; font-size: 12px; color: #94a3b8; line-height: 1.5;">
                                Это автоматическое уведомление. Пожалуйста, не отвечайте на это письмо.
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #94a3b8; line-height: 1.5; word-break: break-word;">
                                Чтобы связаться с пользователем, используйте адрес: {{ $ticketData['email'] ?? 'не указан' }}
                            </p>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
</body>
</html>
