<?php

declare(strict_types=1);

namespace Afea\Einvoice\NES\Voucher\DTOs;

class NesVoucherStatusResponseDTO
{
    /**
     * @var array<string, mixed> Full voucher data from pagination response
     */
    public array $data;

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->data = $data;

        return $dto;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Get status code from voucher data
     */
    public function getStatusCode(): ?string
    {
        return $this->data['statusCode'] ?? null;
    }

    /**
     * Get status detail from voucher data
     */
    public function getStatusDetail(): ?string
    {
        return $this->data['statusDetail'] ?? null;
    }

    /**
     * Check if voucher is canceled
     */
    public function isCanceled(): bool
    {
        return ($this->data['cancelStatus'] ?? false) === true;
    }
}
