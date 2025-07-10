<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $table = 'locations';
    protected $fillable = [
        'location_code',
        'location_name',
    ];

    protected $fields = [
        'id',
        'location_code',
        'location_name',
    ];
    static $allowedParams = [
        'search',
        'sortby',
        'order',
        'fields',
        'location_code',
        'location_name',
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

        if (isset($options['location_code'])) {
            $query->where('location_code', $options['location_code']);
        }

        if (isset($options['location_name'])) {
            $query->where('location_name', $options['location_name']);
        }

        return $query;
    }

    public function scopeWithAllRelations($query)
    {
        return $query;
    }
}
