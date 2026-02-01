<?php

declare(strict_types=1);

namespace Afea\Einvoice\NES\General;

use Afea\Einvoice\Common\HttpClient;

class GeneralClient
{
    protected HttpClient $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function credit(): CreditQuery
    {
        return new CreditQuery($this->httpClient);
    }
}
