@component('mail::message')
    # Приглашение в компанию

    Здравствуйте!

    {{ $inviterName }} приглашает вас присоединиться к компании **{{ $companyName }}**.

    @component('mail::button', ['url' => $invitationUrl])
        Принять приглашение
    @endcomponent

    Ссылка действительна до: {{ $expiresAt }}

    Если вы не ожидали этого приглашения, просто проигнорируйте это письмо.

    С уважением,<br>
    {{ config('app.name') }}
@endcomponent
