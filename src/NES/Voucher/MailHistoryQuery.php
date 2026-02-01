<?php

declare(strict_types=1);

namespace Afea\Einvoice\NES\Voucher;

use Afea\Einvoice\Common\HttpClient;
use Afea\Einvoice\NES\Voucher\DTOs\NesMailHistoryResponseDTO;

class MailHistoryQuery
{
    protected HttpClient $httpClient;
    protected string $uuid;
    protected ?int $pageSize = null;
    protected ?int $page = null;
    protected string $sort = 'CreatedAt desc';

    public function __construct(HttpClient $httpClient, string $uuid)
    {
        $this->httpClient = $httpClient;
        $this->uuid = $uuid;
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

    public function sort(string $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get mail history for the voucher
     *
     * @return NesMailHistoryResponseDTO
     */
    public function get(): NesMailHistoryResponseDTO
    {
        $query = [
            'sort' => $this->sort,
        ];

        if ($this->pageSize !== null) {
            $query['pageSize'] = $this->pageSize;
        }

        if ($this->page !== null) {
            $query['page'] = $this->page;
        }

        $response = $this->httpClient->get("/esmm/v1/vouchers/{$this->uuid}/mailhistories", $query);

        return NesMailHistoryResponseDTO::fromArray($response);
    }
}
