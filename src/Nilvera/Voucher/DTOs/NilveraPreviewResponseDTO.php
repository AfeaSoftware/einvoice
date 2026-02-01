<?php

declare(strict_types=1);

namespace Afea\Einvoice\Nilvera\Voucher\DTOs;

class NilveraPreviewResponseDTO
{
    public ?string $content = null;

    /**
     * @param string $content HTML content
     */
    public static function fromString(string $content): self
    {
        $dto = new self();
        $dto->content = $content;

        return $dto;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'content' => $this->content,
        ];
    }
}
