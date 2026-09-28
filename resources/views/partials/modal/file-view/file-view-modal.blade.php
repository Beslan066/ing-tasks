<div id="file-view-modal"
     class="fixed inset-0 z-[999] hidden items-center justify-center bg-black/80 p-4">
    <button id="file-view-close"
            class="absolute right-5 top-5 text-white hover:text-gray-300"
            aria-label="Закрыть">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M6 6L18 18M6 18L18 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
    </button>

    <img id="file-view-img" src="" alt=""
         class="hidden max-h-[90vh] max-w-[90vw] rounded shadow-lg object-contain">

    <pre id="text-view-content"
         class="hidden max-h-[85vh] min-h-[50vh] w-full max-w-4xl overflow-auto whitespace-pre-wrap break-words rounded bg-white p-5 text-sm text-gray-800 shadow-lg dark:bg-gray-900 dark:text-gray-200"></pre>

    <p id="file-view-name"
       class="absolute bottom-5 left-1/2 -translate-x-1/2 text-sm text-gray-300"></p>
</div>
