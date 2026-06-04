<div class="top-menu">
    <div class="top-menu__inner">
        <nav class="top-menu__navigation">
            <ul>
                <li>
                    <a href="#" class="top-menu__link text-sidebar-text hover:text-white">
                        <span>
                            MenuItem
                        </span>
                    </a>
                </li>
                <li><a href="#" class="top-menu__link text-sidebar-text hover:text-white">
                        <span>
                            MenuItem
                        </span>
                    </a>
                </li>
                <li>
                    <a href="#" class="top-menu__link text-sidebar-text hover:text-white">
                        <span>
                            MenuItem
                        </span>
                    </a>
                </li>
                <li>
                    <a href="#" class="top-menu__link text-sidebar-text hover:text-white">
                        <span>
                            MenuItem
                        </span>
                    </a>
                </li>
            </ul>
        </nav>
        <div class="top-menu__right">
            <div class="flex items-center space-x-3 group">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center flex-shrink-0 shadow-lg group-hover:shadow-primary-500/20 transition-all duration-300">
                        <i class="fas fa-tasks text-white text-lg"></i>
                    </div>
                <a href="http://localhost:8000/home" class="logotype__text">
                    <h1 class="text-xl text-[16px] font-bold text-white">Менеджер<span class="text-primary-500">Плюс</span></h1>
                </a>
            </div>
             <a href="{{route('profile.edit')}}">
                 <div class="w-8 h-8 rounded-full overflow-hidden bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white font-bold shadow-lg">
                     <img class="rounded" src="https://ui-avatars.com/api/?name=%D0%98%D1%81%D0%BB%D0%B0%D0%BC+%D0%9F%D0%B0%D1%80%D1%87%D0%B8%D0%B5%D0%B2&amp;color=7F9CF5&amp;background=EBF4FF" alt="">
                    </div>
                </a>
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
         height: 50px;
        background: linear-gradient(to right, #1a1f2e 0%, #161b28 100%);
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
        gap: 1.5rem;
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
            margin-bottom: 20px;
        }
    }
</style>
@endonce
