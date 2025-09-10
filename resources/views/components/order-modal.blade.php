<div 
    x-data="{ open: @entangle($attributes->wire('model')) }" 
    x-show="open" 
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    x-transition
>
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl p-6 relative">
        <button 
            @click="open = false" 
            class="absolute top-2 right-2 text-gray-500 hover:text-gray-700"
        >
            ✖
        </button>

        {{ $slot }}
    </div>
</div>
