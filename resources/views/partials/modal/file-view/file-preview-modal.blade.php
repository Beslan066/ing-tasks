<div id="file-preview-modal"
     class="fixed inset-0 z-[52] hidden items-center justify-center bg-black/80 p-4"
>
    <button id="file-preview-close"
            class="absolute right-5 top-5 text-white hover:text-gray-300"
            aria-label="Закрыть">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M6 6L18 18M6 18L18 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
    </button>

    <img id="file-preview-img" src="" alt=""
         class="max-h-[90vh] max-w-[90vw] rounded shadow-lg object-contain">

    <p id="file-preview-name"
       class="absolute bottom-5 left-1/2 -translate-x-1/2 text-sm text-gray-300"></p>
</div>
