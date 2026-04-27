<?php

namespace App\Services;

use App\Repositories\Contracts\TourRepositoryInterface;

class TourService
{
    protected $repository;

    public function __construct(TourRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get active tours for public catalog
     */
    public function getActiveTours()
    {
        return $this->repository->getActiveTours();
    }
}
