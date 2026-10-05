<?php

namespace App\Http\Controllers;

use App\Models\SiteSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminCustomizationController extends Controller
{
    public function index()
    {
        $settings = SiteSettings::firstOrCreate([]);
        return view('admin.customization.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'business_name' => 'nullable|string|max:255',
            'system_name' => 'nullable|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'email_address' => 'nullable|email|max:255',
            'contact_number' => 'nullable|string|max:50',
            'business_address' => 'nullable|string|max:500',
            'facebook_link' => 'nullable|url|max:255',
            'twitter_link' => 'nullable|url|max:255',
            'instagram_link' => 'nullable|url|max:255',
            'linkedin_link' => 'nullable|url|max:255',
            'logo' => 'nullable|image|max:5120',
        ]);

        $settings = SiteSettings::firstOrCreate([]);

        $settings->business_name = $request->business_name;
        $settings->system_name = $request->system_name;
        $settings->tagline = $request->tagline;
        $settings->email_address = $request->email_address;
        $settings->contact_number = $request->contact_number;
        $settings->business_address = $request->business_address;
        $settings->facebook_link = $request->facebook_link;
        $settings->twitter_link = $request->twitter_link;
        $settings->instagram_link = $request->instagram_link;
        $settings->linkedin_link = $request->linkedin_link;

        if ($request->hasFile('logo')) {
            if ($settings->logo) {
                Storage::disk('public')->delete($settings->logo);
            }
            $logoPath = $request->file('logo')->store('logos', 'public');
            $settings->logo = $logoPath;
        }

        $settings->save();

        return redirect()->route('admin.customization.index')->with('success', 'Settings updated successfully.');
    }
}
