<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow sm:rounded-lg">
                <div class="p-6 text-gray-900">


                    <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">
                            {{ __('User Registration') }}
                        </h2>
                    </header>

                    <form method="post" action="{{ route('admin.register') }}" class="mt-6 space-y-6">
                        @csrf

                        <div class="row g-3">
                            <!-- Name -->
                            <div class="col-md-6">
                                <x-input-label for="name" :value="__('Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required autofocus />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>
                        

                            <!-- Email -->
                            <div class="col-md-6">
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" name="email" type="text" class="mt-1 block w-full" required autofocus />
                                <x-input-error class="mt-2" :messages="$errors->get('email')" />
                            </div>

                            <!-- Password -->
                            <div class="col-md-6">
                                <x-input-label for="password" :value="__('Password')" />
                                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                                <x-input-error class="mt-2" :messages="$errors->get('password')" />
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-md-6">
                                <x-input-label for="conpassword" :value="__('Confirm Password')" />
                                <x-text-input id="conpassword" name="conpassword" type="password" class="mt-1 block w-full" required />
                                <x-input-error class="mt-2" :messages="$errors->get('conpassword')" />
                            </div>

                            <!-- Role (full width) -->
                            <div class="col-md-6">
                                <x-input-label for="role" :value="__('Role')" />
                                <x-select-input id="role" name="role" type="text" class="mt-1 block w-full" 
                                    :options="[
                                        'Admin' => 'Admin',
                                        'Cashier' => 'Cashier',
                                        'Inventory' => 'Inventory',
                                        'Ordering' => 'Ordering',
                                    ]"
                                    required/>
                                <x-input-error class="mt-2" :messages="$errors->get('role')" />
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
                                {{ __('Client List') }}
                            </h2>
                        </header>

                        <!-- Responsive Table -->
                        <div class="overflow-x-auto max-h-96 overflow-y-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full border-collapse text-sm sm:text-base">
                                <thead class="bg-gray-100 sticky top-0 text-xs sm:text-sm shadow-md">
                                    <tr>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Name</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Email</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-left font-medium border">Role</th>
                                        <th class="px-2 py-1 sm:px-4 sm:py-2 text-center font-medium border">Option</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y text-xs sm:text-sm">
                                    <tr class="hover:bg-gray-100">
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">Juan Dela Cruz</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">Cagayan de Oro</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">Cashier</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 text-center border">
                                            <button class="px-2 py-1 sm:px-3 sm:py-1 text-[10px] sm:text-xs text-white bg-blue-600 rounded hover:bg-blue-700">Edit</button>
                                            <button class="px-2 py-1 sm:px-3 sm:py-1 text-[10px] sm:text-xs text-white bg-red-600 rounded hover:bg-red-700">Delete</button>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-gray-100">
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">Pedro Santos</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">Iligan City</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">Ordering</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 text-center border">
                                            <button class="px-2 py-1 sm:px-3 sm:py-1 text-[10px] sm:text-xs text-white bg-blue-600 rounded hover:bg-blue-700">Edit</button>
                                            <button class="px-2 py-1 sm:px-3 sm:py-1 text-[10px] sm:text-xs text-white bg-red-600 rounded hover:bg-red-700">Delete</button>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-gray-100">
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">Jose Rizal</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">Dapitan</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 border">Inventory</td>
                                        <td class="px-2 py-1 sm:px-4 sm:py-2 text-center border">
                                            <button class="px-2 py-1 sm:px-3 sm:py-1 text-[10px] sm:text-xs text-white bg-blue-600 rounded hover:bg-blue-700">Edit</button>
                                            <button class="px-2 py-1 sm:px-3 sm:py-1 text-[10px] sm:text-xs text-white bg-red-600 rounded hover:bg-red-700">Delete</button>
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
