<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSettings extends Model
{
    protected $fillable = [
        'business_name',
        'system_name',
        'tagline',
        'email_address',
        'contact_number',
        'business_address',
        'facebook_link',
        'twitter_link',
        'instagram_link',
        'linkedin_link',
        'logo',
    ];
}
