<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход на сайт</title>
</head>
<body>
    <form method="POST" action="{{ route('site-password.check') }}">
        @csrf
        <input type="password" name="password" placeholder="Пароль" autofocus>
        <button type="submit">Войти</button>

        @error('password')
            <div style="color: red;">{{ $message }}</div>
        @enderror
    </form>
</body>
</html>
