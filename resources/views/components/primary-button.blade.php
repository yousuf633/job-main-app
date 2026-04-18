<button {{ $attributes->merge(['type' => 'submit', 'class' => 'rounded-lg justify-center bg-gradient-to-r from-indigo-500 to-rose-500 text-white transition hover:from-indigo-600 hover:to-rose-600']) }}>
    {{ $slot }}
</button>
