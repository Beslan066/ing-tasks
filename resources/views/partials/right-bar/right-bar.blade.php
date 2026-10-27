<aside class="right-bar" aria-label="Быстрые действия">
    <button type="button" class="right-bar__handle" aria-label="Быстрые действия">
        <i class="fas fa-chevron-left" aria-hidden="true"></i>
    </button>

    <div class="right-bar__inner">
        <a href="{{ route('chat.index') }}"
           class="right-bar__item"
           style="--accent: 52 211 153"
           data-label="Чат"
           aria-label="Чат">
            <i class="fas fa-comments" aria-hidden="true"></i>
        </a>

        <button type="button"
                class="right-bar__item"
                style="--accent: 244 114 182"
                data-label="Фон"
                aria-label="Сменить фон"
                onclick="openBackgroundSelector()">
            <i class="fas fa-image" aria-hidden="true"></i>
        </button>
    </div>
</aside>

@once
<style>
    .right-bar {
        position: fixed;
        z-index: 40;
        top: 70px;
        right: 0;
        transform: translateX(100%);
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .right-bar:hover,
    .right-bar:focus-within {
        transform: translateX(0);
    }

    .right-bar__inner {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        padding: 12px 10px;
        border: 1px solid rgb(255 255 255 / 0.08);
        border-right: none;
        border-radius: 16px 0 0 16px;
        background: #1e293b;
        box-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.3);
    }
    .right-bar__handle {
        position: absolute;
        top: 50%;
        left: -20px;
        transform: translateY(-50%);
        width: 20px;
        height: 40px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgb(255 255 255 / 0.08);
        border-right: none;
        border-radius: 10px 0 0 10px;
        background: #1e293b;
        color: #94a3b8;
        cursor: pointer;
        transition: color 0.2s ease;
    }

    .right-bar__handle i {
        font-size: 11px;
        transition: transform 0.25s ease;
    }

    .right-bar:hover .right-bar__handle,
    .right-bar:focus-within .right-bar__handle {
        color: rgb(var(--accent, 52 211 153));
    }

    .right-bar:hover .right-bar__handle i,
    .right-bar:focus-within .right-bar__handle i {
        transform: rotate(180deg);
    }

    .right-bar__item {
        --accent: 52 211 153;
        position: relative;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: transparent;
        color: #94a3b8;
        font-size: 18px;
        cursor: pointer;
        transition: transform 0.2s ease, color 0.2s ease, background-color 0.2s ease;
    }

    .right-bar__item:hover,
    .right-bar__item:focus-visible {
        outline: none;
        transform: scale(1.05);
        background: rgb(var(--accent) / 0.1);
        color: rgb(var(--accent));
    }

    .right-bar__item:active {
        transform: scale(0.95);
    }

    .right-bar__item::after {
        content: attr(data-label);
        position: absolute;
        right: calc(100% + 14px);
        top: 50%;
        transform: translateY(-50%) translateX(4px);
        padding: 4px 8px;
        border-radius: 6px;
        background: #0f172a;
        color: #f1f5f9;
        font-size: 12px;
        font-weight: 500;
        white-space: nowrap;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.15s ease, transform 0.15s ease;
    }

    .right-bar__item:hover::after,
    .right-bar__item:focus-visible::after {
        opacity: 1;
        transform: translateY(-50%) translateX(0);
    }
    .main-container.has-background .right-bar__inner,
    .main-container.has-background .right-bar__handle {
        background: rgb(30 41 59 / 0.6);
        -webkit-backdrop-filter: blur(12px);
        backdrop-filter: blur(12px);
    }

    @media (max-width: 768px) {
        .right-bar {
            display: none;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .right-bar,
        .right-bar__item,
        .right-bar__handle i {
            transition: none;
        }
    }
</style>
@endonce
