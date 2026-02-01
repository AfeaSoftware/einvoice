<?php

declare(strict_types=1);

namespace Afea\Einvoice\Nilvera\Voucher\DTOs;

class NilveraMailHistoryResponseDTO
{
    /**
     * @var array<int, array<string, mixed>>
     */
    public array $histories;

    /**
     * @param array<int, array<string, mixed>> $data
     */
    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->histories = $data;

        return $dto;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function toArray(): array
    {
        return $this->histories;
    }
}
