<?php

declare(strict_types=1);

namespace Afea\Einvoice\NES\Voucher\DTOs;

class NesCanceledListResponseDTO
{
    public int $page;
    public int $pageSize;
    public int $totalCount;

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $data;

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->page = (int) $data['page'];
        $dto->pageSize = (int) $data['pageSize'];
        $dto->totalCount = (int) $data['totalCount'];
        $dto->data = $data['data'] ?? [];

        return $dto;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'page' => $this->page,
            'pageSize' => $this->pageSize,
            'totalCount' => $this->totalCount,
            'data' => $this->data,
        ];
    }
}
