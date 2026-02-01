<?php

declare(strict_types=1);

namespace Afea\Einvoice\NES\Voucher\DTOs;

class NesPreviewResponseDTO
{
    public ?string $content = null;
    public bool $isUsingDefaultTemplate = false;
    public ?string $errorMessage = null;

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->content = isset($data['content']) ? (string) $data['content'] : null;
        $dto->isUsingDefaultTemplate = isset($data['isUsingDefaultTemplate']) ? (bool) $data['isUsingDefaultTemplate'] : false;
        $dto->errorMessage = isset($data['errorMessage']) ? (string) $data['errorMessage'] : null;

        return $dto;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'content' => $this->content,
            'isUsingDefaultTemplate' => $this->isUsingDefaultTemplate,
            'errorMessage' => $this->errorMessage,
        ];
    }
}
