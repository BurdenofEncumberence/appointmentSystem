<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * Bootstrap the model and its traits.
     */
    protected static function booted(): void
    {
        static::saving(function (User $user) {
            $computedName = trim(implode(' ', array_filter([$user->first_name, $user->middle_name, $user->last_name])));

            if ($user->name && $user->name !== $computedName && ($user->isDirty('name') || empty($computedName))) {
                $nameParts = preg_split('/\s+/', trim((string) $user->name));
                $user->first_name = array_shift($nameParts) ?: null;
                $user->last_name = ! empty($nameParts) ? array_pop($nameParts) : null;
                $user->middle_name = ! empty($nameParts) ? implode(' ', $nameParts) : null;
            } elseif ($user->first_name || $user->last_name) {
                $parts = array_filter(
                    [$user->first_name, $user->middle_name, $user->last_name],
                    fn ($part) => ! empty(trim((string) $part))
                );
                $user->name = implode(' ', $parts);
            }
        });
    }

    /**
     * Get the user's full name.
     */
    public function getNameAttribute(?string $value): ?string
    {
        if (! empty($value)) {
            return $value;
        }

        $parts = array_filter(
            [$this->attributes['first_name'] ?? null, $this->attributes['middle_name'] ?? null, $this->attributes['last_name'] ?? null],
            fn ($part) => ! empty(trim((string) $part))
        );
        $fullName = implode(' ', $parts);

        return $fullName !== '' ? $fullName : null;
    }

    /**
     * Get the user's first name with fallback to split name.
     */
    public function getFirstNameAttribute(?string $value): ?string
    {
        if (! empty($value)) {
            return $value;
        }

        if (! empty($this->attributes['name'] ?? null)) {
            $parts = explode(' ', trim((string) $this->attributes['name']), 2);
            return $parts[0] ?? null;
        }

        return null;
    }

    /**
     * Get the user's last name with fallback to split name.
     */
    public function getLastNameAttribute(?string $value): ?string
    {
        if (! empty($value)) {
            return $value;
        }

        if (! empty($this->attributes['name'] ?? null)) {
            $parts = explode(' ', trim((string) $this->attributes['name']), 2);
            return $parts[1] ?? null;
        }

        return null;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function hasRole(string|array $roles): bool
    {
        return in_array($this->role, (array) $roles, true);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isStaff(): bool
    {
        return $this->hasRole(['staff', 'admin', 'manager']);
    }

    public function hasPermission(string $permission): bool
    {
        $permissions = [
            'admin' => ['view_admin_dashboard', 'manage_courts', 'view_finances'],
            'manager' => ['view_admin_dashboard', 'manage_courts', 'view_finances'],
            'staff' => ['view_today_bookings', 'manage_booking_status'],
            'player' => [],
        ];

        return in_array('*', $permissions[$this->role] ?? [], true)
            || in_array($permission, $permissions[$this->role] ?? [], true);
    }
}
