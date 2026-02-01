<?php

declare(strict_types=1);

namespace Afea\Einvoice\NES\Voucher\DTOs;

class NesRevertCancelResponseDTO
{
    /**
     * @var array<string>
     */
    public array $succeededUuids = [];

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $failedUuids = [];

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->succeededUuids = $data['succeededUuids'] ?? [];
        $dto->failedUuids = $data['failedUuids'] ?? [];

        return $dto;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'succeededUuids' => $this->succeededUuids,
            'failedUuids' => $this->failedUuids,
        ];
    }

    /**
     * Check if all operations succeeded
     */
    public function isFullySuccessful(): bool
    {
        return !empty($this->succeededUuids) && empty($this->failedUuids);
    }

    /**
     * Check if there are any failures
     */
    public function hasFailures(): bool
    {
        return !empty($this->failedUuids);
    }
}
