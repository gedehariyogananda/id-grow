<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mutation extends Model
{
    use HasFactory;

    protected $table = 'mutations';
    protected $fillable = [
        'user_id',
        'product_location_id',
        'mutation_code',
        'mutation_date',
        'type',
        'quantity',
        'note',
    ];

    protected $fields = [
        'id',
        'user_id',
        'product_location_id',
        'mutation_code',
        'mutation_date',
        'type',
        'quantity',
        'note',
    ];
    static $allowedParams = [
        'search',
        'sortby',
        'order',
        'fields',
        'user_id',
        'product_location_id',
        'mutation_code',
        'mutation_date',
        'type',
        'quantity',
        'note',
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

        if (isset($options['user_id'])) {
            $query->where('user_id', $options['user_id']);
        }

        if (isset($options['product_location_id'])) {
            $query->where('product_location_id', $options['product_location_id']);
        }

        if (isset($options['mutation_code'])) {
            $query->where('mutation_code', $options['mutation_code']);
        }

        if (isset($options['mutation_date'])) {
            $query->where('mutation_date', $options['mutation_date']);
        }

        if (isset($options['type'])) {
            $query->where('type', $options['type']);
        }

        if (isset($options['quantity'])) {
            $query->where('quantity', $options['quantity']);
        }

        if (isset($options['note'])) {
            $query->where('note', $options['note']);
        }

        if (isset($options['embed'])) {
            if ($options['embed'] === 'productLocation') {
                $query->with('productLocation:id,product_id,location_id,stock', 'user:id,name,email');
            }
        }

        return $query;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function productLocation()
    {
        return $this->belongsTo(ProductLocation::class, 'product_location_id');
    }

    public function scopeWithAllRelations($query)
    {
        return $query->with(['user', 'productLocation']);
    }
}
