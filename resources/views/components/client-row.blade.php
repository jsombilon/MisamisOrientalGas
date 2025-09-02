@props(['clientnumber', 'name', 'location', 'contact', 'contactperson', 'paymenttype'])

<div class="grid grid-cols-12 border-b border-gray-200">
    <div class="col-span-1 px-2 md:px-4 py-2 text-xs md:text-base font-medium">
        {{ $clientnumber }}
    </div>
    <div class="col-span-2 px-2 md:px-4 py-2 text-xs md:text-base">
        {{ $name }}
    </div>
    <div class="col-span-2 px-2 md:px-4 py-2 text-xs md:text-base">
        {{ $location }}
    </div>
    <div class="col-span-2 px-2 md:px-4 py-2 text-xs md:text-base">
        {{ $contact }}
    </div>
    <div class="col-span-2 px-2 md:px-4 py-2 text-xs md:text-base">
        {{ $contactperson }}
    </div>
    <div class="col-span-1 px-2 md:px-4 py-2 text-xs md:text-base">
        {{ $paymenttype }}
    </div>
    <div class="col-span-2 px-2 md:px-4 py-2 text-center space-x-1">
        <button class="px-2 md:px-3 py-1 text-xs md:text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700">
            Edit
        </button>
        <button class="px-2 md:px-3 py-1 text-xs md:text-sm text-white bg-red-600 rounded-md hover:bg-red-700">
            Delete
        </button>
    </div>
</div>
