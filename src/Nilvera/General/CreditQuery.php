<?php

declare(strict_types=1);

namespace Afea\Einvoice\Nilvera\General;

use Afea\Einvoice\Common\HttpClient;
use Afea\Einvoice\Nilvera\General\DTOs\NilveraCreditResponseDTO;

class CreditQuery
{
    protected HttpClient $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function get(): NilveraCreditResponseDTO
    {
        $response = $this->httpClient->get('/general/Credits');

        return NilveraCreditResponseDTO::fromArray($response);
    }
}
