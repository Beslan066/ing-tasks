<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>МенеджерПлюс — современная система управления командами</title>
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Менеджер+" />
    <link rel="manifest" href="/site.webmanifest" />
    <meta name="description" content="Современное проектное управление. Agile-доски, корпоративный чат, всё в онлайн и мобильных приложениях." />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap');
        * { font-family: 'Inter', -apple-system, system-ui, sans-serif; }

        body {
            background: #f8fafc;
            color: #1e293b;
        }

        .gradient-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        .gradient-text-primary {
            background: linear-gradient(135deg, #10b981, #059669);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .btn-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            box-shadow: 0 8px 30px rgba(16, 185, 129, 0.35);
            transform: translateY(-2px);
        }

        .btn-outline {
            border: 1.5px solid #e2e8f0;
            color: #475569;
            transition: all 0.3s ease;
        }
        .btn-outline:hover {
            border-color: #10b981;
            color: #10b981;
            background: rgba(16, 185, 129, 0.05);
        }

        /* Стиль для фичей как на скриншоте — иконка слева, текст справа */
        .feature-row {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 20px;
            background: white;
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: default;
        }
        .feature-row:hover {
            border-color: #10b981;
            box-shadow: 0 4px 16px -6px rgba(16, 185, 129, 0.15);
            transform: translateY(-2px);
        }
        .feature-row .icon {
            width: 44px;
            height: 44px;
            min-width: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            flex-shrink: 0;
        }
        .feature-row .icon svg {
            width: 22px;
            height: 22px;
        }
        .feature-row .info {
            flex: 1;
        }
        .feature-row .info .title {
            font-weight: 600;
            font-size: 15px;
            color: #0f172a;
            line-height: 1.3;
        }
        .feature-row .info .desc {
            font-size: 13px;
            color: #64748b;
            margin-top: 2px;
            line-height: 1.4;
        }

        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
            border: 1px solid #f1f5f9;
        }
        .card-hover:hover {
            transform: translateY(-6px);
            border-color: #10b981;
            box-shadow: 0 20px 40px -12px rgba(16, 185, 129, 0.2);
        }

        .image-placeholder {
            background: #f1f4f9;
            border: 2px dashed #dce3ef;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 300px;
            transition: all 0.3s ease;
        }
        .image-placeholder:hover {
            border-color: #10b981;
            background: #f8fafc;
        }
        .image-placeholder .placeholder-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            color: #94a3b8;
        }
        .image-placeholder .placeholder-content svg {
            width: 56px;
            height: 56px;
            stroke: #10b981;
            opacity: 0.5;
        }
        .image-placeholder .placeholder-content span {
            font-size: 14px;
            font-weight: 500;
            color: #64748b;
            background: white;
            padding: 6px 20px;
            border-radius: 40px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .pricing-card {
            background: white;
            border: 1px solid #f1f5f9;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .pricing-card:hover {
            transform: scale(1.02);
            border-color: #10b981;
        }
        .pricing-card.popular {
            border: 2px solid #10b981;
            position: relative;
        }
        .pricing-card.popular .badge {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .faq-item {
            border-bottom: 1px solid #f1f5f9;
        }
        .faq-item:last-child {
            border-bottom: none;
        }

        .testimonial-card {
            background: white;
            border: 1px solid #f1f5f9;
            transition: all 0.3s ease;
        }
        .testimonial-card:hover {
            border-color: #10b981;
            box-shadow: 0 12px 30px -12px rgba(16, 185, 129, 0.12);
        }

        .stat-number {
            background: linear-gradient(135deg, #10b981, #059669);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .container-wide {
            max-width: 1440px;
            margin-left: auto;
            margin-right: auto;
            padding-left: 24px;
            padding-right: 24px;
        }

        @media (min-width: 640px) {
            .container-wide { padding-left: 40px; padding-right: 40px; }
        }
        @media (min-width: 1024px) {
            .container-wide { padding-left: 60px; padding-right: 60px; }
        }

        input, textarea, select {
            background: white;
            border: 1px solid #e2e8f0;
            color: #1e293b;
        }
        input:focus, textarea:focus, select:focus {
            border-color: #10b981;
            outline: none;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
        }
        input::placeholder, textarea::placeholder {
            color: #94a3b8;
        }
    </style>
</head>
<body>

<!-- ===== HEADER ===== -->
<header class="fixed top-0 w-full bg-white/90 backdrop-blur-md border-b border-gray-100/80 z-50">
    <div class="container-wide">
        <div class="flex items-center justify-between h-16">
            <a href="#" class="flex items-center gap-3 group">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white font-bold text-sm" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <img src="{{asset('img/logo.svg')}}" alt="">
                </div>
                <span class="text-xl font-bold text-gray-800 tracking-tight">Менеджер<span style="background: linear-gradient(135deg, #10b981, #059669); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Плюс</span></span>
            </a>

            <nav class="hidden lg:flex items-center gap-8">
                <a href="#" class="text-sm font-medium text-gray-600 hover:text-[#10b981] transition">Новости</a>
                <a href="#" class="text-sm font-medium text-gray-600 hover:text-[#10b981] transition">Документы</a>
                <a href="#" class="text-sm font-medium text-gray-600 hover:text-[#10b981] transition">О нас</a>
                <a href="#" class="text-sm font-medium text-gray-600 hover:text-[#10b981] transition">Контакты</a>
            </nav>

            <div class="flex items-center gap-3">
                <a href="{{route('login')}}" class="hidden sm:inline-block text-sm font-medium text-gray-600 hover:text-[#10b981] transition px-4 py-2 rounded-lg">Войти</a>
                <a href="{{route('register')}}" class="btn-primary text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-lg shadow-[#10b981]/25">
                    Начать
                </a>
            </div>
        </div>
    </div>
</header>

<main class="pt-16">

    @yield('content')

</main>

<!-- ===== ПОДВАЛ ===== -->
<footer class="bg-white border-t border-gray-100">
    <div class="container-wide py-12">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="col-span-2 md:col-span-1">
                <a href="#" class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white font-bold text-xs" style="background: linear-gradient(135deg, #10b981, #059669);">
                        МП
                    </div>
                    <span class="text-lg font-bold text-gray-800">Менеджер<span style="background: linear-gradient(135deg, #10b981, #059669); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Плюс</span></span>
                </a>
                <p class="mt-4 text-sm text-gray-400 max-w-xs">Современная система управления командами и задачами.</p>
            </div>
            <div>
                <h4 class="font-semibold text-gray-800 text-sm">Продукт</h4>
                <ul class="mt-3 space-y-2 text-sm text-gray-400">
                    <li><a href="#" class="hover:text-[#10b981] transition">Новости</a></li>
                    <li><a href="#" class="hover:text-[#10b981] transition">Документы</a></li>
                    <li><a href="#" class="hover:text-[#10b981] transition">Тарифы</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-gray-800 text-sm">Компания</h4>
                <ul class="mt-3 space-y-2 text-sm text-gray-400">
                    <li><a href="#" class="hover:text-[#10b981] transition">О нас</a></li>
                    <li><a href="#" class="hover:text-[#10b981] transition">Блог</a></li>
                    <li><a href="#" class="hover:text-[#10b981] transition">Контакты</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-gray-800 text-sm">Подписка</h4>
                <p class="mt-3 text-sm text-gray-400">Подпишитесь, чтобы получать последние новости и обновления.</p>
                <div class="mt-3 flex">
                    <input type="email" placeholder="Email" class="flex-1 px-4 py-2.5 rounded-l-xl border border-r-0 border-gray-200 bg-white focus:border-[#10b981] focus:ring-2 focus:ring-[#10b981]/20 outline-none transition text-gray-800 placeholder:text-gray-400 text-sm" />
                    <button class="btn-primary text-white px-4 py-2.5 rounded-r-xl">
                        <i data-lucide="send" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-100 mt-8 pt-6 flex flex-col sm:flex-row justify-between items-center text-sm text-gray-400">
            <span>© 2026 МенеджерПлюс. Все права защищены.</span>
            <div>
                <a href="{{route('licence')}}">Лицензионное соглашение / Оферта</a>
                <a href="{{route('privacy')}}">Политика конфиденциальности</a>
                <a href="{{route('terms')}}">Правила использования сервиса</a>
            </div>
            <div class="flex gap-4 mt-3 sm:mt-0">
                <a href="#" class="hover:text-[#10b981] transition"><i data-lucide="twitter" class="w-4 h-4"></i></a>
                <a href="#" class="hover:text-[#10b981] transition"><i data-lucide="github" class="w-4 h-4"></i></a>
                <a href="#" class="hover:text-[#10b981] transition"><i data-lucide="youtube" class="w-4 h-4"></i></a>
                <a href="#" class="hover:text-[#10b981] transition"><i data-lucide="linkedin" class="w-4 h-4"></i></a>
            </div>
        </div>
    </div>
</footer>

<script>
    lucide.createIcons();
</script>

</body>
</html>
