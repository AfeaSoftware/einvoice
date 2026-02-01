<?php

declare(strict_types=1);

namespace Afea\Einvoice\NES\Voucher;

use Afea\Einvoice\Common\HttpClient;
use Afea\Einvoice\NES\Voucher\DTOs\NesSeriesResponseDTO;

class SeriesQuery
{
    protected HttpClient $httpClient;
    protected ?string $status = null;
    protected ?string $source = null;
    protected ?string $query = null;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function status(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function source(?string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function search(string $search): self
    {
        $this->query = $search;

        return $this;
    }

    public function get(): NesSeriesResponseDTO
    {
        $queryParams = [];

        if ($this->status !== null) {
            $queryParams['status'] = $this->status;
        }

        if ($this->source !== null) {
            $queryParams['source'] = $this->source;
        }

        if ($this->query !== null) {
            $queryParams['query'] = $this->query;
        }

        $response = $this->httpClient->get('/esmm/v1/definitions/series', $queryParams);

        return NesSeriesResponseDTO::fromArray($response);
    }
}
