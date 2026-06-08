@php
    $backgroundEnabled = auth()->check() && auth()->user()->background_enabled;
    $backgroundImage = auth()->check() ? auth()->user()->background_image : null;
    $company = auth()->check() ? auth()->user()->company : null;

@endphp


    @if($backgroundEnabled && $backgroundImage)
        <div class="top-menu bg-transparent/10">
    @else
        <div class="top-menu" style="background: linear-gradient(to right, #1a1f2e 0%, #161b28 100%);">
    @endif
    <div class="top-menu__inner">
        <nav class="top-menu__navigation">
            <ul>
                <li>
                    <a href="#" class="top-menu__link text-white">
                        <span class="font-medium">
                            Задачи
                        </span>
                    </a>
                </li>
                <li><a href="#" class="top-menu__link text-white">
                        <span class="font-medium">
                            Аналитика
                        </span>
                    </a>
                </li>
                <li>
                    <a href="#" class="top-menu__link text-white">
                        <span class="font-medium">
                            Отчеты
                        </span>
                    </a>
                </li>
                <li><a href="{{route('activity.index')}}" class="top-menu__link text-white">
                        <span class="font-medium">
                            Лента
                        </span>
                    </a>
                </li>
            </ul>
        </nav>
        <div class="top-menu__right">
            <div>
                @if($company->license_type !== 'premium')
                    <button onclick="openUpgradeModal()"
                            class="bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600
                            hover:to-yellow-700 text-white font-bold py-2 px-2  rounded-lg shadow-lg transition
                            duration-300 transform hover:scale-105 flex items-center text-sm md:text-base">
                        <i class="fas fa-crown"></i>
                        <span>Улучшить подписку</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                @else
                    <span
                        class="bg-gradient-to-r from-purple-500 to-pink-500 text-white font-bold py-2 px-2
                                gap-2 rounded-lg shadow-lg inline-flex items-center  text-sm md:text-base">
                        <i class="fas fa-check-circle"></i>
                        <span>Премиум</span>
                        <i class="fas fa-star"></i>
                    </span>
                @endif
            </div>
             <button type="button" class="w-10 h-10 rounded-lg overflow-hidden bg-gradient-to-br
                                    from-primary-500 to-primary-700 flex items-center justify-center
                                    text-white font-bold shadow-lg" onclick="userProfileModal()">
                 <img class="rounded" src="{{auth()->user()->getAvatarUrlAttribute()}}" alt="{{auth()->user()->name}}">
             </button>
        </div>
    </div>
</div>
@once
<style>
    .main-container.sidebar-mode-collapsed {
        .top-menu {
            padding-left: calc(3rem + 4.5rem);
        }
    }
    .top-menu {
        transition: padding 0.4s ease;
         padding-left: calc(3rem + 256px);

        width: 100%;
        height: 60px;
    }
    .top-menu__inner {
        height: 100%;
        display: flex;
        align-items: center;
        padding-right: 1.0rem;
    }
    .top-menu__right {
        display: flex;
        align-items: center;
        gap: 0.5rem;
            margin-left: auto;
    }
    .top-menu__navigation {
        ul {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
    }
    @media (max-width: 1020px) {
        .top-menu__navigation {
            display: none;
        }
    }
    @media (max-width: 768px) {
        .top-menu {
            padding-left: 1.5rem;
            margin-bottom: 3%0px;
        }
    }
    @media (max-width: 500px) {
        .top-menu {
            display: none !important;
        }
    }
</style>
@endonce
