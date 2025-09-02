<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Request') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">
                            {{ __('Request Form') }}
                        </h2>
                    </header>

                    <form method="post" action="{{ route('admin.register') }}" class="mt-6 space-y-6">
                        @csrf

                        <div class="row g-3">

                            <div class="col-md-6">
                                <x-input-label for="requesttype" :value="__('Request Type')" />
                                <x-select-input id="requesttype" name="requesttype" class="mt-1 block w-full"
                                    :options="[
                                        'Return' => 'Return',
                                        'Client' => 'Client',
                                        'Order' => 'Order',
                                    ]"
                                    required/>

                                <x-input-error class="mt-2" :messages="$errors->get('payment')" />
                            </div>

                        </div>


                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Save') }}</x-primary-button>

                            @if (session('status') === 'profile-updated')
                                <p
                                    x-data="{ show: true }"
                                    x-show="show"
                                    x-transition
                                    x-init="setTimeout(() => show = false, 2000)"
                                    class="text-sm text-gray-600 dark:text-gray-400"
                                >{{ __('Saved.') }}</p>
                            @endif
                        </div>


                    </form>
                </section>
                </div>
            </div>
        </div>
    </div>

    <div class="py-1">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white text-black shadow sm:rounded-lg">
                <div class="max-w-7xl">
                    <section>
                        <header class="mb-4">
                            <h2 class="text-lg font-medium text-black">
                                {{ __('Request List') }}
                            </h2>
                        </header>

                        <!-- Responsive Table -->
                        <div class="overflow-x-auto max-h-96 overflow-y-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full border-collapse text-sm sm:text-base">
                                <thead class="bg-gray-100 sticky top-0 text-xs sm:text-sm shadow-md">
                                    <tr>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Requester</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Request Type</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Date</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Description</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-center font-medium border">Option</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y text-xs sm:text-sm">
                                    <tr class="hover:bg-gray-100">
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">Juan Dela Cruz</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">Return</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">01/09/2025</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">Return 2 2kg Petron Gasul Defective</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 text-center border">
                                            <button class="px-2 py-1 sm:px-3 sm:py-1 text-[10px] sm:text-xs text-white bg-blue-600 rounded hover:bg-blue-700">Approve</button>
                                            <button class="px-2 py-1 sm:px-3 sm:py-1 text-[10px] sm:text-xs text-white bg-red-600 rounded hover:bg-red-700">Deny</button>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-gray-100">
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">Pedro Santos</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">Client</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">01/09/2025</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">Change name Client 0001</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 text-center border">
                                            <button class="px-2 py-1 sm:px-3 sm:py-1 text-[10px] sm:text-xs text-white bg-blue-600 rounded hover:bg-blue-700">Approve</button>
                                            <button class="px-2 py-1 sm:px-3 sm:py-1 text-[10px] sm:text-xs text-white bg-red-600 rounded hover:bg-red-700">Deny</button>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-gray-100">
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">Jose Rizal</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">Order</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">01/09/2025</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">Cancelled Order</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 text-center border">
                                            <button class="px-2 py-1 sm:px-3 sm:py-1 text-[10px] sm:text-xs text-white bg-blue-600 rounded hover:bg-blue-700">Approve</button>
                                            <button class="px-2 py-1 sm:px-3 sm:py-1 text-[10px] sm:text-xs text-white bg-red-600 rounded hover:bg-red-700">Deny</button>
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
