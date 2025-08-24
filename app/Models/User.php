<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'schema_name',
        'pg_username',  // We'll keep the username for reference
    ];

    protected $table = 'user';
    
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    
    /**
     * Get the schema route associated with the user.
     */
    public function schemaRoute()
    {
        return $this->hasOne(SchemaRoute::class);
    }

    /**
     * Assign roles to the user
     */
    public function assignRoles(... $roles)
    {
        $roles = $this->getRoles($roles);
        
        if (empty($roles)) {
            return $this;
        }
        
        $this->roles()->syncWithoutDetaching($roles);
        
        return $this;
    }

    /**
     * Remove roles from the user
     */
    public function removeRoles(... $roles)
    {
        $roles = $this->getRoles($roles);
        
        if (empty($roles)) {
            return $this;
        }
        
        $this->roles()->detach($roles);
        
        return $this;
    }

    /**
     * Sync roles for the user
     */
    public function syncRoles(... $roles)
    {
        $roles = $this->getRoles($roles);
        
        if (empty($roles)) {
            return $this;
        }
        
        $this->roles()->sync($roles);
        
        return $this;
    }

    /**
     * Check if the user has any of the given roles
     */
    public function hasAnyRole(... $roles)
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if the user has all of the given roles
     */
    public function hasAllRoles(... $roles)
    {
        foreach ($roles as $role) {
            if (!$this->hasRole($role)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Get all roles from array of role names/slugs/IDs
     */
    protected function getRoles(array $roles)
    {
        if (empty($roles)) {
            return [];
        }
        
        // Если массив уже содержит объекты Role, просто возвращаем его
        if (isset($roles[0]) && is_object($roles[0])) {
            return $roles;
        }
        
        // Разделяем входные данные на числовые ID и строковые slug'и
        $roleIds = [];
        $roleSlugs = [];

        foreach ($roles as $role) {
            if (is_numeric($role)) {
                $roleIds[] = (int) $role;
            } else if (is_string($role)) {
                $roleSlugs[] = $role;
            }
        }

        // Строим запрос, используя только подходящие данные
        $query = Role::query();

        if (!empty($roleSlugs)) {
            // Ищем по slug, если переданы строки
            $query->whereIn('slug', $roleSlugs);
        }

        if (!empty($roleIds)) {
            // Ищем по id, если переданы числа
            $query->orWhereIn('id', $roleIds);
        }
        
        return $query->get();
    }


    public function isSuperuser()
    {
        // Cache the result to avoid repeated database queries
        return cache()->remember("user_{$this->id}_is_superuser", now()->addMinutes(60), function () {
            return $this->hasRole('superuser');
        });
    }

    /**
     * Check if the user has a specific role by name or ID
     */
    public function hasRole($role)
    {
        // --- НАЧАЛО ОТЛАДКИ ---
        // try {
        //     $currentSearchPath = \Illuminate\Support\Facades\DB::selectOne("show search_path")->search_path;
        //     $connectionConfig = config('database.connections.' . \Illuminate\Support\Facades\DB::getDefaultConnection());
        //     dd(
        //         "Текущий search_path в БД:", 
        //         $currentSearchPath, 
        //         "Конфигурация соединения Laravel:", 
        //         $connectionConfig
        //     );
        // } catch (\Exception $e) {
        //     dd("Не удалось выполнить отладочный запрос: " . $e->getMessage());
        // }
        // --- КОНЕЦ ОТЛАДКИ ---

        if (is_string($role)) {
            return $this->roles()->where('slug', $role)->exists();
        }
        
        return $this->roles->contains($role);
    }

    /**
     * Get the roles that belong to the user
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    /**
     * Check if the user has a specific permission
     * This checks permissions from roles.
     */
    public function hasPermissionTo(string $routeName): bool
    {
        // Superusers bypass all permission checks
        if ($this->isSuperuser()) {
            return true;
        }
        
        foreach ($this->roles as $role) {
            if ($role->hasPermission($routeName)) {
                return true;
            }
        }

        return false;
    }
}