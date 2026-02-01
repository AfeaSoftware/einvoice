<?php

declare(strict_types=1);

namespace Afea\Einvoice\NES\Voucher\DTOs;

class NesSendResponseDTO
{
    public string $uuid;
    public ?string $documentNumber = null;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $preview = null;

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->uuid = (string) $data['uuid'];
        $dto->documentNumber = isset($data['documentNumber']) ? (string) $data['documentNumber'] : null;
        $dto->preview = $data['preview'] ?? null;

        return $dto;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'documentNumber' => $this->documentNumber,
            'preview' => $this->preview,
        ];
    }
}
