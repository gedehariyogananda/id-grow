<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'users';
    protected $fillable = [
        'name',
        'email',
        'password',
        'refresh_token',
    ];
    protected $hidden = [
        'password',
        'remember_token',
        'refresh_token',
    ];
    protected $fields = [
        'id',
        'name',
        'email',
    ];
    static $allowedParams = [
        'search',
        'sortby',
        'order',
        'fields',
        'name',
        'email',
        'embed'
    ];

    public function scopeOptions($query, $options = [])
    {
        if (isset($options['search'])) {
            $search = strtolower(trim($options['search']));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"]);
            });
        }

        if (isset($options['sortby']) && in_array($options['sortby'], $this->fields)) {
            if (!isset($options['order'])) {
                $options['order'] = 'ASC';
            }
            $order = strtoupper($options['order'] ?? 'ASC');
            $query->orderBy($options['sortby'], $order);
        } else {
            $query->orderBy('id', 'ASC');
        }

        if (isset($options['fields'])) {
            $fields = explode(',', $options['fields']);
            $fields = array_intersect($fields, $this->fields);

            if (count($fields) > 0) {
                $query->select($fields);
            }
        }

        if (isset($options['name'])) {
            $query->where('name', $options['name']);
        }

        if (isset($options['email'])) {
            $query->where('email', $options['email']);
        }

        if (isset($options['embed'])) {
            if ($options['embed'] === 'mutations') {
                $query->select(['id', 'name', 'email'])
                    ->with([
                        'mutations' => function ($query) {
                            $query->select(['id', 'user_id', 'mutation_code', 'mutation_date', 'type', 'quantity', 'note']);
                        }
                    ]);
            }
        }

        return $query;
    }

    public function mutations()
    {
        return $this->hasMany(Mutation::class, 'user_id');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function scopeWithAllRelations($query)
    {
        return $query->select(['id', 'name', 'email'])
            ->with([
                'mutations' => function ($query) {
                    $query->select(['id', 'user_id', 'mutation_code', 'mutation_date', 'type', 'quantity', 'note']);
                }
            ]);
    }
}
