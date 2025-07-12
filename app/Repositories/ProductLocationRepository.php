<?php

namespace App\Repositories;

use App\Models\ProductLocation;

class ProductLocationRepository extends BaseRepository
{
    public function __construct(ProductLocation $productLocation)
    {
        parent::__construct($productLocation);
    }

    public function updateStock($id, $quantity, $isInStock = true)
    {
        $data = $this->model->find($id);

        $this->model->where('id', $id)->update([
            'stock' => $isInStock ? $data->stock + $quantity : $data->stock - $quantity,
        ]);
    }
}
