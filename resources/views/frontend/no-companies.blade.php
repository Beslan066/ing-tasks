@extends('layouts.app')

@section('content')
    <!-- Hero -->
    <div
        class="relative overflow-hidden before:absolute before:top-0 before:start-1/2 before:bg-[url('https://preline.co/assets/svg/examples/polygon-bg-element.svg')] dark:before:bg-[url('https://preline.co/assets/svg/examples-dark/polygon-bg-element.svg')] before:bg-no-repeat before:bg-top before:bg-cover before:size-full before:-z-1 before:transform before:-translate-x-1/2">
        <div class="max-w-[85rem] mx-auto ">

            <div class="text-[#1b1b18]">
                {{auth()->user()->userSupervisorCount}}
            </div>
            <!-- Announcement Banner -->

            <!-- End Announcement Banner -->

            <!-- Title -->
            <div class="mt-5 max-w-2xl text-center mx-auto">
                <h1 class="block font-bold text-gray-800 text-4xl md:text-5xl lg:text-6xl dark:text-neutral-200">
                    Еще немного!
                    Чтобы начать работу вам необходимо создать компанию.
                </h1>
            </div>
            <!-- End Title -->

            <!-- Buttons -->

            <!-- End Buttons -->
        </div>

    </div>

    <div class="mt-8 gap-3 flex justify-center">

        <a href="{{route('companies.create')}}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 flex items-center">Создать

            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
            </svg>

        </a>
    </div>
    <!-- End Hero -->
@endsection
