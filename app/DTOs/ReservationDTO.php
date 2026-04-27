<?php

namespace App\DTOs;

class ReservationDTO
{
    public function __construct(
        public readonly int $tour_id,
        public readonly string $seats,
        public readonly string $name,
        public readonly string $phone,
        public readonly string $email,
        public readonly ?string $whatsapp = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            tour_id: (int) $data['tour_id'],
            seats: $data['seats'],
            name: $data['name'],
            phone: $data['phone'],
            email: $data['email'],
            whatsapp: $data['whatsapp'] ?? null
        );
    }
}
