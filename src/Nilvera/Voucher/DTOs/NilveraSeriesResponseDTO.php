<?php

declare(strict_types=1);

namespace Afea\Einvoice\Nilvera\Voucher\DTOs;

class NilveraSeriesResponseDTO
{
    public ?int $page = null;
    public ?int $pageSize = null;
    public ?int $totalCount = null;
    public ?int $totalPages = null;

    /**
     * @var array<int, array<string, mixed>>|null
     */
    public ?array $content = null;

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->page = isset($data['Page']) ? (int) $data['Page'] : null;
        $dto->pageSize = isset($data['PageSize']) ? (int) $data['PageSize'] : null;
        $dto->totalCount = isset($data['TotalCount']) ? (int) $data['TotalCount'] : null;
        $dto->totalPages = isset($data['TotalPages']) ? (int) $data['TotalPages'] : null;
        $dto->content = $data['Content'] ?? null;

        return $dto;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'Page' => $this->page,
            'PageSize' => $this->pageSize,
            'TotalCount' => $this->totalCount,
            'TotalPages' => $this->totalPages,
            'Content' => $this->content,
        ];
    }
}
