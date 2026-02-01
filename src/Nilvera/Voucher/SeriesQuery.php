<?php

declare(strict_types=1);

namespace Afea\Einvoice\Nilvera\Voucher;

use Afea\Einvoice\Common\HttpClient;
use Afea\Einvoice\Nilvera\Voucher\DTOs\NilveraSeriesResponseDTO;

class SeriesQuery
{
    protected HttpClient $httpClient;
    protected ?string $search = null;
    protected ?int $pageSize = null;
    protected ?int $page = null;
    protected ?string $sortColumn = null;
    protected ?string $sortType = null;
    protected ?bool $isActive = null;
    protected ?bool $isDefault = null;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function search(string $search): self
    {
        $this->search = $search;

        return $this;
    }

    public function pageSize(int $size): self
    {
        $this->pageSize = $size;

        return $this;
    }

    public function page(int $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function sortColumn(string $column): self
    {
        $this->sortColumn = $column;

        return $this;
    }

    public function sortType(string $type): self
    {
        $this->sortType = $type;

        return $this;
    }

    public function isActive(bool $active): self
    {
        $this->isActive = $active;

        return $this;
    }

    public function isDefault(bool $default): self
    {
        $this->isDefault = $default;

        return $this;
    }

    public function get(): NilveraSeriesResponseDTO
    {
        $query = [];

        if ($this->search !== null) {
            $query['Search'] = $this->search;
        }

        if ($this->pageSize !== null) {
            $query['PageSize'] = $this->pageSize;
        }

        if ($this->page !== null) {
            $query['Page'] = $this->page;
        }

        if ($this->sortColumn !== null) {
            $query['SortColumn'] = $this->sortColumn;
        }

        if ($this->sortType !== null) {
            $query['SortType'] = $this->sortType;
        }

        if ($this->isActive !== null) {
            $query['IsActive'] = $this->isActive ? 'true' : 'false';
        }

        if ($this->isDefault !== null) {
            $query['IsDefault'] = $this->isDefault ? 'true' : 'false';
        }

        $response = $this->httpClient->get('/evoucher/Series', $query);

        return NilveraSeriesResponseDTO::fromArray($response);
    }
}
