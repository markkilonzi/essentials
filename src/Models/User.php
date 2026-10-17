<?php

namespace Nyumbani\Essentials\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Lab404\Impersonate\Models\Impersonate;
use Nyumbani\Essentials\Traits\HasProfileImage;
use Nyumbani\Essentials\Traits\HasUlids;
use Nyumbani\Essentials\Traits\HasUploads;
use Spatie\Permission\Traits\HasRoles;

/**
 * User model every Nyumbani app extends: a person who can sign in, hold roles and permissions, own uploads and be impersonated.
 * An app adds its own columns with mergeFillable() and its own casts, relationships and status.
 *
 * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
 */
class User extends Authenticatable
{
    use HasFactory, HasProfileImage, HasRoles, HasUlids, HasUploads, Impersonate, Notifiable;

    /**
     * The attributes that are mass assignable
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization
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
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Determine whether this user is a system admin
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function isSystemAdmin(): bool
    {
        return $this->hasRole('system-admin');
    }

    /**
     * Determine whether this user is an admin
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Determine whether this user can impersonate other users
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function canImpersonate(): bool
    {
        return $this->isSystemAdmin();
    }

    /**
     * Determine whether this user can be impersonated
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function canBeImpersonated(): bool
    {
        return ! $this->isSystemAdmin();
    }

    /**
     * Get the default permission names defined for a role in permissions.json
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public static function defaultPermissionsFor(string $role): array
    {
        //  Get the permissions defined by the provided role
        $data = json_decode(
            file_get_contents(database_path('seeders/data/permissions.json')), true
        );

        //  Flatten the grouped permissions
        return collect($data[$role])->flatten()->unique()->values()->toArray();
    }

    /**
     * Assign a role and its default permissions to a user
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function assignDefaultPermissions(string $role): void
    {
        //  Give the user the role
        $this->assignRole($role);

        //  Sync the defined permissions
        $this->syncPermissions(static::defaultPermissionsFor($role));
    }
}
