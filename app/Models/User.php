<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// បន្ថែម 'role' ទៅក្នុង Fillable Attribute របស់ Laravel 11
#[Fillable(['name', 'email', 'password', 'role', 'shop_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

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

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS FOR USER ROLES
    |--------------------------------------------------------------------------
    */

    /**
     * ពិនិត្យថាតើជា Admin ឬទេ
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * ពិនិត្យថាតើជា Staff ឬទេ
     */
    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isOwner(): bool
    {
        return in_array($this->role, ['owner', 'shop_owner']);
    }

    /**
     * ពិនិត្យថាតើជា Cashier ឬទេ (គាំទ្រទាំង 'cashier' និង 'user')
     */
    public function isCashier(): bool
    {
        return in_array($this->role, ['cashier', 'user']);
    }

    /**
     * ពិនិត្យលក្ខខណ្ឌសិទ្ធិជាច្រើនក្នុងពេលតែមួយ
     */
    public function hasAnyRole(array $roles): bool
    {
        // បើជា admin គឺមានសិទ្ធិទាំងអស់ដោយស្វ័យប្រវត្ត
        if ($this->isAdmin()) {
            return true;
        }

        return in_array($this->role, $roles);
    }
}
