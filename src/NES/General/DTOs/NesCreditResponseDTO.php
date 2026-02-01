<?php

declare(strict_types=1);

namespace Afea\Einvoice\NES\General\DTOs;

class NesCreditResponseDTO
{
    public int $totalDefinedCount;
    public int $totalUsedCount;
    public int $totalExpiredUnUsedCount;

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->totalDefinedCount = (int) $data['totalDefinedCount'];
        $dto->totalUsedCount = (int) $data['totalUsedCount'];
        $dto->totalExpiredUnUsedCount = (int) $data['totalExpiredUnUsedCount'];

        return $dto;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'totalDefinedCount' => $this->totalDefinedCount,
            'totalUsedCount' => $this->totalUsedCount,
            'totalExpiredUnUsedCount' => $this->totalExpiredUnUsedCount,
        ];
    }
}
