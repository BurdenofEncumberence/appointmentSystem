<x-admin-layout>
    <x-slot name="heading">{{ $court->exists ? 'Edit Court' : 'Add New Court' }}</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="mb-4">
            <a href="{{ route('admin.courts.index') }}" class="font-pixel text-[9px] text-stone-700 hover:text-black">
                ← BACK TO COURT INVENTORY
            </a>
        </div>

        <div class="pixel-border p-6" style="background: var(--cream);">
            <div class="border-b-2 pb-4 mb-6" style="border-color: var(--ink);">
                <span class="font-pixel text-[9px] px-2 py-0.5 inline-block pixel-border mb-2" style="background: var(--jade); color: var(--cream);">
                    ARENA SETUP
                </span>
                <h2 class="font-pixel text-base" style="color: var(--ink);">
                    {{ $court->exists ? 'UPDATE COURT: ' . strtoupper($court->court_name) : 'REGISTER NEW COURT' }}
                </h2>
                <p class="text-base text-stone-600 mt-1">
                    Set operational details and pricing displayed in the court appointment system.
                </p>
            </div>

            <form method="POST" action="{{ $court->exists ? route('admin.courts.update', $court) : route('admin.courts.store') }}" class="space-y-5">
                @csrf
                @if($court->exists)
                    @method('PUT')
                @endif

                {{-- Court Name --}}
                <div>
                    <label for="court_name" class="block font-pixel text-[10px] mb-2" style="color: var(--ink);">
                        COURT NAME *
                    </label>
                    <input id="court_name"
                           name="court_name"
                           type="text"
                           value="{{ old('court_name', $court->court_name) }}"
                           placeholder="e.g. Center Court, Court 5"
                           required
                           class="pixel-input">
                    <x-input-error :messages="$errors->get('court_name')" class="mt-2 font-pixel text-[9px]" />
                </div>

                {{-- Court Size and Price Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="size" class="block font-pixel text-[10px] mb-2" style="color: var(--ink);">
                            COURT DIMENSIONS / TYPE
                        </label>
                        <input id="size"
                               name="size"
                               type="text"
                               value="{{ old('size', $court->size) }}"
                               placeholder="e.g. Standard / Doubles"
                               class="pixel-input">
                        <x-input-error :messages="$errors->get('size')" class="mt-2 font-pixel text-[9px]" />
                    </div>

                    <div>
                        <label for="price_per_hour" class="block font-pixel text-[10px] mb-2" style="color: var(--ink);">
                            RATE PER HOUR (PHP) *
                        </label>
                        <input id="price_per_hour"
                               name="price_per_hour"
                               type="number"
                               min="0"
                               step="0.01"
                               value="{{ old('price_per_hour', $court->price_per_hour) }}"
                               placeholder="e.g. 350.00"
                               required
                               class="pixel-input">
                        <x-input-error :messages="$errors->get('price_per_hour')" class="mt-2 font-pixel text-[9px]" />
                    </div>
                </div>

                {{-- Court Status --}}
                <div>
                    <label for="court_status" class="block font-pixel text-[10px] mb-2" style="color: var(--ink);">
                        OPERATIONAL STATUS *
                    </label>
                    <select id="court_status" name="court_status" class="pixel-input cursor-pointer">
                        <option value="available" @selected(old('court_status', $court->court_status ?: 'available') === 'available')>
                            AVAILABLE (Open for Public Reservations)
                        </option>
                        <option value="maintenance" @selected(old('court_status', $court->court_status) === 'maintenance')>
                            MAINTENANCE (Temporarily Unavailable)
                        </option>
                        <option value="closed" @selected(old('court_status', $court->court_status) === 'closed')>
                            CLOSED (Offline / Archived)
                        </option>
                    </select>
                    <x-input-error :messages="$errors->get('court_status')" class="mt-2 font-pixel text-[9px]" />
                </div>

                {{-- Form Actions --}}
                <div class="pt-6 border-t-2 flex flex-col-reverse sm:flex-row sm:justify-end gap-3" style="border-color: var(--ink);">
                    <a href="{{ route('admin.courts.index') }}" class="pixel-btn text-center text-[10px] py-2.5 px-4" style="background: var(--parchment); color: var(--ink);">
                        CANCEL
                    </a>
                    <button type="submit" class="pixel-btn text-[10px] py-2.5 px-5" style="background: var(--jade); color: var(--cream);">
                        {{ $court->exists ? 'SAVE CHANGES' : 'CREATE COURT' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
