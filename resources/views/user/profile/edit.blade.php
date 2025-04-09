<x-user-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Alumni Information') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ __("Update your alumni profile information.") }}
                            </p>
                        </header>

                        <form method="post" action="{{ route('user.profile.update') }}" class="mt-6 space-y-6">
                            @csrf
                            @method('patch')

                            <div>
                                <x-input-label for="student_id" :value="__('Student ID')" />
                                <x-text-input id="student_id" name="student_id" type="text" class="mt-1 block w-full" :value="old('student_id', $user->student_id)" required autofocus />
                                <x-input-error class="mt-2" :messages="$errors->get('student_id')" />
                            </div>

                            <div>
                                <x-input-label for="fullname" :value="__('Full Name')" />
                                <x-text-input id="fullname" name="fullname" type="text" class="mt-1 block w-full" :value="old('fullname', $user->fullname)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('fullname')" />
                            </div>

                            <div>
                                <x-input-label for="age" :value="__('Age')" />
                                <x-text-input id="age" name="age" type="number" class="mt-1 block w-full" :value="old('age', $user->age)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('age')" />
                            </div>

                            <div>
                                <x-input-label for="gender" :value="__('Gender')" />
                                <select id="gender" name="gender" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="Male" {{ old('gender', $user->gender) === 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender', $user->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('gender')" />
                            </div>

                            <div>
                                <x-input-label for="course" :value="__('Course')" />
                                <x-text-input id="course" name="course" type="text" class="mt-1 block w-full" :value="old('course', $user->course)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('course')" />
                            </div>

                            <div>
                                <x-input-label for="section" :value="__('Section')" />
                                <x-text-input id="section" name="section" type="text" class="mt-1 block w-full" :value="old('section', $user->section)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('section')" />
                            </div>

                            <div>
                                <x-input-label for="batch" :value="__('Batch')" />
                                <x-text-input id="batch" name="batch" type="text" class="mt-1 block w-full" :value="old('batch', $user->batch)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('batch')" />
                            </div>

                            <div>
                                <x-input-label for="contact" :value="__('Contact')" />
                                <x-text-input id="contact" name="contact" type="text" class="mt-1 block w-full" :value="old('contact', $user->contact)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('contact')" />
                            </div>

                            <div>
                                <x-input-label for="address" :value="__('Address')" />
                                <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $user->address)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('address')" />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('Email Address')" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('email')" />
                            </div>

                            <div>
                                <x-input-label for="occupation" :value="__('Occupation')" />
                                <x-text-input id="occupation" name="occupation" type="text" class="mt-1 block w-full" :value="old('occupation', $user->occupation)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('occupation')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Save') }}</x-primary-button>

                                @if (session('status') === 'profile-updated')
                                    <p
                                        x-data="{ show: true }"
                                        x-show="show"
                                        x-transition
                                        x-init="setTimeout(() => show = false, 2000)"
                                        class="text-sm text-gray-600"
                                    >{{ __('Saved.') }}</p>
                                @endif
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div x-data="{ show: false }" 
         x-show="show" 
         x-init="@if(session('status') === 'profile-updated') show = true; setTimeout(() => show = false, 2000) @endif"
         class="fixed inset-0 flex items-center justify-center z-50"
         style="display: none;">
        <div class="bg-white rounded-lg p-6 shadow-xl border-2 border-green-500">
            <div class="flex items-center text-green-600">
                <svg class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <p class="text-lg font-semibold">Profile Updated Successfully!</p>
            </div>
        </div>
    </div>
</x-user-layout>