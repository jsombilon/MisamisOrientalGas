<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ledger') }}
        </h2>
    </x-slot>

        <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white text-black shadow sm:rounded-lg">
                <div class="max-w-7xl">
                    <section>
                        <header class="mb-4">
                            <h2 class="text-lg font-medium text-black">
                                {{ __('Ledger List') }}
                            </h2>
                        </header>

                        <!-- Responsive Table -->
                        <div class="overflow-x-auto max-h-96 overflow-y-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full border-collapse text-sm sm:text-base">
                                <thead class="bg-gray-100 sticky top-0 text-xs sm:text-sm shadow-md">
                                    <tr>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Client Number</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Name</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Address</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-center font-medium border">Option</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y text-xs sm:text-sm">
                                    <tr class="hover:bg-gray-100">
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">0001</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">Juan Dela Cruz</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">Cagayan de Oro</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 text-center border">
                                            <button class="px-2 py-1 sm:px-3 sm:py-1 text-[10px] sm:text-xs text-white bg-blue-600 rounded hover:bg-blue-700">View</button>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-gray-100">
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">0002</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">Pedro Santos</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">Iligan City</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 text-center border">
                                            <button class="px-2 py-1 sm:px-3 sm:py-1 text-[10px] sm:text-xs text-white bg-blue-600 rounded hover:bg-blue-700">View</button>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-gray-100">
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">0003</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">Jose Rizal</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">Dapitan</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 text-center border">
                                            <button class="px-2 py-1 sm:px-3 sm:py-1 text-[10px] sm:text-xs text-white bg-blue-600 rounded hover:bg-blue-700">View</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
