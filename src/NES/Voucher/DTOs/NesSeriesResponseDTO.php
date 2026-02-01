<?php

declare(strict_types=1);

namespace Afea\Einvoice\NES\Voucher\DTOs;

class NesSeriesResponseDTO
{
    /**
     * @var array<int, array<string, mixed>>
     */
    public array $series;

    /**
     * @param array<int, array<string, mixed>> $data
     */
    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->series = $data;

        return $dto;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function toArray(): array
    {
        return $this->series;
    }
}
