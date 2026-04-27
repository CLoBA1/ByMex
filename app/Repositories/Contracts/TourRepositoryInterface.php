<?php

namespace App\Repositories\Contracts;

interface TourRepositoryInterface
{
    public function getActiveTours();
    public function getAllToursWithStats();
    public function findTourWithReservations(int $id);
    public function createTour(array $data);
    public function updateTour(int $id, array $data);
    public function deleteTour(int $id);
}
