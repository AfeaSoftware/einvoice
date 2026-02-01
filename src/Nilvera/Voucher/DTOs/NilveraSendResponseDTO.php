<?php

declare(strict_types=1);

namespace Afea\Einvoice\Nilvera\Voucher\DTOs;

class NilveraSendResponseDTO
{
    public string $uuid;
    public ?string $voucherNumber = null;

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->uuid = (string) $data['UUID'];
        $dto->voucherNumber = isset($data['VoucherNumber']) ? (string) $data['VoucherNumber'] : null;

        return $dto;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'UUID' => $this->uuid,
            'VoucherNumber' => $this->voucherNumber,
        ];
    }
}
