<x-app-layout>
    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-8">
            {{-- Profile Information Card --}}
            <div class="surface-card p-8">
                <div class="flex items-start gap-4 mb-6">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900 dark:to-green-800 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-xl mb-1" style="color: var(--gz-ink);">
                            {{ __('Profile Information') }}
                        </h3>
                        <p class="text-sm" style="color: var(--gz-muted);">
                            {{ __("Update your account's profile information and email address.") }}
                        </p>
                    </div>
                </div>

                <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                    @csrf
                </form>

                <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
                    @csrf
                    @method('patch')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="first_name" class="block text-sm font-semibold mb-2" style="color: var(--gz-ink);">
                                {{ __('First Name') }}
                            </label>
                            <input
                                id="first_name"
                                name="first_name"
                                type="text"
                                class="w-full px-4 py-3 rounded-lg border focus:outline-none focus:ring-2 transition-all"
                                style="background: var(--gz-surface); border-color: var(--gz-border); color: var(--gz-ink);"
                                :value="old('first_name', $user->first_name)"
                                required
                                autofocus
                                autocomplete="given-name"
                                onfocus="this.style.borderColor='var(--pop)'"
                                onblur="this.style.borderColor='var(--gz-border)'"
                            >
                            @error('first_name')
                                <p class="mt-2 text-sm" style="color: var(--gz-danger);">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="middle_name" class="block text-sm font-semibold mb-2" style="color: var(--gz-ink);">
                                {{ __('Middle Name (Optional)') }}
                            </label>
                            <input
                                id="middle_name"
                                name="middle_name"
                                type="text"
                                class="w-full px-4 py-3 rounded-lg border focus:outline-none focus:ring-2 transition-all"
                                style="background: var(--gz-surface); border-color: var(--gz-border); color: var(--gz-ink);"
                                :value="old('middle_name', $user->middle_name)"
                                autocomplete="additional-name"
                                onfocus="this.style.borderColor='var(--pop)'"
                                onblur="this.style.borderColor='var(--gz-border)'"
                            >
                            @error('middle_name')
                                <p class="mt-2 text-sm" style="color: var(--gz-danger);">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-semibold mb-2" style="color: var(--gz-ink);">
                            {{ __('Last Name') }}
                        </label>
                        <input
                            id="last_name"
                            name="last_name"
                            type="text"
                            class="w-full px-4 py-3 rounded-lg border focus:outline-none focus:ring-2 transition-all"
                            style="background: var(--gz-surface); border-color: var(--gz-border); color: var(--gz-ink);"
                            :value="old('last_name', $user->last_name)"
                            required
                            autocomplete="family-name"
                            onfocus="this.style.borderColor='var(--pop)'"
                            onblur="this.style.borderColor='var(--gz-border)'"
                        >
                        @error('last_name')
                            <p class="mt-2 text-sm" style="color: var(--gz-danger);">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold mb-2" style="color: var(--gz-ink);">
                            {{ __('Email') }}
                        </label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            class="w-full px-4 py-3 rounded-lg border focus:outline-none focus:ring-2 transition-all"
                            style="background: var(--gz-surface); border-color: var(--gz-border); color: var(--gz-ink);"
                            :value="old('email', $user->email)"
                            required
                            autocomplete="username"
                            onfocus="this.style.borderColor='var(--pop)'"
                            onblur="this.style.borderColor='var(--gz-border)'"
                        >
                        @error('email')
                            <p class="mt-2 text-sm" style="color: var(--gz-danger);">{{ $message }}</p>
                        @enderror

                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <div class="mt-3 p-3 rounded-lg" style="background: rgba(229, 168, 35, 0.1); border: 1px solid var(--pop);">
                                <p class="text-sm" style="color: var(--gz-ink);">
                                    {{ __('Your email address is unverified.') }}

                                    <button form="send-verification" class="underline font-semibold" style="color: var(--pop-dark);">
                                        {{ __('Click here to re-send the verification email.') }}
                                    </button>
                                </p>

                                @if (session('status') === 'verification-link-sent')
                                    <p class="mt-2 text-sm font-semibold" style="color: var(--pop-dark);">
                                        {{ __('A new verification link has been sent to your email address.') }}
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-4 pt-4">
                        <button type="submit" class="gz-btn-primary">
                            {{ __('Save') }}
                        </button>

                        @if (session('status') === 'profile-updated')
                            <p
                                x-data="{ show: true }"
                                x-show="show"
                                x-transition
                                x-init="setTimeout(() => show = false, 2000)"
                                class="text-sm font-semibold"
                                style="color: var(--pop-dark);"
                            >{{ __('Saved.') }}</p>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Update Password Card --}}
            <div class="surface-card p-8">
                <div class="flex items-start gap-4 mb-6">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900 dark:to-blue-800 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-xl mb-1" style="color: var(--gz-ink);">
                            {{ __('Update Password') }}
                        </h3>
                        <p class="text-sm" style="color: var(--gz-muted);">
                            {{ __('Ensure your account is using a long, random password to stay secure.') }}
                        </p>
                    </div>
                </div>

                <form method="post" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf
                    @method('put')

                    <div>
                        <label for="update_password_current_password" class="block text-sm font-semibold mb-2" style="color: var(--gz-ink);">
                            {{ __('Current Password') }}
                        </label>
                        <input
                            id="update_password_current_password"
                            name="current_password"
                            type="password"
                            class="w-full px-4 py-3 rounded-lg border focus:outline-none focus:ring-2 transition-all"
                            style="background: var(--gz-surface); border-color: var(--gz-border); color: var(--gz-ink);"
                            autocomplete="current-password"
                            onfocus="this.style.borderColor='var(--pop)'"
                            onblur="this.style.borderColor='var(--gz-border)'"
                        >
                        @error('updatePassword.current_password')
                            <p class="mt-2 text-sm" style="color: var(--gz-danger);">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="update_password_password" class="block text-sm font-semibold mb-2" style="color: var(--gz-ink);">
                            {{ __('New Password') }}
                        </label>
                        <input
                            id="update_password_password"
                            name="password"
                            type="password"
                            class="w-full px-4 py-3 rounded-lg border focus:outline-none focus:ring-2 transition-all"
                            style="background: var(--gz-surface); border-color: var(--gz-border); color: var(--gz-ink);"
                            autocomplete="new-password"
                            onfocus="this.style.borderColor='var(--pop)'"
                            onblur="this.style.borderColor='var(--gz-border)'"
                        >
                        @error('updatePassword.password')
                            <p class="mt-2 text-sm" style="color: var(--gz-danger);">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="update_password_password_confirmation" class="block text-sm font-semibold mb-2" style="color: var(--gz-ink);">
                            {{ __('Confirm Password') }}
                        </label>
                        <input
                            id="update_password_password_confirmation"
                            name="password_confirmation"
                            type="password"
                            class="w-full px-4 py-3 rounded-lg border focus:outline-none focus:ring-2 transition-all"
                            style="background: var(--gz-surface); border-color: var(--gz-border); color: var(--gz-ink);"
                            autocomplete="new-password"
                            onfocus="this.style.borderColor='var(--pop)'"
                            onblur="this.style.borderColor='var(--gz-border)'"
                        >
                        @error('updatePassword.password_confirmation')
                            <p class="mt-2 text-sm" style="color: var(--gz-danger);">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-4 pt-4">
                        <button type="submit" class="gz-btn-primary">
                            {{ __('Save') }}
                        </button>

                        @if (session('status') === 'password-updated')
                            <p
                                x-data="{ show: true }"
                                x-show="show"
                                x-transition
                                x-init="setTimeout(() => show = false, 2000)"
                                class="text-sm font-semibold"
                                style="color: var(--pop-dark);"
                            >{{ __('Saved.') }}</p>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Delete Account Card --}}
            <div class="surface-card p-8" style="border-color: var(--gz-danger);">
                <div class="flex items-start gap-4 mb-6">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-100 to-red-200 dark:from-red-900 dark:to-red-800 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-xl mb-1" style="color: var(--gz-danger);">
                            {{ __('Delete Account') }}
                        </h3>
                        <p class="text-sm" style="color: var(--gz-muted);">
                            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}
                        </p>
                    </div>
                </div>

                <form method="post" action="{{ route('profile.destroy') }}" class="space-y-5">
                    @csrf
                    @method('delete')

                    <div>
                        <label for="delete_password" class="block text-sm font-semibold mb-2" style="color: var(--gz-ink);">
                            {{ __('Password') }}
                        </label>
                        <input
                            id="delete_password"
                            name="password"
                            type="password"
                            class="w-full px-4 py-3 rounded-lg border focus:outline-none focus:ring-2 transition-all"
                            style="background: var(--gz-surface); border-color: var(--gz-border); color: var(--gz-ink);"
                            placeholder="{{ __('Enter your password to confirm') }}"
                            required
                            onfocus="this.style.borderColor='var(--gz-danger)'"
                            onblur="this.style.borderColor='var(--gz-border)'"
                        >
                        @error('userDeletion.password')
                            <p class="mt-2 text-sm" style="color: var(--gz-danger);">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-4 pt-4">
                        <button type="submit" class="px-6 py-3 rounded-lg font-bold text-sm transition-all" style="background: var(--gz-danger); color: white; box-shadow: 0 4px 0 rgba(239, 68, 68, 0.3);">
                            {{ __('Delete Account') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
