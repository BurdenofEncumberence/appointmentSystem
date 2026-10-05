<x-admin-layout>
    <x-slot name="heading">Customization</x-slot>

    <div class="gz-container">
        <form method="POST" action="{{ route('admin.customization.update') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('patch')

            {{-- Business Information --}}
            <div class="gz-panel">
                <div class="gz-panel-header">
                    <h3 class="gz-eyebrow">Business Identity</h3>
                </div>
                <div class="gz-panel-body space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="gz-label">Business Name</label>
                            <input
                                type="text"
                                name="business_name"
                                class="gz-input"
                                value="{{ old('business_name', $settings->business_name ?? '') }}"
                                placeholder="e.g., KYMNET Pickleball"
                            >
                        </div>
                        <div>
                            <label class="gz-label">System Name</label>
                            <input
                                type="text"
                                name="system_name"
                                class="gz-input"
                                value="{{ old('system_name', $settings->system_name ?? '') }}"
                                placeholder="e.g., KYMNET"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="gz-label">Tagline</label>
                        <input
                            type="text"
                            name="tagline"
                            class="gz-input"
                            value="{{ old('tagline', $settings->tagline ?? '') }}"
                            placeholder="e.g., Your court era starts now"
                        >
                    </div>

                    <div>
                        <label class="gz-label">Business Logo</label>
                        <div class="mt-2">
                            @if($settings->logo)
                                <div class="mb-4">
                                    <img src="{{ asset('storage/' . $settings->logo) }}" alt="Current Logo" class="h-24 rounded-lg border" style="border-color: var(--gz-border);">
                                    <p class="text-sm mt-2" style="color: var(--gz-muted);">Current logo</p>
                                </div>
                            @endif
                            <input
                                type="file"
                                name="logo"
                                class="gz-input"
                                accept="image/*"
                            >
                            <p class="gz-hint">Upload a new logo to replace the current one. Max size: 5MB.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contact Information --}}
            <div class="gz-panel">
                <div class="gz-panel-header">
                    <h3 class="gz-eyebrow">Contact Information</h3>
                </div>
                <div class="gz-panel-body space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="gz-label">Email Address</label>
                            <input
                                type="email"
                                name="email_address"
                                class="gz-input"
                                value="{{ old('email_address', $settings->email_address ?? '') }}"
                                placeholder="e.g., info@kymnet.com"
                            >
                        </div>
                        <div>
                            <label class="gz-label">Contact Number</label>
                            <input
                                type="text"
                                name="contact_number"
                                class="gz-input"
                                value="{{ old('contact_number', $settings->contact_number ?? '') }}"
                                placeholder="e.g., +63 912 345 6789"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="gz-label">Business Address</label>
                        <textarea
                            name="business_address"
                            class="gz-input"
                            rows="3"
                            placeholder="e.g., 123 Main Street, Davao City, Philippines"
                        >{{ old('business_address', $settings->business_address ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Social Media Links --}}
            <div class="gz-panel">
                <div class="gz-panel-header">
                    <h3 class="gz-eyebrow">Social Media Links</h3>
                </div>
                <div class="gz-panel-body space-y-6">
                    <div>
                        <label class="gz-label">Facebook</label>
                        <input
                            type="url"
                            name="facebook_link"
                            class="gz-input"
                            value="{{ old('facebook_link', $settings->facebook_link ?? '') }}"
                            placeholder="https://facebook.com/yourbusiness"
                        >
                    </div>

                    <div>
                        <label class="gz-label">Twitter</label>
                        <input
                            type="url"
                            name="twitter_link"
                            class="gz-input"
                            value="{{ old('twitter_link', $settings->twitter_link ?? '') }}"
                            placeholder="https://twitter.com/yourbusiness"
                        >
                    </div>

                    <div>
                        <label class="gz-label">Instagram</label>
                        <input
                            type="url"
                            name="instagram_link"
                            class="gz-input"
                            value="{{ old('instagram_link', $settings->instagram_link ?? '') }}"
                            placeholder="https://instagram.com/yourbusiness"
                        >
                    </div>

                    <div>
                        <label class="gz-label">LinkedIn</label>
                        <input
                            type="url"
                            name="linkedin_link"
                            class="gz-input"
                            value="{{ old('linkedin_link', $settings->linkedin_link ?? '') }}"
                            placeholder="https://linkedin.com/company/yourbusiness"
                        >
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-4">
                <a href="{{ route('admin.dashboard') }}" class="gz-btn-outline">Cancel</a>
                <button type="submit" class="gz-btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</x-admin-layout>
