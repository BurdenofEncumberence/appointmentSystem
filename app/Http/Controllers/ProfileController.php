<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (array_key_exists('first_name', $data)) {
            $data['first_name'] = User::titleCaseName($data['first_name']);
        }
        if (array_key_exists('middle_name', $data)) {
            $data['middle_name'] = User::titleCaseName($data['middle_name']);
        }
        if (array_key_exists('last_name', $data)) {
            $data['last_name'] = User::titleCaseName($data['last_name']);
        }

        if (isset($data['first_name']) || isset($data['last_name'])) {
            $parts = array_filter(
                [$data['first_name'] ?? null, $data['middle_name'] ?? null, $data['last_name'] ?? null],
                fn ($part) => ! empty(trim((string) $part))
            );
            $data['name'] = implode(' ', $parts);
        } elseif (array_key_exists('name', $data)) {
            $data['name'] = User::titleCaseName($data['name']);
        }

        $request->user()->fill($data);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
