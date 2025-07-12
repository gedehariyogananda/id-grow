<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $fillable = [
        'product_code',
        'name_product',
        'category_id',
        'unit_id',
    ];

    protected $fields = [
        'id',
        'product_code',
        'name_product',
        'category_id',
        'unit_id',
    ];
    static $allowedParams = [
        'search',
        'sortby',
        'order',
        'fields',
        'product_code',
        'name_product',
        'category_id',
        'unit_id',
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

        if (isset($options['product_code'])) {
            $query->where('product_code', $options['product_code']);
        }

        if (isset($options['name_product'])) {
            $query->where('name_product', $options['name_product']);
        }

        if (isset($options['category_id'])) {
            $query->where('category_id', $options['category_id']);
        }

        if (isset($options['unit_id'])) {
            $query->where('unit_id', $options['unit_id']);
        }

        if (isset($options['embed'])) {
            if ($options['embed'] === 'mutations') {
                $query->with([
                    'unit:id,name',
                    'category:id,name',
                    'productLocations' => function ($q) {
                        $q->select(['id', 'product_id', 'location_id', 'stock']);
                    },
                    'productLocations.location' => function ($q) {
                        $q->select(['id', 'location_code', 'location_name']);
                    },
                    'productLocations.mutations' => function ($q) {
                        $q->select(['id', 'product_location_id', 'mutation_code', 'mutation_date', 'type', 'quantity', 'note'])
                            ->orderBy('mutation_date', 'desc');
                    },
                ]);
            }
        }

        return $query;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function productLocations()
    {
        return $this->hasMany(ProductLocation::class);
    }

    public function scopeWithAllRelations($query)
    {
        return $query->with([
            'unit:id,name',
            'category:id,name',
            'productLocations' => function ($q) {
                $q->select(['id', 'product_id', 'location_id', 'stock']);
            },
            'productLocations.location' => function ($q) {
                $q->select(['id', 'location_code', 'location_name']);
            },
            'productLocations.mutations' => function ($q) {
                $q->select(['id', 'product_location_id', 'mutation_code', 'mutation_date', 'type', 'quantity', 'note'])
                    ->orderBy('mutation_date', 'desc');
            },
        ]);
    }
}
