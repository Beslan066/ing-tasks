@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-gray-300 block w-full p-2.5  dark:bg-gray-700 dark:border-green-600 dark:placeholder-gray-400 dark:text-white outline-none
']) }}>
