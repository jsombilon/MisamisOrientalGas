<div>
    
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Client') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">
                            {{ __('Client Registration') }}
                        </h2>
                    </header>

                    <div wire:key="form-{{ $formKey }}">
                        <form wire:submit.prevent="register" class="mt-6 space-y-6">
                            @csrf

                            <div class="row g-3">

                                <div class="col-md-6">
                                    <x-input-label for="clientnum" :value="__('Client Number')" />
                                    <input id="clientnum" type="text" wire:model.defer="clientnum" class="mt-1 block w-full border rounded-lg px-3 py-2 text-sm" required autofocus autocomplete="off" />
                                    <x-input-error class="mt-2" :messages="$errors->get('clientnum')" />
                                </div>

                                <div class="col-md-6">
                                    <x-input-label for="name" :value="__('Name')" />
                                    <input id="name" type="text" wire:model.defer="name" class="mt-1 block w-full border rounded-lg px-3 py-2 text-sm" required autocomplete="off"/>
                                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                </div>

                                <div class="col-md-6">
                                    <x-input-label for="location" :value="__('Location')" />
                                    <input id="location" type="text" wire:model.defer="location" class="mt-1 block w-full border rounded-lg px-3 py-2 text-sm" required autocomplete="off"/>
                                    <x-input-error class="mt-2" :messages="$errors->get('location')" />
                                </div>

                                <div class="col-md-6">
                                    <x-input-label for="contact" :value="__('Contact')" />
                                    <input id="contact" type="text" wire:model.defer="contact" placeholder="09XXXXXXXXX or +639XXXXXXXXX" class="mt-1 block w-full border rounded-lg px-3 py-2 text-sm" autocomplete="off"/>
                                    <x-input-error class="mt-2" :messages="$errors->get('contact')" />
                                </div>

                                <div class="col-md-6">
                                    <x-input-label for="contactper" :value="__('Contact Person')" />
                                    <input id="contactper" type="text" wire:model.defer="contactper" class="mt-1 block w-full border rounded-lg px-3 py-2 text-sm" autocomplete="off"/>
                                    <x-input-error class="mt-2" :messages="$errors->get('contactper')" />
                                </div>

                                <div class="col-md-6">
                                    <label class="block text-sm font-medium mb-1" for="payment">Payment Type</label>
                                    <select id="payment" wire:model.defer="payment" class="mt-1 block w-full border rounded-lg px-3 py-2 text-sm">
                                        <option value="">-- Select --</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Post Date Check">Post Date Check</option>
                                        <option value="On Date Check">On Date Check</option>
                                        <option value="Charge">Charge</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('payment')" />
                                </div>

                            </div>


                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Save') }}</x-primary-button>

                                @if (session('status') === 'success')
                                    <p
                                        x-data="{ show: true }"
                                        x-show="show"
                                        x-transition
                                        x-init="setTimeout(() => show = false, 2000)"
                                        class="text-sm text-red-600 dark:text-red-400"
                                    >{{ __('Client Added Successfully!.') }}</p>
                                @endif
                            </div>
                        </form>
                    </div>
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
                                {{ __('Client List') }}
                            </h2>
                        </header>

                        <!-- Responsive Table -->
                        <div class="overflow-x-auto max-h-96 overflow-y-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full border-collapse text-sm sm:text-base">
                                <thead class="bg-gray-100 sticky top-0 text-xs sm:text-sm shadow-md">
                                    <tr>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Client Number</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Name</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Location</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Contact</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Contact Person</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Payment Type</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-center font-medium border">Option</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y text-xs sm:text-sm">
                                    @forelse ($clients as $client)
                                        <tr class="hover:bg-gray-100">
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">{{ $client->client_number }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">{{ $client->client_name }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">{{ $client->location }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">{{ $client->contact }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">{{ $client->contact_person }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 border">{{ $client->payment_type }}</td>
                                            <td class="px-2 py-1 sm:px-4 sm:py-2 text-center border">
                                                <button class="px-2 py-1 sm:px-3 sm:py-1 text-[10px] sm:text-xs text-white bg-blue-600 rounded hover:bg-blue-700">Edit</button>
                                                <button class="px-2 py-1 sm:px-3 sm:py-1 text-[10px] sm:text-xs text-white bg-red-600 rounded hover:bg-red-700">Delete</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-2 py-4 text-center text-gray-500">No clients found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>
