<?php

declare(strict_types=1);

namespace Afea\Einvoice\NES\General;

use Afea\Einvoice\Common\HttpClient;
use Afea\Einvoice\NES\General\DTOs\NesCreditResponseDTO;

class CreditQuery
{
    protected HttpClient $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function get(): NesCreditResponseDTO
    {
        $response = $this->httpClient->get('/general/v1/management/creditsummary');

        return NesCreditResponseDTO::fromArray($response);
    }
}
