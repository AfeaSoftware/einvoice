<?php

declare(strict_types=1);

namespace Afea\Einvoice\Nilvera\General\DTOs;

class NilveraCreditResponseDTO
{
    /**
     * @var array<int, array<string, mixed>>
     */
    public array $credits;

    /**
     * @param array<int, array<string, mixed>> $data
     */
    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->credits = $data;

        return $dto;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function toArray(): array
    {
        return $this->credits;
    }
}
