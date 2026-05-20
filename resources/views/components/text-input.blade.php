@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-bedas-500 focus:ring-bedas-500 rounded-md shadow-sm']) }}>