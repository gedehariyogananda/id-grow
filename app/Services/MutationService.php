<?php

namespace App\Services;

use App\Helper\FormatHelper;
use App\Repositories\MutationRepository;
use App\Repositories\ProductLocationRepository;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MutationService extends BaseService
{
    protected $mutationRepository;
    protected $productLocationRepository;

    public function __construct(MutationRepository $mutationRepository, ProductLocationRepository $productLocationRepository)
    {
        parent::__construct($mutationRepository);
        $this->mutationRepository = $mutationRepository;
        $this->productLocationRepository = $productLocationRepository;
    }

    public function addMutation(array $data)
    {
        $data['mutation_code'] = FormatHelper::generateMutationCode($data['product_location_id']);
        $data['mutation_date'] = now();

        $productLocation = $this->productLocationRepository->find($data['product_location_id']);

        if (!$productLocation) {
            throw new HttpException(404, 'Product location not found');
        }

        if ($data['type'] === 'out') {
            if ($productLocation->stock < $data['quantity']) {
                throw new HttpException(400, 'Insufficient stock for mutation');
            }
        }

        $this->productLocationRepository->updateStock(
            $data['product_location_id'],
            $data['quantity'],
            $data['type'] === 'in'
        );

        $storeMutation = $this->mutationRepository->create($data);
        if (!$storeMutation) {
            throw new HttpException(500, 'Failed to create mutation');
        }

        return $storeMutation;
    }

    public function updateMutation(int $id, array $data)
    {
        $mutation = $this->mutationRepository->find($id);
        if (!$mutation) {
            throw new HttpException(404, 'Mutation not found');
        }

        $productLocation = $this->productLocationRepository->find($mutation->product_location_id);
        if (!$productLocation) {
            throw new HttpException(404, 'Product location not found');
        }
        if ($data['type'] !== $mutation->type || $data['quantity'] !== $mutation->quantity) {
            $this->productLocationRepository->updateStock(
                $mutation->product_location_id,
                $mutation->quantity,
                $mutation->type === 'out'
            );

            if ($data['type'] === 'out' && $productLocation->stock < $data['quantity']) {
                throw new HttpException(400, 'Insufficient stock for mutation');
            }

            $this->productLocationRepository->updateStock(
                $mutation->product_location_id,
                $data['quantity'],
                $data['type'] === 'in'
            );
        }

        $this->mutationRepository->update($id, $data);

        return $this->mutationRepository->find($id);
    }

    public function deleteMutation(int $id)
    {
        $mutation = $this->mutationRepository->find($id);
        if (!$mutation) {
            throw new HttpException(404, 'Mutation not found');
        }

        $productLocation = $this->productLocationRepository->find($mutation->product_location_id);
        if (!$productLocation) {
            throw new HttpException(404, 'Product location not found');
        }

        $this->productLocationRepository->updateStock(
            $mutation->product_location_id,
            $mutation->quantity,
            $mutation->type === 'out'
        );

        return $this->mutationRepository->delete($id);
    }
}
