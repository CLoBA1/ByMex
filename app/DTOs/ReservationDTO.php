<?php

namespace App\DTOs;

class ReservationDTO
{
    public function __construct(
        public readonly int $tour_id,
        public readonly string $seats,
        public readonly string $name,
        public readonly string $whatsapp,
        public readonly ?string $phone = null,
        public readonly ?string $email = null,
        public readonly ?array $passengers = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            tour_id:    (int) $data['tour_id'],
            seats:      $data['seats'],
            name:       $data['name'],
            whatsapp:   $data['whatsapp'],
            phone:      $data['phone'] ?? null,
            email:      $data['email'] ?? null,
            passengers: $data['passengers'] ?? null
        );
    }
}
