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
     * Roles del sistema (SCRUM-24 / CMS-06):
     *  - admin:    gestiona todo (usuarios, roles, eliminar productos, configuración).
     *  - vendedor: personal interno; gestiona productos y ventas. Solo lo asigna un admin.
     *  - cliente:  cualquier persona que se registra desde el sitio público. Sin acceso al panel.
     */
    public const ROLE_ADMIN = 'admin';
    public const ROLE_VENDEDOR = 'vendedor';
    public const ROLE_CLIENTE = 'cliente';

    public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_VENDEDOR,
        self::ROLE_CLIENTE,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * 'role' NO está aquí a propósito: así nadie puede auto-asignarse un rol
     * enviando un campo "role" extra en el formulario de registro
     * (mass assignment). El rol solo se cambia con forceFill() desde
     * código que ya verificó que quien lo hace es admin.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Valor por defecto en memoria (coincide con el default de la BD),
     * para que $user->role ya sea 'cliente' justo después de create().
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'role' => self::ROLE_CLIENTE,
    ];

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

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Atajo para no repetir "$user->role === 'admin'" en Policies y vistas.
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isVendedor(): bool
    {
        return $this->role === self::ROLE_VENDEDOR;
    }

    /**
     * Personal interno de la ferretería (admin o vendedor):
     * son los únicos que pueden entrar al panel de productos/ventas.
     */
    public function isStaff(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_VENDEDOR], true);
    }
}
