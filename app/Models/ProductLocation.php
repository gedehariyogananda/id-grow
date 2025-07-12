<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductLocation extends Model
{
    use HasFactory;

    protected $table = 'product_locations';
    protected $fillable = [
        'product_id',
        'location_id',
        'stock',
    ];

    protected $fields = [
        'id',
        'product_id',
        'location_id',
        'stock',
    ];
    static $allowedParams = [
        'search',
        'sortby',
        'order',
        'fields',
        'product_id',
        'location_id',
        'stock',
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

        if (isset($options['product_id'])) {
            $query->where('product_id', $options['product_id']);
        }

        if (isset($options['location_id'])) {
            $query->where('location_id', $options['location_id']);
        }

        if (isset($options['stock'])) {
            $query->where('stock', $options['stock']);
        }

        return $query;
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function mutations()
    {
        return $this->hasMany(Mutation::class, 'product_location_id');
    }

    public function scopeWithAllRelations($query)
    {
        return $query->with(['product', 'location']);
    }
}
