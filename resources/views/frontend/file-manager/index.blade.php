@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-(--breakpoint-2xl) p-4 pb-20 md:p-6 md:pb-6">
        <!-- Breadcrumb Start -->
        <div x-data="{ pageName: `File Manager`}">
            <div class="flex flex-wrap items-center justify-between gap-3 pb-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90" x-text="pageName">File Manager</h2>
                <nav>
                    <ol class="flex items-center gap-1.5">
                        <li>
                            <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
                                href="index.html">
                                Home
                                <svg class="stroke-current" width="17" height="16" viewBox="0 0 17 16" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.0765 12.667L10.2432 8.50033L6.0765 4.33366" stroke="" stroke-width="1.2"
                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </a>
                        </li>
                        <li class="text-sm text-gray-800 dark:text-white/90" x-text="pageName">File Manager</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!-- Breadcrumb End -->

        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-12">
                <!-- Media Card -->
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="px-4 py-4 sm:pl-6 sm:pr-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                All Media
                            </h3>

                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                                <div class="relative">
                                    <button
                                        class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M3.04199 9.37363C3.04199 5.87693 5.87735 3.04199 9.37533 3.04199C12.8733 3.04199 15.7087 5.87693 15.7087 9.37363C15.7087 12.8703 12.8733 15.7053 9.37533 15.7053C5.87735 15.7053 3.04199 12.8703 3.04199 9.37363ZM9.37533 1.54199C5.04926 1.54199 1.54199 5.04817 1.54199 9.37363C1.54199 13.6991 5.04926 17.2053 9.37533 17.2053C11.2676 17.2053 13.0032 16.5344 14.3572 15.4176L17.1773 18.238C17.4702 18.5309 17.945 18.5309 18.2379 18.238C18.5308 17.9451 18.5309 17.4703 18.238 17.1773L15.4182 14.3573C16.5367 13.0033 17.2087 11.2669 17.2087 9.37363C17.2087 5.04817 13.7014 1.54199 9.37533 1.54199Z"
                                                fill=""></path>
                                        </svg>
                                    </button>

                                    <input type="text" placeholder="Search..."
                                        class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-[42px] pr-3.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-green-300 focus:outline-hidden focus:ring-3 focus:ring-green-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-green-800 xl:w-[300px]">
                                </div>

                                <button
                                    class="flex w-full items-center justify-center gap-2 rounded-lg bg-green-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs hover:bg-green-600 sm:w-auto">
                                    <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M9.2502 4.99951C9.2502 4.5853 9.58599 4.24951 10.0002 4.24951C10.4144 4.24951 10.7502 4.5853 10.7502 4.99951V9.24971H15.0006C15.4148 9.24971 15.7506 9.5855 15.7506 9.99971C15.7506 10.4139 15.4148 10.7497 15.0006 10.7497H10.7502V15.0001C10.7502 15.4143 10.4144 15.7501 10.0002 15.7501C9.58599 15.7501 9.2502 15.4143 9.2502 15.0001V10.7497H5C4.58579 10.7497 4.25 10.4139 4.25 9.99971C4.25 9.5855 4.58579 9.24971 5 9.24971H9.2502V4.99951Z"
                                            fill=""></path>
                                    </svg>

                                    Upload File
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="border-t border-gray-100 p-4 dark:border-gray-800 sm:p-6">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6 xl:grid-cols-3">
                            <!-- Media item-->
                            <div
                                class="flex items-center justify-between rounded-2xl border border-gray-100 bg-white py-4 pl-4 pr-4 dark:border-gray-800 dark:bg-white/[0.03] xl:pr-5">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="flex h-[52px] w-[52px] items-center justify-center rounded-xl bg-green-600/[0.08] text-green-500">
                                        <svg class="fill-current" width="20" height="18" viewBox="0 0 20 18" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M9.05 3.9L8.45 4.35L9.05 3.9ZM2.25 2.25H6.5V0.75H2.25V2.25ZM1.5 15V3H0V15H1.5ZM17.75 15.75H2.25V17.25H17.75V15.75ZM18.5 6V15H20V6H18.5ZM17.75 3.75H10.25V5.25H17.75V3.75ZM9.65 3.45L8.3 1.65L7.1 2.55L8.45 4.35L9.65 3.45ZM10.25 3.75C10.0139 3.75 9.79164 3.63885 9.65 3.45L8.45 4.35C8.87492 4.91656 9.5418 5.25 10.25 5.25V3.75ZM20 6C20 4.75736 18.9926 3.75 17.75 3.75V5.25C18.1642 5.25 18.5 5.58579 18.5 6H20ZM17.75 17.25C18.9926 17.25 20 16.2426 20 15H18.5C18.5 15.4142 18.1642 15.75 17.75 15.75V17.25ZM0 15C0 16.2426 1.00736 17.25 2.25 17.25V15.75C1.83579 15.75 1.5 15.4142 1.5 15H0ZM6.5 2.25C6.73607 2.25 6.95836 2.36115 7.1 2.55L8.3 1.65C7.87508 1.08344 7.2082 0.75 6.5 0.75V2.25ZM2.25 0.75C1.00736 0.75 0 1.75736 0 3H1.5C1.5 2.58579 1.83579 2.25 2.25 2.25V0.75Z"
                                                fill=""></path>
                                        </svg>
                                    </div>

                                    <div>
                                        <h4 class="mb-1 text-sm font-medium text-gray-800 dark:text-white/90">
                                            Image
                                        </h4>
                                        <span class="block text-sm text-gray-500 dark:text-gray-400">
                                            17% Used
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <span class="mb-1 block text-right text-sm text-gray-500 dark:text-gray-400">
                                        245 files
                                    </span>
                                    <span class="block text-right text-sm text-gray-500 dark:text-gray-400">
                                        26.40 GB
                                    </span>
                                </div>
                            </div>

                            <!-- Media item-->
                            <div
                                class="flex items-center justify-between rounded-2xl border border-gray-100 bg-white py-4 pl-4 pr-4 dark:border-gray-800 dark:bg-white/[0.03] xl:pr-5">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="flex h-[52px] w-[52px] items-center justify-center rounded-xl bg-green-600/[0.08] text-green-600">
                                        <svg class="stroke-current" width="25" height="24" viewBox="0 0 25 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M6.70825 5.93126L6.70825 18.0687C6.70825 19.2416 7.9937 19.9607 8.99315 19.347L18.8765 13.2783C19.83 12.6928 19.83 11.3072 18.8765 10.7217L8.99315 4.65301C7.9937 4.03931 6.70825 4.75844 6.70825 5.93126Z"
                                                stroke="" stroke-width="1.5" stroke-linejoin="round"></path>
                                        </svg>
                                    </div>

                                    <div>
                                        <h4 class="mb-1 text-sm font-medium text-gray-800 dark:text-white/90">
                                            Videos
                                        </h4>
                                        <span class="block text-sm text-gray-500 dark:text-gray-400">
                                            22% Used
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <span class="mb-1 block text-right text-sm text-gray-500 dark:text-gray-400">
                                        245 files
                                    </span>
                                    <span class="block text-right text-sm text-gray-500 dark:text-gray-400">
                                        26.40 GB
                                    </span>
                                </div>
                            </div>

                            <!-- Media item-->
                            <div
                                class="flex items-center justify-between rounded-2xl border border-gray-100 bg-white py-4 pl-4 pr-4 dark:border-gray-800 dark:bg-white/[0.03] xl:pr-5">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="flex h-[52px] w-[52px] items-center justify-center rounded-xl bg-blue-500/[0.08] text-blue-light-500">
                                        <svg class="fill-current" width="25" height="24" viewBox="0 0 25 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M21.4166 4.00001C21.4166 3.77689 21.3173 3.56536 21.1456 3.42287C20.9739 3.28039 20.7477 3.22173 20.5284 3.26285L8.52841 5.51285C8.17368 5.57936 7.91663 5.88909 7.91663 6.25001V9.98484C7.91644 9.99437 7.91644 10.0039 7.91663 10.0135V14.4585C7.3716 14.1636 6.72327 14 6.04163 14C5.16738 14 4.34794 14.2691 3.73094 14.7392C3.11333 15.2098 2.66663 15.9138 2.66663 16.75C2.66663 17.5862 3.11333 18.2902 3.73094 18.7608C4.34794 19.2309 5.16738 19.5 6.04163 19.5C6.91587 19.5 7.73532 19.2309 8.35231 18.7608C8.95774 18.2995 9.39893 17.6139 9.41611 16.7993C9.41645 16.79 9.41663 16.7806 9.41663 16.7712V16.75V10.62L19.9166 8.60938V12.2085C19.3716 11.9136 18.7233 11.75 18.0416 11.75C17.1674 11.75 16.3479 12.0191 15.7309 12.4892C15.1133 12.9598 14.6666 13.6638 14.6666 14.5C14.6666 15.3362 15.1133 16.0402 15.7309 16.5108C16.3479 16.9809 17.1674 17.25 18.0416 17.25C18.9159 17.25 19.7353 16.9809 20.3523 16.5108C20.9577 16.0495 21.3989 15.3639 21.4161 14.5493C21.4165 14.54 21.4166 14.5306 21.4166 14.5212V14.5V4.00001ZM19.9166 14.5C19.9166 14.2316 19.7757 13.9357 19.4432 13.6824C19.1102 13.4286 18.6171 13.25 18.0416 13.25C17.4661 13.25 16.9731 13.4286 16.64 13.6824C16.3076 13.9357 16.1666 14.2316 16.1666 14.5C16.1666 14.7684 16.3076 15.0643 16.64 15.3176C16.9731 15.5714 17.4661 15.75 18.0416 15.75C18.6171 15.75 19.1102 15.5714 19.4432 15.3176C19.7757 15.0643 19.9166 14.7684 19.9166 14.5ZM7.44325 15.9324C7.7757 16.1857 7.91663 16.4816 7.91663 16.75C7.91663 17.0184 7.7757 17.3143 7.44325 17.5676C7.11018 17.8214 6.61713 18 6.04163 18C5.46613 18 4.97307 17.8214 4.64 17.5676C4.30755 17.3143 4.16663 17.0184 4.16663 16.75C4.16663 16.4816 4.30755 16.1857 4.64 15.9324C4.97307 15.6786 5.46613 15.5 6.04163 15.5C6.61713 15.5 7.11018 15.6786 7.44325 15.9324ZM19.9166 7.08212V4.9037L9.41663 6.87245V9.09276L19.9166 7.08212Z"
                                                fill=""></path>
                                        </svg>
                                    </div>

                                    <div>
                                        <h4 class="mb-1 text-sm font-medium text-gray-800 dark:text-white/90">
                                            Audio
                                        </h4>
                                        <span class="block text-sm text-gray-500 dark:text-gray-400">
                                            24% Used
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <span class="mb-1 block text-right text-sm text-gray-500 dark:text-gray-400">
                                        245 files
                                    </span>
                                    <span class="block text-right text-sm text-gray-500 dark:text-gray-400">
                                        26.40 GB
                                    </span>
                                </div>
                            </div>

                            <!-- Media item-->
                            <div
                                class="flex items-center justify-between rounded-2xl border border-gray-100 bg-white py-4 pl-4 pr-4 dark:border-gray-800 dark:bg-white/[0.03] xl:pr-5">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="flex h-[52px] w-[52px] items-center justify-center rounded-xl bg-orange-500/[0.08] text-orange-500">
                                        <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M5.5 3.25C4.25736 3.25 3.25 4.25736 3.25 5.5V8.99998C3.25 10.2426 4.25736 11.25 5.5 11.25H9C10.2426 11.25 11.25 10.2426 11.25 8.99998V5.5C11.25 4.25736 10.2426 3.25 9 3.25H5.5ZM4.75 5.5C4.75 5.08579 5.08579 4.75 5.5 4.75H9C9.41421 4.75 9.75 5.08579 9.75 5.5V8.99998C9.75 9.41419 9.41421 9.74998 9 9.74998H5.5C5.08579 9.74998 4.75 9.41419 4.75 8.99998V5.5ZM5.5 12.75C4.25736 12.75 3.25 13.7574 3.25 15V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H9C10.2426 20.75 11.25 19.7427 11.25 18.5V15C11.25 13.7574 10.2426 12.75 9 12.75H5.5ZM4.75 15C4.75 14.5858 5.08579 14.25 5.5 14.25H9C9.41421 14.25 9.75 14.5858 9.75 15V18.5C9.75 18.9142 9.41421 19.25 9 19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5V15ZM12.75 5.5C12.75 4.25736 13.7574 3.25 15 3.25H18.5C19.7426 3.25 20.75 4.25736 20.75 5.5V8.99998C20.75 10.2426 19.7426 11.25 18.5 11.25H15C13.7574 11.25 12.75 10.2426 12.75 8.99998V5.5ZM15 4.75C14.5858 4.75 14.25 5.08579 14.25 5.5V8.99998C14.25 9.41419 14.5858 9.74998 15 9.74998H18.5C18.9142 9.74998 19.25 9.41419 19.25 8.99998V5.5C19.25 5.08579 18.9142 4.75 18.5 4.75H15ZM15 12.75C13.7574 12.75 12.75 13.7574 12.75 15V18.5C12.75 19.7426 13.7574 20.75 15 20.75H18.5C19.7426 20.75 20.75 19.7427 20.75 18.5V15C20.75 13.7574 19.7426 12.75 18.5 12.75H15ZM14.25 15C14.25 14.5858 14.5858 14.25 15 14.25H18.5C18.9142 14.25 19.25 14.5858 19.25 15V18.5C19.25 18.9142 18.9142 19.25 18.5 19.25H15C14.5858 19.25 14.25 18.9142 14.25 18.5V15Z"
                                                fill=""></path>
                                        </svg>
                                    </div>

                                    <div>
                                        <h4 class="mb-1 text-sm font-medium text-gray-800 dark:text-white/90">
                                            Apps
                                        </h4>
                                        <span class="block text-sm text-gray-500 dark:text-gray-400">
                                            46% Used
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <span class="mb-1 block text-right text-sm text-gray-500 dark:text-gray-400">
                                        245 files
                                    </span>
                                    <span class="block text-right text-sm text-gray-500 dark:text-gray-400">
                                        26.40 GB
                                    </span>
                                </div>
                            </div>

                            <!-- Media item-->
                            <div
                                class="flex items-center justify-between rounded-2xl border border-gray-100 bg-white py-4 pl-4 pr-4 dark:border-gray-800 dark:bg-white/[0.03] xl:pr-5">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="flex h-[52px] w-[52px] items-center justify-center rounded-xl bg-warning-500/[0.08] text-warning-500">
                                        <svg class="fill-current" width="25" height="24" viewBox="0 0 25 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M19.8335 19.75C19.8335 20.9926 18.8261 22 17.5835 22H7.0835C5.84086 22 4.8335 20.9926 4.8335 19.75V9.62105C4.8335 9.02455 5.07036 8.45247 5.49201 8.03055L10.8597 2.65951C11.2817 2.23725 11.8542 2 12.4512 2H17.5835C18.8261 2 19.8335 3.00736 19.8335 4.25V19.75ZM17.5835 20.5C17.9977 20.5 18.3335 20.1642 18.3335 19.75V4.25C18.3335 3.83579 17.9977 3.5 17.5835 3.5H12.5815L12.5844 7.49913C12.5853 8.7424 11.5776 9.75073 10.3344 9.75073H6.3335V19.75C6.3335 20.1642 6.66928 20.5 7.0835 20.5H17.5835ZM7.39262 8.25073L11.0823 4.55876L11.0844 7.5002C11.0847 7.91462 10.7488 8.25073 10.3344 8.25073H7.39262ZM8.5835 14.5C8.5835 14.0858 8.91928 13.75 9.3335 13.75H15.3335C15.7477 13.75 16.0835 14.0858 16.0835 14.5C16.0835 14.9142 15.7477 15.25 15.3335 15.25H9.3335C8.91928 15.25 8.5835 14.9142 8.5835 14.5ZM8.5835 17.5C8.5835 17.0858 8.91928 16.75 9.3335 16.75H12.3335C12.7477 16.75 13.0835 17.0858 13.0835 17.5C13.0835 17.9142 12.7477 18.25 12.3335 18.25H9.3335C8.91928 18.25 8.5835 17.9142 8.5835 17.5Z"
                                                fill=""></path>
                                        </svg>
                                    </div>

                                    <div>
                                        <h4 class="mb-1 text-sm font-medium text-gray-800 dark:text-white/90">
                                            Docs
                                        </h4>
                                        <span class="block text-sm text-gray-500 dark:text-gray-400">
                                            18% Used
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <span class="mb-1 block text-right text-sm text-gray-500 dark:text-gray-400">
                                        245 files
                                    </span>
                                    <span class="block text-right text-sm text-gray-500 dark:text-gray-400">
                                        26.40 GB
                                    </span>
                                </div>
                            </div>

                            <!-- Media item-->
                            <div
                                class="flex items-center justify-between rounded-2xl border border-gray-100 bg-white py-4 pl-4 pr-4 dark:border-gray-800 dark:bg-white/[0.03] xl:pr-5">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="flex h-[52px] w-[52px] items-center justify-center rounded-xl bg-theme-purple-500/[0.08] text-theme-purple-500">
                                        <svg class="fill-current" width="25" height="24" viewBox="0 0 25 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M12.6686 16.75C12.4526 16.75 12.2579 16.6587 12.1211 16.5126L7.5115 11.9059C7.21851 11.6131 7.21836 11.1382 7.51116 10.8452C7.80396 10.5523 8.27883 10.5521 8.57182 10.8449L11.9186 14.1896V4C11.9186 3.58579 12.2544 3.25 12.6686 3.25C13.0828 3.25 13.4186 3.58579 13.4186 4V14.1854L16.7615 10.8449C17.0545 10.5521 17.5294 10.5523 17.8222 10.8453C18.115 11.1383 18.1148 11.6131 17.8218 11.9059L13.2469 16.4776C13.1093 16.644 12.9013 16.75 12.6686 16.75ZM5.41663 16C5.41663 15.5858 5.08084 15.25 4.66663 15.25C4.25241 15.25 3.91663 15.5858 3.91663 16V18.5C3.91663 19.7426 4.92399 20.75 6.16663 20.75H19.1675C20.4101 20.75 21.4175 19.7426 21.4175 18.5V16C21.4175 15.5858 21.0817 15.25 20.6675 15.25C20.2533 15.25 19.9175 15.5858 19.9175 16V18.5C19.9175 18.9142 19.5817 19.25 19.1675 19.25H6.16663C5.75241 19.25 5.41663 18.9142 5.41663 18.5V16Z"
                                                fill=""></path>
                                        </svg>
                                    </div>

                                    <div>
                                        <h4 class="mb-1 text-sm font-medium text-gray-800 dark:text-white/90">
                                            Downloads
                                        </h4>
                                        <span class="block text-sm text-gray-500 dark:text-gray-400">
                                            16% Used
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <span class="mb-1 block text-right text-sm text-gray-500 dark:text-gray-400">
                                        245 files
                                    </span>
                                    <span class="block text-right text-sm text-gray-500 dark:text-gray-400">
                                        26.40 GB
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Media Card -->
            </div>

            <div class="col-span-12 xl:col-span-8">
                <!-- Folders Card -->
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="px-4 py-4 sm:pl-6 sm:pr-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                All Folders
                            </h3>

                            <a href="#"
                                class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-green-500 dark:text-gray-400 dark:hover:text-green-500">
                                View All
                                <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M17.4175 9.9986C17.4178 10.1909 17.3446 10.3832 17.198 10.53L12.2013 15.5301C11.9085 15.8231 11.4337 15.8233 11.1407 15.5305C10.8477 15.2377 10.8475 14.7629 11.1403 14.4699L14.8604 10.7472L3.33301 10.7472C2.91879 10.7472 2.58301 10.4114 2.58301 9.99715C2.58301 9.58294 2.91879 9.24715 3.33301 9.24715L14.8549 9.24715L11.1403 5.53016C10.8475 5.23717 10.8477 4.7623 11.1407 4.4695C11.4336 4.1767 11.9085 4.17685 12.2013 4.46984L17.1588 9.43049C17.3173 9.568 17.4175 9.77087 17.4175 9.99715C17.4175 9.99763 17.4175 9.99812 17.4175 9.9986Z"
                                        fill=""></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                    <div class="border-t border-gray-100 p-5 dark:border-gray-800 sm:p-6">
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 sm:gap-6">
                            <!-- Folders item-->
                            <div
                                class="rounded-2xl border border-gray-100 bg-gray-50 px-6 py-6 dark:border-gray-800 dark:bg-white/[0.03] xl:py-[27px]">
                                <div class="mb-6 flex justify-between">
                                    <div>
                                        <svg width="36" height="36" viewBox="0 0 36 36" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M13.3986 4.40674C12.9265 3.77722 12.1855 3.40674 11.3986 3.40674H2.5C1.11929 3.40674 0 4.52602 0 5.90674V30.0959C0 31.4766 1.11929 32.5959 2.5 32.5959H33.5C34.8807 32.5959 36 31.4766 36 30.0959V11.7446C36 10.3639 34.8807 9.24458 33.5 9.24458H18.277C17.4901 9.24458 16.7492 8.87409 16.277 8.24458L13.3986 4.40674Z"
                                                fill="url(#paint0_linear_2816_28044)"></path>
                                            <defs>
                                                <linearGradient id="paint0_linear_2816_28044" x1="18" y1="3.40674" x2="18"
                                                    y2="32.5959" gradientUnits="userSpaceOnUse">
                                                    <stop stop-color="#FFDC78"></stop>
                                                    <stop offset="1" stop-color="#FBBC1A"></stop>
                                                </linearGradient>
                                            </defs>
                                        </svg>
                                    </div>

                                    <div x-data="{openDropDown: false}" class="relative">
                                        <button @click="openDropDown = !openDropDown"
                                            :class="openDropDown ? 'text-gray-700 dark:text-white' : 'text-gray-400 hover:text-gray-700 dark:hover:text-white'"
                                            class="text-gray-400 hover:text-gray-700 dark:hover:text-white">
                                            <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M10.2441 6C10.2441 5.0335 11.0276 4.25 11.9941 4.25H12.0041C12.9706 4.25 13.7541 5.0335 13.7541 6C13.7541 6.9665 12.9706 7.75 12.0041 7.75H11.9941C11.0276 7.75 10.2441 6.9665 10.2441 6ZM10.2441 18C10.2441 17.0335 11.0276 16.25 11.9941 16.25H12.0041C12.9706 16.25 13.7541 17.0335 13.7541 18C13.7541 18.9665 12.9706 19.75 12.0041 19.75H11.9941C11.0276 19.75 10.2441 18.9665 10.2441 18ZM11.9941 10.25C11.0276 10.25 10.2441 11.0335 10.2441 12C10.2441 12.9665 11.0276 13.75 11.9941 13.75H12.0041C12.9706 13.75 13.7541 12.9665 13.7541 12C13.7541 11.0335 12.9706 10.25 12.0041 10.25H11.9941Z"
                                                    fill=""></path>
                                            </svg>
                                        </button>
                                        <div x-show="openDropDown" @click.outside="openDropDown = false"
                                            class="absolute right-0 top-full z-40 w-40 space-y-1 rounded-2xl border border-gray-200 bg-white p-2 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark"
                                            style="display: none;">
                                            <button
                                                class="flex w-full rounded-lg px-3 py-2 text-left text-theme-xs font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                                View More
                                            </button>
                                            <button
                                                class="flex w-full rounded-lg px-3 py-2 text-left text-theme-xs font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <h4 class="mb-1 text-sm font-medium text-gray-800 dark:text-white/90">
                                    Image
                                </h4>
                                <div class="flex items-center justify-between">
                                    <span class="block text-sm text-gray-500 dark:text-gray-400">
                                        345 Files
                                    </span>
                                    <span class="block text-right text-sm text-gray-500 dark:text-gray-400">
                                        26.40 GB
                                    </span>
                                </div>
                            </div>

                            <!-- Folders item-->
                            <div
                                class="rounded-2xl border border-gray-100 bg-gray-50 px-6 py-6 dark:border-gray-800 dark:bg-white/[0.03] xl:py-[27px]">
                                <div class="mb-6 flex justify-between">
                                    <div>
                                        <svg width="36" height="36" viewBox="0 0 36 36" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M13.3986 4.40674C12.9265 3.77722 12.1855 3.40674 11.3986 3.40674H2.5C1.11929 3.40674 0 4.52602 0 5.90674V30.0959C0 31.4766 1.11929 32.5959 2.5 32.5959H33.5C34.8807 32.5959 36 31.4766 36 30.0959V11.7446C36 10.3639 34.8807 9.24458 33.5 9.24458H18.277C17.4901 9.24458 16.7492 8.87409 16.277 8.24458L13.3986 4.40674Z"
                                                fill="url(#paint0_linear_2816_28044)"></path>
                                            <defs>
                                                <linearGradient id="paint0_linear_2816_28044" x1="18" y1="3.40674" x2="18"
                                                    y2="32.5959" gradientUnits="userSpaceOnUse">
                                                    <stop stop-color="#FFDC78"></stop>
                                                    <stop offset="1" stop-color="#FBBC1A"></stop>
                                                </linearGradient>
                                            </defs>
                                        </svg>
                                    </div>

                                    <div x-data="{openDropDown: false}" class="relative">
                                        <button @click="openDropDown = !openDropDown"
                                            :class="openDropDown ? 'text-gray-700 dark:text-white' : 'text-gray-400 hover:text-gray-700 dark:hover:text-white'"
                                            class="text-gray-400 hover:text-gray-700 dark:hover:text-white">
                                            <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M10.2441 6C10.2441 5.0335 11.0276 4.25 11.9941 4.25H12.0041C12.9706 4.25 13.7541 5.0335 13.7541 6C13.7541 6.9665 12.9706 7.75 12.0041 7.75H11.9941C11.0276 7.75 10.2441 6.9665 10.2441 6ZM10.2441 18C10.2441 17.0335 11.0276 16.25 11.9941 16.25H12.0041C12.9706 16.25 13.7541 17.0335 13.7541 18C13.7541 18.9665 12.9706 19.75 12.0041 19.75H11.9941C11.0276 19.75 10.2441 18.9665 10.2441 18ZM11.9941 10.25C11.0276 10.25 10.2441 11.0335 10.2441 12C10.2441 12.9665 11.0276 13.75 11.9941 13.75H12.0041C12.9706 13.75 13.7541 12.9665 13.7541 12C13.7541 11.0335 12.9706 10.25 12.0041 10.25H11.9941Z"
                                                    fill=""></path>
                                            </svg>
                                        </button>
                                        <div x-show="openDropDown" @click.outside="openDropDown = false"
                                            class="absolute right-0 top-full z-40 w-40 space-y-1 rounded-2xl border border-gray-200 bg-white p-2 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark"
                                            style="display: none;">
                                            <button
                                                class="flex w-full rounded-lg px-3 py-2 text-left text-theme-xs font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                                View More
                                            </button>
                                            <button
                                                class="flex w-full rounded-lg px-3 py-2 text-left text-theme-xs font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <h4 class="mb-1 text-sm font-medium text-gray-800 dark:text-white/90">
                                    Documents
                                </h4>
                                <div class="flex items-center justify-between">
                                    <span class="block text-sm text-gray-500 dark:text-gray-400">
                                        130 Files
                                    </span>
                                    <span class="block text-right text-sm text-gray-500 dark:text-gray-400">
                                        26.40 GB
                                    </span>
                                </div>
                            </div>

                            <!-- Folders item-->
                            <div
                                class="rounded-2xl border border-gray-100 bg-gray-50 px-6 py-6 dark:border-gray-800 dark:bg-white/[0.03] xl:py-[27px]">
                                <div class="mb-6 flex justify-between">
                                    <div>
                                        <svg width="36" height="36" viewBox="0 0 36 36" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M13.3986 4.40674C12.9265 3.77722 12.1855 3.40674 11.3986 3.40674H2.5C1.11929 3.40674 0 4.52602 0 5.90674V30.0959C0 31.4766 1.11929 32.5959 2.5 32.5959H33.5C34.8807 32.5959 36 31.4766 36 30.0959V11.7446C36 10.3639 34.8807 9.24458 33.5 9.24458H18.277C17.4901 9.24458 16.7492 8.87409 16.277 8.24458L13.3986 4.40674Z"
                                                fill="url(#paint0_linear_2816_28044)"></path>
                                            <defs>
                                                <linearGradient id="paint0_linear_2816_28044" x1="18" y1="3.40674" x2="18"
                                                    y2="32.5959" gradientUnits="userSpaceOnUse">
                                                    <stop stop-color="#FFDC78"></stop>
                                                    <stop offset="1" stop-color="#FBBC1A"></stop>
                                                </linearGradient>
                                            </defs>
                                        </svg>
                                    </div>

                                    <div x-data="{openDropDown: false}" class="relative">
                                        <button @click="openDropDown = !openDropDown"
                                            :class="openDropDown ? 'text-gray-700 dark:text-white' : 'text-gray-400 hover:text-gray-700 dark:hover:text-white'"
                                            class="text-gray-400 hover:text-gray-700 dark:hover:text-white">
                                            <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M10.2441 6C10.2441 5.0335 11.0276 4.25 11.9941 4.25H12.0041C12.9706 4.25 13.7541 5.0335 13.7541 6C13.7541 6.9665 12.9706 7.75 12.0041 7.75H11.9941C11.0276 7.75 10.2441 6.9665 10.2441 6ZM10.2441 18C10.2441 17.0335 11.0276 16.25 11.9941 16.25H12.0041C12.9706 16.25 13.7541 17.0335 13.7541 18C13.7541 18.9665 12.9706 19.75 12.0041 19.75H11.9941C11.0276 19.75 10.2441 18.9665 10.2441 18ZM11.9941 10.25C11.0276 10.25 10.2441 11.0335 10.2441 12C10.2441 12.9665 11.0276 13.75 11.9941 13.75H12.0041C12.9706 13.75 13.7541 12.9665 13.7541 12C13.7541 11.0335 12.9706 10.25 12.0041 10.25H11.9941Z"
                                                    fill=""></path>
                                            </svg>
                                        </button>
                                        <div x-show="openDropDown" @click.outside="openDropDown = false"
                                            class="absolute right-0 top-full z-40 w-40 space-y-1 rounded-2xl border border-gray-200 bg-white p-2 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark"
                                            style="display: none;">
                                            <button
                                                class="flex w-full rounded-lg px-3 py-2 text-left text-theme-xs font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                                View More
                                            </button>
                                            <button
                                                class="flex w-full rounded-lg px-3 py-2 text-left text-theme-xs font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <h4 class="mb-1 text-sm font-medium text-gray-800 dark:text-white/90">
                                    Apps
                                </h4>
                                <div class="flex items-center justify-between">
                                    <span class="block text-sm text-gray-500 dark:text-gray-400">
                                        130 Files
                                    </span>
                                    <span class="block text-right text-sm text-gray-500 dark:text-gray-400">
                                        26.40 GB
                                    </span>
                                </div>
                            </div>

                            <!-- Folders item-->
                            <div
                                class="rounded-2xl border border-gray-100 bg-gray-50 px-6 py-6 dark:border-gray-800 dark:bg-white/[0.03] xl:py-[27px]">
                                <div class="mb-6 flex justify-between">
                                    <div>
                                        <svg width="36" height="36" viewBox="0 0 36 36" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M13.3986 4.40674C12.9265 3.77722 12.1855 3.40674 11.3986 3.40674H2.5C1.11929 3.40674 0 4.52602 0 5.90674V30.0959C0 31.4766 1.11929 32.5959 2.5 32.5959H33.5C34.8807 32.5959 36 31.4766 36 30.0959V11.7446C36 10.3639 34.8807 9.24458 33.5 9.24458H18.277C17.4901 9.24458 16.7492 8.87409 16.277 8.24458L13.3986 4.40674Z"
                                                fill="url(#paint0_linear_2816_28044)"></path>
                                            <defs>
                                                <linearGradient id="paint0_linear_2816_28044" x1="18" y1="3.40674" x2="18"
                                                    y2="32.5959" gradientUnits="userSpaceOnUse">
                                                    <stop stop-color="#FFDC78"></stop>
                                                    <stop offset="1" stop-color="#FBBC1A"></stop>
                                                </linearGradient>
                                            </defs>
                                        </svg>
                                    </div>

                                    <div x-data="{openDropDown: false}" class="relative">
                                        <button @click="openDropDown = !openDropDown"
                                            :class="openDropDown ? 'text-gray-700 dark:text-white' : 'text-gray-400 hover:text-gray-700 dark:hover:text-white'"
                                            class="text-gray-400 hover:text-gray-700 dark:hover:text-white">
                                            <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M10.2441 6C10.2441 5.0335 11.0276 4.25 11.9941 4.25H12.0041C12.9706 4.25 13.7541 5.0335 13.7541 6C13.7541 6.9665 12.9706 7.75 12.0041 7.75H11.9941C11.0276 7.75 10.2441 6.9665 10.2441 6ZM10.2441 18C10.2441 17.0335 11.0276 16.25 11.9941 16.25H12.0041C12.9706 16.25 13.7541 17.0335 13.7541 18C13.7541 18.9665 12.9706 19.75 12.0041 19.75H11.9941C11.0276 19.75 10.2441 18.9665 10.2441 18ZM11.9941 10.25C11.0276 10.25 10.2441 11.0335 10.2441 12C10.2441 12.9665 11.0276 13.75 11.9941 13.75H12.0041C12.9706 13.75 13.7541 12.9665 13.7541 12C13.7541 11.0335 12.9706 10.25 12.0041 10.25H11.9941Z"
                                                    fill=""></path>
                                            </svg>
                                        </button>
                                        <div x-show="openDropDown" @click.outside="openDropDown = false"
                                            class="absolute right-0 top-full z-40 w-40 space-y-1 rounded-2xl border border-gray-200 bg-white p-2 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark"
                                            style="display: none;">
                                            <button
                                                class="flex w-full rounded-lg px-3 py-2 text-left text-theme-xs font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                                View More
                                            </button>
                                            <button
                                                class="flex w-full rounded-lg px-3 py-2 text-left text-theme-xs font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <h4 class="mb-1 text-sm font-medium text-gray-800 dark:text-white/90">
                                    Downloads
                                </h4>
                                <div class="flex items-center justify-between">
                                    <span class="block text-sm text-gray-500 dark:text-gray-400">
                                        345 Files
                                    </span>
                                    <span class="block text-right text-sm text-gray-500 dark:text-gray-400">
                                        26.40 GB
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Folders Card -->
            </div>

            <div class="col-span-12 xl:col-span-4">
                <!-- ====== Chart Sixteen Start -->
                <div
                    class="rounded-2xl border border-gray-200 bg-white px-4 pt-6 pb-6 sm:px-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                Storage Details
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                585 GB Free space left
                            </p>
                        </div>

                        <div x-data="{openDropDown: false}" class="relative">
                            <button @click="openDropDown = !openDropDown"
                                :class="openDropDown ? 'text-gray-700 dark:text-white' : 'text-gray-400 hover:text-gray-700 dark:hover:text-white'"
                                class="text-gray-400 hover:text-gray-700 dark:hover:text-white">
                                <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M10.2441 6C10.2441 5.0335 11.0276 4.25 11.9941 4.25H12.0041C12.9706 4.25 13.7541 5.0335 13.7541 6C13.7541 6.9665 12.9706 7.75 12.0041 7.75H11.9941C11.0276 7.75 10.2441 6.9665 10.2441 6ZM10.2441 18C10.2441 17.0335 11.0276 16.25 11.9941 16.25H12.0041C12.9706 16.25 13.7541 17.0335 13.7541 18C13.7541 18.9665 12.9706 19.75 12.0041 19.75H11.9941C11.0276 19.75 10.2441 18.9665 10.2441 18ZM11.9941 10.25C11.0276 10.25 10.2441 11.0335 10.2441 12C10.2441 12.9665 11.0276 13.75 11.9941 13.75H12.0041C12.9706 13.75 13.7541 12.9665 13.7541 12C13.7541 11.0335 12.9706 10.25 12.0041 10.25H11.9941Z"
                                        fill=""></path>
                                </svg>
                            </button>
                            <div x-show="openDropDown" @click.outside="openDropDown = false"
                                class="absolute right-0 z-40 w-40 p-2 space-y-1 bg-white border border-gray-200 shadow-theme-lg dark:bg-gray-dark top-full rounded-2xl dark:border-gray-800"
                                style="display: none;">
                                <button
                                    class="flex w-full px-3 py-2 font-medium text-left text-gray-500 rounded-lg text-theme-xs hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                    View More
                                </button>
                                <button
                                    class="flex w-full px-3 py-2 font-medium text-left text-gray-500 rounded-lg text-theme-xs hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div id="chartSixteen" class="flex justify-center mx-auto chartDarkStyle"
                            style="min-height: 329px;">
                            <div id="apexchartsbzuaf9r0k" class="apexcharts-canvas apexchartsbzuaf9r0k apexcharts-theme-"
                                style="width: 400px; height: 329px;"><svg id="SvgjsSvg1042" width="400" height="329"
                                    xmlns="http://www.w3.org/2000/svg" version="1.1"
                                    xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.dev"
                                    class="apexcharts-svg" xmlns:data="ApexChartsNS" transform="translate(0, 0)">
                                    <foreignObject x="0" y="0" width="400" height="329">
                                        <div xmlns="http://www.w3.org/1999/xhtml"
                                            style="position: relative; height: 100%; width: 100%;">
                                            <div class="apexcharts-legend apexcharts-align-left apx-legend-position-bottom"
                                                style="right: 0px; position: absolute; left: 0px; top: 302.333px; max-height: 166.667px;">
                                                <div class="apexcharts-legend-series" rel="1" seriesname="Downloads"
                                                    data:collapsed="false" style="margin: 6px 10px;"><span
                                                        class="apexcharts-legend-marker" rel="1" data:collapsed="false"
                                                        style="height: 10px; width: 10px; left: 0px; top: 0px;"><svg
                                                            id="SvgjsSvg1045" width="100%" height="100%"
                                                            xmlns="http://www.w3.org/2000/svg" version="1.1"
                                                            xmlns:xlink="http://www.w3.org/1999/xlink"
                                                            xmlns:svgjs="http://svgjs.dev">
                                                            <defs id="SvgjsDefs1046">
                                                                <clipPath id="gridRectMaskbzuaf9r0k">
                                                                    <rect id="SvgjsRect1057" width="400"
                                                                        height="293.33333333333337" x="0" y="0" rx="0"
                                                                        ry="0" opacity="1" stroke-width="0" stroke="none"
                                                                        stroke-dasharray="0" fill="#fff"></rect>
                                                                </clipPath>
                                                                <clipPath id="gridRectBarMaskbzuaf9r0k">
                                                                    <rect id="SvgjsRect1058" width="408"
                                                                        height="301.33333333333337" x="-4" y="-4" rx="0"
                                                                        ry="0" opacity="1" stroke-width="0" stroke="none"
                                                                        stroke-dasharray="0" fill="#fff"></rect>
                                                                </clipPath>
                                                                <clipPath id="gridRectMarkerMaskbzuaf9r0k">
                                                                    <rect id="SvgjsRect1059" width="400"
                                                                        height="293.33333333333337" x="0" y="0" rx="0"
                                                                        ry="0" opacity="1" stroke-width="0" stroke="none"
                                                                        stroke-dasharray="0" fill="#fff"></rect>
                                                                </clipPath>
                                                                <clipPath id="forecastMaskbzuaf9r0k"></clipPath>
                                                                <clipPath id="nonForecastMaskbzuaf9r0k"></clipPath>
                                                            </defs>
                                                            <path id="SvgjsPath1047" d="M 0, 0
               m -5, 0
               a 5,5 0 1,0 10,0
               a 5,5 0 1,0 -10,0" fill="#9b8afb" fill-opacity="1" stroke-opacity="0.9" stroke-linecap="butt"
                                                                stroke-width="0" stroke-dasharray="0" cx="0" cy="0"
                                                                shape="circle"
                                                                class="apexcharts-legend-marker apexcharts-marker apexcharts-marker-circle"
                                                                style="transform: translate(50%, 50%);"></path>
                                                        </svg></span><span class="apexcharts-legend-text" rel="1" i="0"
                                                        data:default-text="Downloads" data:collapsed="false"
                                                        style="color: rgb(55, 61, 63); font-size: 14px; font-weight: 400; font-family: Outfit;">Downloads</span>
                                                </div>
                                                <div class="apexcharts-legend-series" rel="2" seriesname="Apps"
                                                    data:collapsed="false" style="margin: 6px 10px;"><span
                                                        class="apexcharts-legend-marker" rel="2" data:collapsed="false"
                                                        style="height: 10px; width: 10px; left: 0px; top: 0px;"><svg
                                                            id="SvgjsSvg1048" width="100%" height="100%"
                                                            xmlns="http://www.w3.org/2000/svg" version="1.1"
                                                            xmlns:xlink="http://www.w3.org/1999/xlink"
                                                            xmlns:svgjs="http://svgjs.dev">
                                                            <defs id="SvgjsDefs1049"></defs>
                                                            <path id="SvgjsPath1050" d="M 0, 0
               m -5, 0
               a 5,5 0 1,0 10,0
               a 5,5 0 1,0 -10,0" fill="#fd853a" fill-opacity="1" stroke-opacity="0.9" stroke-linecap="butt"
                                                                stroke-width="0" stroke-dasharray="0" cx="0" cy="0"
                                                                shape="circle"
                                                                class="apexcharts-legend-marker apexcharts-marker apexcharts-marker-circle"
                                                                style="transform: translate(50%, 50%);"></path>
                                                        </svg></span><span class="apexcharts-legend-text" rel="2" i="1"
                                                        data:default-text="Apps" data:collapsed="false"
                                                        style="color: rgb(55, 61, 63); font-size: 14px; font-weight: 400; font-family: Outfit;">Apps</span>
                                                </div>
                                                <div class="apexcharts-legend-series" rel="3" seriesname="Documents"
                                                    data:collapsed="false" style="margin: 6px 10px;"><span
                                                        class="apexcharts-legend-marker" rel="3" data:collapsed="false"
                                                        style="height: 10px; width: 10px; left: 0px; top: 0px;"><svg
                                                            id="SvgjsSvg1051" width="100%" height="100%"
                                                            xmlns="http://www.w3.org/2000/svg" version="1.1"
                                                            xmlns:xlink="http://www.w3.org/1999/xlink"
                                                            xmlns:svgjs="http://svgjs.dev">
                                                            <defs id="SvgjsDefs1052"></defs>
                                                            <path id="SvgjsPath1053" d="M 0, 0
               m -5, 0
               a 5,5 0 1,0 10,0
               a 5,5 0 1,0 -10,0" fill="#fdb022" fill-opacity="1" stroke-opacity="0.9" stroke-linecap="butt"
                                                                stroke-width="0" stroke-dasharray="0" cx="0" cy="0"
                                                                shape="circle"
                                                                class="apexcharts-legend-marker apexcharts-marker apexcharts-marker-circle"
                                                                style="transform: translate(50%, 50%);"></path>
                                                        </svg></span><span class="apexcharts-legend-text" rel="3" i="2"
                                                        data:default-text="Documents" data:collapsed="false"
                                                        style="color: rgb(55, 61, 63); font-size: 14px; font-weight: 400; font-family: Outfit;">Documents</span>
                                                </div>
                                                <div class="apexcharts-legend-series" rel="4" seriesname="Media"
                                                    data:collapsed="false" style="margin: 6px 10px;"><span
                                                        class="apexcharts-legend-marker" rel="4" data:collapsed="false"
                                                        style="height: 10px; width: 10px; left: 0px; top: 0px;"><svg
                                                            id="SvgjsSvg1054" width="100%" height="100%"
                                                            xmlns="http://www.w3.org/2000/svg" version="1.1"
                                                            xmlns:xlink="http://www.w3.org/1999/xlink"
                                                            xmlns:svgjs="http://svgjs.dev">
                                                            <defs id="SvgjsDefs1055"></defs>
                                                            <path id="SvgjsPath1056" d="M 0, 0
               m -5, 0
               a 5,5 0 1,0 10,0
               a 5,5 0 1,0 -10,0" fill="#32d583" fill-opacity="1" stroke-opacity="0.9" stroke-linecap="butt"
                                                                stroke-width="0" stroke-dasharray="0" cx="0" cy="0"
                                                                shape="circle"
                                                                class="apexcharts-legend-marker apexcharts-marker apexcharts-marker-circle"
                                                                style="transform: translate(50%, 50%);"></path>
                                                        </svg></span><span class="apexcharts-legend-text" rel="4" i="3"
                                                        data:default-text="Media" data:collapsed="false"
                                                        style="color: rgb(55, 61, 63); font-size: 14px; font-weight: 400; font-family: Outfit;">Media</span>
                                                </div>
                                            </div>
                                        </div>
                                        <style type="text/css">
                                            .apexcharts-legend {
                                                display: flex;
                                                overflow: auto;
                                                padding: 0 10px;
                                            }

                                            .apexcharts-legend.apx-legend-position-bottom,
                                            .apexcharts-legend.apx-legend-position-top {
                                                flex-wrap: wrap
                                            }

                                            .apexcharts-legend.apx-legend-position-right,
                                            .apexcharts-legend.apx-legend-position-left {
                                                flex-direction: column;
                                                bottom: 0;
                                            }

                                            .apexcharts-legend.apx-legend-position-bottom.apexcharts-align-left,
                                            .apexcharts-legend.apx-legend-position-top.apexcharts-align-left,
                                            .apexcharts-legend.apx-legend-position-right,
                                            .apexcharts-legend.apx-legend-position-left {
                                                justify-content: flex-start;
                                            }

                                            .apexcharts-legend.apx-legend-position-bottom.apexcharts-align-center,
                                            .apexcharts-legend.apx-legend-position-top.apexcharts-align-center {
                                                justify-content: center;
                                            }

                                            .apexcharts-legend.apx-legend-position-bottom.apexcharts-align-right,
                                            .apexcharts-legend.apx-legend-position-top.apexcharts-align-right {
                                                justify-content: flex-end;
                                            }

                                            .apexcharts-legend-series {
                                                cursor: pointer;
                                                line-height: normal;
                                                display: flex;
                                                align-items: center;
                                            }

                                            .apexcharts-legend-text {
                                                position: relative;
                                                font-size: 14px;
                                            }

                                            .apexcharts-legend-text *,
                                            .apexcharts-legend-marker * {
                                                pointer-events: none;
                                            }

                                            .apexcharts-legend-marker {
                                                position: relative;
                                                display: flex;
                                                align-items: center;
                                                justify-content: center;
                                                cursor: pointer;
                                                margin-right: 1px;
                                            }

                                            .apexcharts-legend-series.apexcharts-no-click {
                                                cursor: auto;
                                            }

                                            .apexcharts-legend .apexcharts-hidden-zero-series,
                                            .apexcharts-legend .apexcharts-hidden-null-series {
                                                display: none !important;
                                            }

                                            .apexcharts-inactive-legend {
                                                opacity: 0.45;
                                            }
                                        </style>
                                    </foreignObject>
                                    <g id="SvgjsG1044" class="apexcharts-inner apexcharts-graphical"
                                        transform="translate(0, 0)">
                                        <defs id="SvgjsDefs1043"></defs>
                                        <g id="SvgjsG1062" class="apexcharts-pie">
                                            <g id="SvgjsG1063" transform="translate(0, 0) scale(1)">
                                                <circle id="SvgjsCircle1064" r="87.80813008130085" cx="200"
                                                    cy="146.66666666666669" fill="transparent"></circle>
                                                <g id="SvgjsG1065" class="apexcharts-slices">
                                                    <g id="SvgjsG1066" class="apexcharts-series apexcharts-pie-series"
                                                        seriesName="Downloads" rel="1" data:realIndex="0">
                                                        <path id="SvgjsPath1067"
                                                            d="M 200 11.57723577235771 A 135.08943089430898 135.08943089430898 0 0 1 332.49372535918764 173.02130724081292 L 286.120921483472 163.79718303986175 A 87.80813008130085 87.80813008130085 0 0 0 200 58.85853658536584 L 200 11.57723577235771 z "
                                                            fill="rgba(155,138,251,1)" fill-opacity="1" stroke-opacity="1"
                                                            stroke-linecap="butt" stroke-width="0" stroke-dasharray="0"
                                                            class="apexcharts-pie-area apexcharts-donut-slice-0" index="0"
                                                            j="0" data:angle="101.25" data:startAngle="0"
                                                            data:strokeWidth="0" data:value="45"
                                                            data:pathOrig="M 200 11.57723577235771 A 135.08943089430898 135.08943089430898 0 0 1 332.49372535918764 173.02130724081292 L 286.120921483472 163.79718303986175 A 87.80813008130085 87.80813008130085 0 0 0 200 58.85853658536584 L 200 11.57723577235771 z ">
                                                        </path>
                                                    </g>
                                                    <g id="SvgjsG1068" class="apexcharts-series apexcharts-pie-series"
                                                        seriesName="Apps" rel="2" data:realIndex="1">
                                                        <path id="SvgjsPath1069"
                                                            d="M 332.49372535918764 173.02130724081292 A 135.08943089430898 135.08943089430898 0 0 1 75.19363973815005 198.36315375754745 L 118.87586582979752 180.2693832757392 A 87.80813008130085 87.80813008130085 0 0 0 286.120921483472 163.79718303986175 L 332.49372535918764 173.02130724081292 z "
                                                            fill="rgba(253,133,58,1)" fill-opacity="1" stroke-opacity="1"
                                                            stroke-linecap="butt" stroke-width="0" stroke-dasharray="0"
                                                            class="apexcharts-pie-area apexcharts-donut-slice-1" index="0"
                                                            j="1" data:angle="146.25" data:startAngle="101.25"
                                                            data:strokeWidth="0" data:value="65"
                                                            data:pathOrig="M 332.49372535918764 173.02130724081292 A 135.08943089430898 135.08943089430898 0 0 1 75.19363973815005 198.36315375754745 L 118.87586582979752 180.2693832757392 A 87.80813008130085 87.80813008130085 0 0 0 286.120921483472 163.79718303986175 L 332.49372535918764 173.02130724081292 z ">
                                                        </path>
                                                    </g>
                                                    <g id="SvgjsG1070" class="apexcharts-series apexcharts-pie-series"
                                                        seriesName="Documents" rel="3" data:realIndex="2">
                                                        <path id="SvgjsPath1071"
                                                            d="M 75.19363973815005 198.36315375754745 A 135.08943089430898 135.08943089430898 0 0 1 87.67724326813745 71.61500006622998 L 126.99020812428932 97.88308337638283 A 87.80813008130085 87.80813008130085 0 0 0 118.87586582979752 180.2693832757392 L 75.19363973815005 198.36315375754745 z "
                                                            fill="rgba(253,176,34,1)" fill-opacity="1" stroke-opacity="1"
                                                            stroke-linecap="butt" stroke-width="0" stroke-dasharray="0"
                                                            class="apexcharts-pie-area apexcharts-donut-slice-2" index="0"
                                                            j="2" data:angle="56.25" data:startAngle="247.5"
                                                            data:strokeWidth="0" data:value="25"
                                                            data:pathOrig="M 75.19363973815005 198.36315375754745 A 135.08943089430898 135.08943089430898 0 0 1 87.67724326813745 71.61500006622998 L 126.99020812428932 97.88308337638283 A 87.80813008130085 87.80813008130085 0 0 0 118.87586582979752 180.2693832757392 L 75.19363973815005 198.36315375754745 z ">
                                                        </path>
                                                    </g>
                                                    <g id="SvgjsG1072" class="apexcharts-series apexcharts-pie-series"
                                                        seriesName="Media" rel="4" data:realIndex="3">
                                                        <path id="SvgjsPath1073"
                                                            d="M 87.67724326813745 71.61500006622998 A 135.08943089430898 135.08943089430898 0 0 1 199.97642244658218 11.577237829887395 L 199.98467459027842 58.858537922760135 A 87.80813008130085 87.80813008130085 0 0 0 126.99020812428932 97.88308337638283 L 87.67724326813745 71.61500006622998 z "
                                                            fill="rgba(50,213,131,1)" fill-opacity="1" stroke-opacity="1"
                                                            stroke-linecap="butt" stroke-width="0" stroke-dasharray="0"
                                                            class="apexcharts-pie-area apexcharts-donut-slice-3" index="0"
                                                            j="3" data:angle="56.25" data:startAngle="303.75"
                                                            data:strokeWidth="0" data:value="25"
                                                            data:pathOrig="M 87.67724326813745 71.61500006622998 A 135.08943089430898 135.08943089430898 0 0 1 199.97642244658218 11.577237829887395 L 199.98467459027842 58.858537922760135 A 87.80813008130085 87.80813008130085 0 0 0 126.99020812428932 97.88308337638283 L 87.67724326813745 71.61500006622998 z ">
                                                        </path>
                                                    </g>
                                                </g>
                                            </g>
                                            <g id="SvgjsG1060" class="apexcharts-datalabels-group"
                                                transform="translate(0, 0) scale(1)"><text id="SvgjsText1074"
                                                    font-family="Outfit, sans-serif" x="200" y="146.66666666666669"
                                                    text-anchor="middle" dominant-baseline="auto" font-size="24px"
                                                    font-weight="bold" fill="#000000"
                                                    class="apexcharts-text apexcharts-datalabel-label"
                                                    style="font-family: Outfit, sans-serif;">Total 135 GB</text><text
                                                    id="SvgjsText1075" font-family="Outfit, sans-serif" x="200"
                                                    y="172.66666666666669" text-anchor="middle" dominant-baseline="auto"
                                                    font-size="14px" font-weight="400" fill="#667085"
                                                    class="apexcharts-text apexcharts-datalabel-value"
                                                    style="font-family: Outfit, sans-serif;">160</text></g>
                                        </g>
                                        <line id="SvgjsLine1076" x1="0" y1="0" x2="400" y2="0" stroke="#b6b6b6"
                                            stroke-dasharray="0" stroke-width="1" stroke-linecap="butt"
                                            class="apexcharts-ycrosshairs"></line>
                                        <line id="SvgjsLine1077" x1="0" y1="0" x2="400" y2="0" stroke-dasharray="0"
                                            stroke-width="0" stroke-linecap="butt" class="apexcharts-ycrosshairs-hidden">
                                        </line>
                                    </g>
                                    <g id="SvgjsG1061" class="apexcharts-datalabels-group"
                                        transform="translate(0, 0) scale(1)"></g>
                                </svg></div>
                        </div>
                    </div>
                </div>
                <!-- ====== Chart Sixteen End -->
            </div>

            <div class="col-span-12">
                <!-- ====== Table Seven Start -->
                <div
                    class="overflow-hidden rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="mb-4 flex items-center justify-between px-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                                Recent Files
                            </h3>
                        </div>

                        <a href="#"
                            class="inline-flex items-center gap-2 text-gray-500 hover:text-green-500 dark:text-gray-400 dark:hover:text-green-500">
                            View All
                            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M17.4175 9.9986C17.4178 10.1909 17.3446 10.3832 17.198 10.53L12.2013 15.5301C11.9085 15.8231 11.4337 15.8233 11.1407 15.5305C10.8477 15.2377 10.8475 14.7629 11.1403 14.4699L14.8604 10.7472L3.33301 10.7472C2.91879 10.7472 2.58301 10.4114 2.58301 9.99715C2.58301 9.58294 2.91879 9.24715 3.33301 9.24715L14.8549 9.24715L11.1403 5.53016C10.8475 5.23717 10.8477 4.7623 11.1407 4.4695C11.4336 4.1767 11.9085 4.17685 12.2013 4.46984L17.1588 9.43049C17.3173 9.568 17.4175 9.77087 17.4175 9.99715C17.4175 9.99763 17.4175 9.99812 17.4175 9.9986Z"
                                    fill=""></path>
                            </svg>
                        </a>
                    </div>

                    <div class="max-w-full overflow-x-auto">
                        <div class="min-w-[1026px]">
                            <!-- table header start -->
                            <div class="grid grid-cols-11 border-t border-gray-200 px-6 py-3 dark:border-gray-800">
                                <div class="col-span-3 flex items-center">
                                    <p class="text-theme-sm font-medium text-gray-500 dark:text-gray-400">
                                        File Name
                                    </p>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <p class="text-theme-sm font-medium text-gray-500 dark:text-gray-400">
                                        Category
                                    </p>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <p class="text-theme-sm font-medium text-gray-500 dark:text-gray-400">
                                        Size
                                    </p>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <p class="text-theme-sm font-medium text-gray-500 dark:text-gray-400">
                                        Date Modified
                                    </p>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <p
                                        class="w-full text-center text-theme-sm font-medium text-gray-500 dark:text-gray-400">
                                        Action
                                    </p>
                                </div>
                            </div>
                            <!-- table header end -->

                            <!-- table body start -->
                            <!-- table item -->
                            <div class="grid grid-cols-11 border-t border-gray-100 px-6 py-[18px] dark:border-gray-800">
                                <div class="col-span-3 flex items-center">
                                    <div class="flex w-full items-center gap-2 text-sm text-gray-700 dark:text-gray-400">
                                        <div>
                                            <img src="src/images/icons/file-video.svg" alt="icon" class="dark:hidden">
                                            <img src="src/images/icons/file-video-dark.svg" alt="icon"
                                                class="hidden dark:block">
                                        </div>

                                        Video_947954.mp4
                                    </div>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <p class="text-theme-sm text-gray-700 dark:text-gray-400">Video</p>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <p class="text-theme-sm text-gray-700 dark:text-gray-400">89 MB</p>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <p class="text-theme-sm text-gray-700 dark:text-gray-400">
                                        12 Jan, 2027
                                    </p>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <div class="flex w-full items-center justify-center gap-2">
                                        <button
                                            class="text-gray-500 hover:text-error-500 dark:text-gray-400 dark:hover:text-error-500">
                                            <svg class="fill-current" width="21" height="20" viewBox="0 0 21 20" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M7.4163 3.79199C7.4163 2.54935 8.42366 1.54199 9.6663 1.54199H12.083C13.3256 1.54199 14.333 2.54935 14.333 3.79199V4.04199H16.5H17.5409C17.9551 4.04199 18.2909 4.37778 18.2909 4.79199C18.2909 5.20621 17.9551 5.54199 17.5409 5.54199H17.25V8.24687V13.2469V16.2087C17.25 17.4513 16.2427 18.4587 15 18.4587H6.75004C5.5074 18.4587 4.50004 17.4513 4.50004 16.2087V13.2469V8.24687V5.54199H4.20837C3.79416 5.54199 3.45837 5.20621 3.45837 4.79199C3.45837 4.37778 3.79416 4.04199 4.20837 4.04199H5.25004H7.4163V3.79199ZM15.75 13.2469V8.24687V5.54199H14.333H13.583H8.1663H7.4163H6.00004V8.24687V13.2469V16.2087C6.00004 16.6229 6.33583 16.9587 6.75004 16.9587H15C15.4143 16.9587 15.75 16.6229 15.75 16.2087V13.2469ZM8.9163 4.04199H12.833V3.79199C12.833 3.37778 12.4972 3.04199 12.083 3.04199H9.6663C9.25209 3.04199 8.9163 3.37778 8.9163 3.79199V4.04199ZM9.20837 8.00033C9.62259 8.00033 9.95837 8.33611 9.95837 8.75033V13.7503C9.95837 14.1645 9.62259 14.5003 9.20837 14.5003C8.79416 14.5003 8.45837 14.1645 8.45837 13.7503V8.75033C8.45837 8.33611 8.79416 8.00033 9.20837 8.00033ZM13.2917 8.75033C13.2917 8.33611 12.9559 8.00033 12.5417 8.00033C12.1275 8.00033 11.7917 8.33611 11.7917 8.75033V13.7503C11.7917 14.1645 12.1275 14.5003 12.5417 14.5003C12.9559 14.5003 13.2917 14.1645 13.2917 13.7503V8.75033Z"
                                                    fill=""></path>
                                            </svg>
                                        </button>

                                        <button
                                            class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white/90">
                                            <svg class="fill-current" width="21" height="20" viewBox="0 0 21 20" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M10.8749 13.8619C8.10837 13.8619 5.74279 12.1372 4.79804 9.70241C5.74279 7.26761 8.10837 5.54297 10.8749 5.54297C13.6415 5.54297 16.0071 7.26762 16.9518 9.70243C16.0071 12.1372 13.6415 13.8619 10.8749 13.8619ZM10.8749 4.04297C7.35666 4.04297 4.36964 6.30917 3.29025 9.4593C3.23626 9.61687 3.23626 9.78794 3.29025 9.94552C4.36964 13.0957 7.35666 15.3619 10.8749 15.3619C14.3932 15.3619 17.3802 13.0957 18.4596 9.94555C18.5136 9.78797 18.5136 9.6169 18.4596 9.45932C17.3802 6.30919 14.3932 4.04297 10.8749 4.04297ZM10.8663 7.84413C9.84002 7.84413 9.00808 8.67606 9.00808 9.70231C9.00808 10.7286 9.84002 11.5605 10.8663 11.5605H10.8811C11.9074 11.5605 12.7393 10.7286 12.7393 9.70231C12.7393 8.67606 11.9074 7.84413 10.8811 7.84413H10.8663Z"
                                                    fill=""></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- table item -->
                            <div class="grid grid-cols-11 border-t border-gray-100 px-6 py-[18px] dark:border-gray-800">
                                <div class="col-span-3 flex items-center">
                                    <div class="flex w-full items-center gap-2 text-sm text-gray-700 dark:text-gray-400">
                                        <div>
                                            <img src="src/images/icons/file-image.svg" alt="icon" class="dark:hidden">
                                            <img src="src/images/icons/file-image-dark.svg" alt="icon"
                                                class="hidden dark:block">
                                        </div>

                                        Travel.jpeg
                                    </div>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <p class="text-theme-sm text-gray-700 dark:text-gray-400">Image</p>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <p class="text-theme-sm text-gray-700 dark:text-gray-400">5 MB</p>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <p class="text-theme-sm text-gray-700 dark:text-gray-400">
                                        19 Feb, 2027
                                    </p>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <div class="flex w-full items-center justify-center gap-2">
                                        <button
                                            class="text-gray-500 hover:text-error-500 dark:text-gray-400 dark:hover:text-error-500">
                                            <svg class="fill-current" width="21" height="20" viewBox="0 0 21 20" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M7.4163 3.79199C7.4163 2.54935 8.42366 1.54199 9.6663 1.54199H12.083C13.3256 1.54199 14.333 2.54935 14.333 3.79199V4.04199H16.5H17.5409C17.9551 4.04199 18.2909 4.37778 18.2909 4.79199C18.2909 5.20621 17.9551 5.54199 17.5409 5.54199H17.25V8.24687V13.2469V16.2087C17.25 17.4513 16.2427 18.4587 15 18.4587H6.75004C5.5074 18.4587 4.50004 17.4513 4.50004 16.2087V13.2469V8.24687V5.54199H4.20837C3.79416 5.54199 3.45837 5.20621 3.45837 4.79199C3.45837 4.37778 3.79416 4.04199 4.20837 4.04199H5.25004H7.4163V3.79199ZM15.75 13.2469V8.24687V5.54199H14.333H13.583H8.1663H7.4163H6.00004V8.24687V13.2469V16.2087C6.00004 16.6229 6.33583 16.9587 6.75004 16.9587H15C15.4143 16.9587 15.75 16.6229 15.75 16.2087V13.2469ZM8.9163 4.04199H12.833V3.79199C12.833 3.37778 12.4972 3.04199 12.083 3.04199H9.6663C9.25209 3.04199 8.9163 3.37778 8.9163 3.79199V4.04199ZM9.20837 8.00033C9.62259 8.00033 9.95837 8.33611 9.95837 8.75033V13.7503C9.95837 14.1645 9.62259 14.5003 9.20837 14.5003C8.79416 14.5003 8.45837 14.1645 8.45837 13.7503V8.75033C8.45837 8.33611 8.79416 8.00033 9.20837 8.00033ZM13.2917 8.75033C13.2917 8.33611 12.9559 8.00033 12.5417 8.00033C12.1275 8.00033 11.7917 8.33611 11.7917 8.75033V13.7503C11.7917 14.1645 12.1275 14.5003 12.5417 14.5003C12.9559 14.5003 13.2917 14.1645 13.2917 13.7503V8.75033Z"
                                                    fill=""></path>
                                            </svg>
                                        </button>

                                        <button
                                            class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white/90">
                                            <svg class="fill-current" width="21" height="20" viewBox="0 0 21 20" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M10.8749 13.8619C8.10837 13.8619 5.74279 12.1372 4.79804 9.70241C5.74279 7.26761 8.10837 5.54297 10.8749 5.54297C13.6415 5.54297 16.0071 7.26762 16.9518 9.70243C16.0071 12.1372 13.6415 13.8619 10.8749 13.8619ZM10.8749 4.04297C7.35666 4.04297 4.36964 6.30917 3.29025 9.4593C3.23626 9.61687 3.23626 9.78794 3.29025 9.94552C4.36964 13.0957 7.35666 15.3619 10.8749 15.3619C14.3932 15.3619 17.3802 13.0957 18.4596 9.94555C18.5136 9.78797 18.5136 9.6169 18.4596 9.45932C17.3802 6.30919 14.3932 4.04297 10.8749 4.04297ZM10.8663 7.84413C9.84002 7.84413 9.00808 8.67606 9.00808 9.70231C9.00808 10.7286 9.84002 11.5605 10.8663 11.5605H10.8811C11.9074 11.5605 12.7393 10.7286 12.7393 9.70231C12.7393 8.67606 11.9074 7.84413 10.8811 7.84413H10.8663Z"
                                                    fill=""></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- table item -->
                            <div class="grid grid-cols-11 border-t border-gray-100 px-6 py-[18px] dark:border-gray-800">
                                <div class="col-span-3 flex items-center">
                                    <div class="flex w-full items-center gap-2 text-sm text-gray-700 dark:text-gray-400">
                                        <div>
                                            <img src="src/images/icons/file-pdf.svg" alt="icon" class="dark:hidden">
                                            <img src="src/images/icons/file-pdf-dark.svg" alt="icon"
                                                class="hidden dark:block">
                                        </div>

                                        Document.pdf
                                    </div>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <p class="text-theme-sm text-gray-700 dark:text-gray-400">Document</p>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <p class="text-theme-sm text-gray-700 dark:text-gray-400">10 MB</p>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <p class="text-theme-sm text-gray-700 dark:text-gray-400">
                                        15 Mar, 2027
                                    </p>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <div class="flex w-full items-center justify-center gap-2">
                                        <button
                                            class="text-gray-500 hover:text-error-500 dark:text-gray-400 dark:hover:text-error-500">
                                            <svg class="fill-current" width="21" height="20" viewBox="0 0 21 20" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M7.4163 3.79199C7.4163 2.54935 8.42366 1.54199 9.6663 1.54199H12.083C13.3256 1.54199 14.333 2.54935 14.333 3.79199V4.04199H16.5H17.5409C17.9551 4.04199 18.2909 4.37778 18.2909 4.79199C18.2909 5.20621 17.9551 5.54199 17.5409 5.54199H17.25V8.24687V13.2469V16.2087C17.25 17.4513 16.2427 18.4587 15 18.4587H6.75004C5.5074 18.4587 4.50004 17.4513 4.50004 16.2087V13.2469V8.24687V5.54199H4.20837C3.79416 5.54199 3.45837 5.20621 3.45837 4.79199C3.45837 4.37778 3.79416 4.04199 4.20837 4.04199H5.25004H7.4163V3.79199ZM15.75 13.2469V8.24687V5.54199H14.333H13.583H8.1663H7.4163H6.00004V8.24687V13.2469V16.2087C6.00004 16.6229 6.33583 16.9587 6.75004 16.9587H15C15.4143 16.9587 15.75 16.6229 15.75 16.2087V13.2469ZM8.9163 4.04199H12.833V3.79199C12.833 3.37778 12.4972 3.04199 12.083 3.04199H9.6663C9.25209 3.04199 8.9163 3.37778 8.9163 3.79199V4.04199ZM9.20837 8.00033C9.62259 8.00033 9.95837 8.33611 9.95837 8.75033V13.7503C9.95837 14.1645 9.62259 14.5003 9.20837 14.5003C8.79416 14.5003 8.45837 14.1645 8.45837 13.7503V8.75033C8.45837 8.33611 8.79416 8.00033 9.20837 8.00033ZM13.2917 8.75033C13.2917 8.33611 12.9559 8.00033 12.5417 8.00033C12.1275 8.00033 11.7917 8.33611 11.7917 8.75033V13.7503C11.7917 14.1645 12.1275 14.5003 12.5417 14.5003C12.9559 14.5003 13.2917 14.1645 13.2917 13.7503V8.75033Z"
                                                    fill=""></path>
                                            </svg>
                                        </button>

                                        <button
                                            class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white/90">
                                            <svg class="fill-current" width="21" height="20" viewBox="0 0 21 20" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M10.8749 13.8619C8.10837 13.8619 5.74279 12.1372 4.79804 9.70241C5.74279 7.26761 8.10837 5.54297 10.8749 5.54297C13.6415 5.54297 16.0071 7.26762 16.9518 9.70243C16.0071 12.1372 13.6415 13.8619 10.8749 13.8619ZM10.8749 4.04297C7.35666 4.04297 4.36964 6.30917 3.29025 9.4593C3.23626 9.61687 3.23626 9.78794 3.29025 9.94552C4.36964 13.0957 7.35666 15.3619 10.8749 15.3619C14.3932 15.3619 17.3802 13.0957 18.4596 9.94555C18.5136 9.78797 18.5136 9.6169 18.4596 9.45932C17.3802 6.30919 14.3932 4.04297 10.8749 4.04297ZM10.8663 7.84413C9.84002 7.84413 9.00808 8.67606 9.00808 9.70231C9.00808 10.7286 9.84002 11.5605 10.8663 11.5605H10.8811C11.9074 11.5605 12.7393 10.7286 12.7393 9.70231C12.7393 8.67606 11.9074 7.84413 10.8811 7.84413H10.8663Z"
                                                    fill=""></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- table item -->
                            <div class="grid grid-cols-11 border-t border-gray-100 px-6 py-[18px] dark:border-gray-800">
                                <div class="col-span-3 flex items-center">
                                    <div class="flex w-full items-center gap-2 text-sm text-gray-700 dark:text-gray-400">
                                        <div>
                                            <img src="src/images/icons/file-video.svg" alt="icon" class="dark:hidden">
                                            <img src="src/images/icons/file-video-dark.svg" alt="icon"
                                                class="hidden dark:block">
                                        </div>

                                        Video_947954_028.mp4
                                    </div>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <p class="text-theme-sm text-gray-700 dark:text-gray-400">Video</p>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <p class="text-theme-sm text-gray-700 dark:text-gray-400">489 MB</p>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <p class="text-theme-sm text-gray-700 dark:text-gray-400">
                                        29 Apr, 2027
                                    </p>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <div class="flex w-full items-center justify-center gap-2">
                                        <button
                                            class="text-gray-500 hover:text-error-500 dark:text-gray-400 dark:hover:text-error-500">
                                            <svg class="fill-current" width="21" height="20" viewBox="0 0 21 20" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M7.4163 3.79199C7.4163 2.54935 8.42366 1.54199 9.6663 1.54199H12.083C13.3256 1.54199 14.333 2.54935 14.333 3.79199V4.04199H16.5H17.5409C17.9551 4.04199 18.2909 4.37778 18.2909 4.79199C18.2909 5.20621 17.9551 5.54199 17.5409 5.54199H17.25V8.24687V13.2469V16.2087C17.25 17.4513 16.2427 18.4587 15 18.4587H6.75004C5.5074 18.4587 4.50004 17.4513 4.50004 16.2087V13.2469V8.24687V5.54199H4.20837C3.79416 5.54199 3.45837 5.20621 3.45837 4.79199C3.45837 4.37778 3.79416 4.04199 4.20837 4.04199H5.25004H7.4163V3.79199ZM15.75 13.2469V8.24687V5.54199H14.333H13.583H8.1663H7.4163H6.00004V8.24687V13.2469V16.2087C6.00004 16.6229 6.33583 16.9587 6.75004 16.9587H15C15.4143 16.9587 15.75 16.6229 15.75 16.2087V13.2469ZM8.9163 4.04199H12.833V3.79199C12.833 3.37778 12.4972 3.04199 12.083 3.04199H9.6663C9.25209 3.04199 8.9163 3.37778 8.9163 3.79199V4.04199ZM9.20837 8.00033C9.62259 8.00033 9.95837 8.33611 9.95837 8.75033V13.7503C9.95837 14.1645 9.62259 14.5003 9.20837 14.5003C8.79416 14.5003 8.45837 14.1645 8.45837 13.7503V8.75033C8.45837 8.33611 8.79416 8.00033 9.20837 8.00033ZM13.2917 8.75033C13.2917 8.33611 12.9559 8.00033 12.5417 8.00033C12.1275 8.00033 11.7917 8.33611 11.7917 8.75033V13.7503C11.7917 14.1645 12.1275 14.5003 12.5417 14.5003C12.9559 14.5003 13.2917 14.1645 13.2917 13.7503V8.75033Z"
                                                    fill=""></path>
                                            </svg>
                                        </button>

                                        <button
                                            class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white/90">
                                            <svg class="fill-current" width="21" height="20" viewBox="0 0 21 20" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M10.8749 13.8619C8.10837 13.8619 5.74279 12.1372 4.79804 9.70241C5.74279 7.26761 8.10837 5.54297 10.8749 5.54297C13.6415 5.54297 16.0071 7.26762 16.9518 9.70243C16.0071 12.1372 13.6415 13.8619 10.8749 13.8619ZM10.8749 4.04297C7.35666 4.04297 4.36964 6.30917 3.29025 9.4593C3.23626 9.61687 3.23626 9.78794 3.29025 9.94552C4.36964 13.0957 7.35666 15.3619 10.8749 15.3619C14.3932 15.3619 17.3802 13.0957 18.4596 9.94555C18.5136 9.78797 18.5136 9.6169 18.4596 9.45932C17.3802 6.30919 14.3932 4.04297 10.8749 4.04297ZM10.8663 7.84413C9.84002 7.84413 9.00808 8.67606 9.00808 9.70231C9.00808 10.7286 9.84002 11.5605 10.8663 11.5605H10.8811C11.9074 11.5605 12.7393 10.7286 12.7393 9.70231C12.7393 8.67606 11.9074 7.84413 10.8811 7.84413H10.8663Z"
                                                    fill=""></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- table item -->
                            <div class="grid grid-cols-11 border-t border-gray-100 px-6 py-[18px] dark:border-gray-800">
                                <div class="col-span-3 flex items-center">
                                    <div class="flex w-full items-center gap-2 text-sm text-gray-700 dark:text-gray-400">
                                        <div>
                                            <img src="src/images/icons/file-image.svg" alt="icon" class="dark:hidden">
                                            <img src="src/images/icons/file-image-dark.svg" alt="icon"
                                                class="hidden dark:block">
                                        </div>

                                        Mountain.png
                                    </div>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <p class="text-theme-sm text-gray-700 dark:text-gray-400">Image</p>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <p class="text-theme-sm text-gray-700 dark:text-gray-400">8 MB</p>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <p class="text-theme-sm text-gray-700 dark:text-gray-400">
                                        20 May, 2027
                                    </p>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <div class="flex w-full items-center justify-center gap-2">
                                        <button
                                            class="text-gray-500 hover:text-error-500 dark:text-gray-400 dark:hover:text-error-500">
                                            <svg class="fill-current" width="21" height="20" viewBox="0 0 21 20" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M7.4163 3.79199C7.4163 2.54935 8.42366 1.54199 9.6663 1.54199H12.083C13.3256 1.54199 14.333 2.54935 14.333 3.79199V4.04199H16.5H17.5409C17.9551 4.04199 18.2909 4.37778 18.2909 4.79199C18.2909 5.20621 17.9551 5.54199 17.5409 5.54199H17.25V8.24687V13.2469V16.2087C17.25 17.4513 16.2427 18.4587 15 18.4587H6.75004C5.5074 18.4587 4.50004 17.4513 4.50004 16.2087V13.2469V8.24687V5.54199H4.20837C3.79416 5.54199 3.45837 5.20621 3.45837 4.79199C3.45837 4.37778 3.79416 4.04199 4.20837 4.04199H5.25004H7.4163V3.79199ZM15.75 13.2469V8.24687V5.54199H14.333H13.583H8.1663H7.4163H6.00004V8.24687V13.2469V16.2087C6.00004 16.6229 6.33583 16.9587 6.75004 16.9587H15C15.4143 16.9587 15.75 16.6229 15.75 16.2087V13.2469ZM8.9163 4.04199H12.833V3.79199C12.833 3.37778 12.4972 3.04199 12.083 3.04199H9.6663C9.25209 3.04199 8.9163 3.37778 8.9163 3.79199V4.04199ZM9.20837 8.00033C9.62259 8.00033 9.95837 8.33611 9.95837 8.75033V13.7503C9.95837 14.1645 9.62259 14.5003 9.20837 14.5003C8.79416 14.5003 8.45837 14.1645 8.45837 13.7503V8.75033C8.45837 8.33611 8.79416 8.00033 9.20837 8.00033ZM13.2917 8.75033C13.2917 8.33611 12.9559 8.00033 12.5417 8.00033C12.1275 8.00033 11.7917 8.33611 11.7917 8.75033V13.7503C11.7917 14.1645 12.1275 14.5003 12.5417 14.5003C12.9559 14.5003 13.2917 14.1645 13.2917 13.7503V8.75033Z"
                                                    fill=""></path>
                                            </svg>
                                        </button>

                                        <button
                                            class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white/90">
                                            <svg class="fill-current" width="21" height="20" viewBox="0 0 21 20" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M10.8749 13.8619C8.10837 13.8619 5.74279 12.1372 4.79804 9.70241C5.74279 7.26761 8.10837 5.54297 10.8749 5.54297C13.6415 5.54297 16.0071 7.26762 16.9518 9.70243C16.0071 12.1372 13.6415 13.8619 10.8749 13.8619ZM10.8749 4.04297C7.35666 4.04297 4.36964 6.30917 3.29025 9.4593C3.23626 9.61687 3.23626 9.78794 3.29025 9.94552C4.36964 13.0957 7.35666 15.3619 10.8749 15.3619C14.3932 15.3619 17.3802 13.0957 18.4596 9.94555C18.5136 9.78797 18.5136 9.6169 18.4596 9.45932C17.3802 6.30919 14.3932 4.04297 10.8749 4.04297ZM10.8663 7.84413C9.84002 7.84413 9.00808 8.67606 9.00808 9.70231C9.00808 10.7286 9.84002 11.5605 10.8663 11.5605H10.8811C11.9074 11.5605 12.7393 10.7286 12.7393 9.70231C12.7393 8.67606 11.9074 7.84413 10.8811 7.84413H10.8663Z"
                                                    fill=""></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- table item -->
                            <div class="grid grid-cols-11 border-t border-gray-100 px-6 py-[18px] dark:border-gray-800">
                                <div class="col-span-3 flex items-center">
                                    <div class="flex w-full items-center gap-2 text-sm text-gray-700 dark:text-gray-400">
                                        <div>
                                            <img src="src/images/icons/file-pdf.svg" alt="icon" class="dark:hidden">
                                            <img src="src/images/icons/file-pdf-dark.svg" alt="icon"
                                                class="hidden dark:block">
                                        </div>

                                        CV.pdf
                                    </div>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <p class="text-theme-sm text-gray-700 dark:text-gray-400">Document</p>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <p class="text-theme-sm text-gray-700 dark:text-gray-400">12 MB</p>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <p class="text-theme-sm text-gray-700 dark:text-gray-400">
                                        17 Jun, 2027
                                    </p>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <div class="flex w-full items-center justify-center gap-2">
                                        <button
                                            class="text-gray-500 hover:text-error-500 dark:text-gray-400 dark:hover:text-error-500">
                                            <svg class="fill-current" width="21" height="20" viewBox="0 0 21 20" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M7.4163 3.79199C7.4163 2.54935 8.42366 1.54199 9.6663 1.54199H12.083C13.3256 1.54199 14.333 2.54935 14.333 3.79199V4.04199H16.5H17.5409C17.9551 4.04199 18.2909 4.37778 18.2909 4.79199C18.2909 5.20621 17.9551 5.54199 17.5409 5.54199H17.25V8.24687V13.2469V16.2087C17.25 17.4513 16.2427 18.4587 15 18.4587H6.75004C5.5074 18.4587 4.50004 17.4513 4.50004 16.2087V13.2469V8.24687V5.54199H4.20837C3.79416 5.54199 3.45837 5.20621 3.45837 4.79199C3.45837 4.37778 3.79416 4.04199 4.20837 4.04199H5.25004H7.4163V3.79199ZM15.75 13.2469V8.24687V5.54199H14.333H13.583H8.1663H7.4163H6.00004V8.24687V13.2469V16.2087C6.00004 16.6229 6.33583 16.9587 6.75004 16.9587H15C15.4143 16.9587 15.75 16.6229 15.75 16.2087V13.2469ZM8.9163 4.04199H12.833V3.79199C12.833 3.37778 12.4972 3.04199 12.083 3.04199H9.6663C9.25209 3.04199 8.9163 3.37778 8.9163 3.79199V4.04199ZM9.20837 8.00033C9.62259 8.00033 9.95837 8.33611 9.95837 8.75033V13.7503C9.95837 14.1645 9.62259 14.5003 9.20837 14.5003C8.79416 14.5003 8.45837 14.1645 8.45837 13.7503V8.75033C8.45837 8.33611 8.79416 8.00033 9.20837 8.00033ZM13.2917 8.75033C13.2917 8.33611 12.9559 8.00033 12.5417 8.00033C12.1275 8.00033 11.7917 8.33611 11.7917 8.75033V13.7503C11.7917 14.1645 12.1275 14.5003 12.5417 14.5003C12.9559 14.5003 13.2917 14.1645 13.2917 13.7503V8.75033Z"
                                                    fill=""></path>
                                            </svg>
                                        </button>

                                        <button
                                            class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white/90">
                                            <svg class="fill-current" width="21" height="20" viewBox="0 0 21 20" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M10.8749 13.8619C8.10837 13.8619 5.74279 12.1372 4.79804 9.70241C5.74279 7.26761 8.10837 5.54297 10.8749 5.54297C13.6415 5.54297 16.0071 7.26762 16.9518 9.70243C16.0071 12.1372 13.6415 13.8619 10.8749 13.8619ZM10.8749 4.04297C7.35666 4.04297 4.36964 6.30917 3.29025 9.4593C3.23626 9.61687 3.23626 9.78794 3.29025 9.94552C4.36964 13.0957 7.35666 15.3619 10.8749 15.3619C14.3932 15.3619 17.3802 13.0957 18.4596 9.94555C18.5136 9.78797 18.5136 9.6169 18.4596 9.45932C17.3802 6.30919 14.3932 4.04297 10.8749 4.04297ZM10.8663 7.84413C9.84002 7.84413 9.00808 8.67606 9.00808 9.70231C9.00808 10.7286 9.84002 11.5605 10.8663 11.5605H10.8811C11.9074 11.5605 12.7393 10.7286 12.7393 9.70231C12.7393 8.67606 11.9074 7.84413 10.8811 7.84413H10.8663Z"
                                                    fill=""></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- table item -->
                            <div class="grid grid-cols-11 border-t border-gray-100 px-6 py-[18px] dark:border-gray-800">
                                <div class="col-span-3 flex items-center">
                                    <div class="flex w-full items-center gap-2 text-sm text-gray-700 dark:text-gray-400">
                                        <div>
                                            <img src="src/images/icons/file-video.svg" alt="icon" class="dark:hidden">
                                            <img src="src/images/icons/file-video-dark.svg" alt="icon"
                                                class="hidden dark:block">
                                        </div>

                                        Video_09783_882943.mp4
                                    </div>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <p class="text-theme-sm text-gray-700 dark:text-gray-400">Video</p>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <p class="text-theme-sm text-gray-700 dark:text-gray-400">309 MB</p>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <p class="text-theme-sm text-gray-700 dark:text-gray-400">
                                        27 Jul, 2027
                                    </p>
                                </div>
                                <div class="col-span-2 flex items-center">
                                    <div class="flex w-full items-center justify-center gap-2">
                                        <button
                                            class="text-gray-500 hover:text-error-500 dark:text-gray-400 dark:hover:text-error-500">
                                            <svg class="fill-current" width="21" height="20" viewBox="0 0 21 20" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M7.4163 3.79199C7.4163 2.54935 8.42366 1.54199 9.6663 1.54199H12.083C13.3256 1.54199 14.333 2.54935 14.333 3.79199V4.04199H16.5H17.5409C17.9551 4.04199 18.2909 4.37778 18.2909 4.79199C18.2909 5.20621 17.9551 5.54199 17.5409 5.54199H17.25V8.24687V13.2469V16.2087C17.25 17.4513 16.2427 18.4587 15 18.4587H6.75004C5.5074 18.4587 4.50004 17.4513 4.50004 16.2087V13.2469V8.24687V5.54199H4.20837C3.79416 5.54199 3.45837 5.20621 3.45837 4.79199C3.45837 4.37778 3.79416 4.04199 4.20837 4.04199H5.25004H7.4163V3.79199ZM15.75 13.2469V8.24687V5.54199H14.333H13.583H8.1663H7.4163H6.00004V8.24687V13.2469V16.2087C6.00004 16.6229 6.33583 16.9587 6.75004 16.9587H15C15.4143 16.9587 15.75 16.6229 15.75 16.2087V13.2469ZM8.9163 4.04199H12.833V3.79199C12.833 3.37778 12.4972 3.04199 12.083 3.04199H9.6663C9.25209 3.04199 8.9163 3.37778 8.9163 3.79199V4.04199ZM9.20837 8.00033C9.62259 8.00033 9.95837 8.33611 9.95837 8.75033V13.7503C9.95837 14.1645 9.62259 14.5003 9.20837 14.5003C8.79416 14.5003 8.45837 14.1645 8.45837 13.7503V8.75033C8.45837 8.33611 8.79416 8.00033 9.20837 8.00033ZM13.2917 8.75033C13.2917 8.33611 12.9559 8.00033 12.5417 8.00033C12.1275 8.00033 11.7917 8.33611 11.7917 8.75033V13.7503C11.7917 14.1645 12.1275 14.5003 12.5417 14.5003C12.9559 14.5003 13.2917 14.1645 13.2917 13.7503V8.75033Z"
                                                    fill=""></path>
                                            </svg>
                                        </button>

                                        <button
                                            class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white/90">
                                            <svg class="fill-current" width="21" height="20" viewBox="0 0 21 20" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M10.8749 13.8619C8.10837 13.8619 5.74279 12.1372 4.79804 9.70241C5.74279 7.26761 8.10837 5.54297 10.8749 5.54297C13.6415 5.54297 16.0071 7.26762 16.9518 9.70243C16.0071 12.1372 13.6415 13.8619 10.8749 13.8619ZM10.8749 4.04297C7.35666 4.04297 4.36964 6.30917 3.29025 9.4593C3.23626 9.61687 3.23626 9.78794 3.29025 9.94552C4.36964 13.0957 7.35666 15.3619 10.8749 15.3619C14.3932 15.3619 17.3802 13.0957 18.4596 9.94555C18.5136 9.78797 18.5136 9.6169 18.4596 9.45932C17.3802 6.30919 14.3932 4.04297 10.8749 4.04297ZM10.8663 7.84413C9.84002 7.84413 9.00808 8.67606 9.00808 9.70231C9.00808 10.7286 9.84002 11.5605 10.8663 11.5605H10.8811C11.9074 11.5605 12.7393 10.7286 12.7393 9.70231C12.7393 8.67606 11.9074 7.84413 10.8811 7.84413H10.8663Z"
                                                    fill=""></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- table body end -->
                        </div>
                    </div>
                </div>
                <!-- ====== Table Seven End -->
            </div>
        </div>
    </div>
@endsection