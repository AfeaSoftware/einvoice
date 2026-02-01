<?php

declare(strict_types=1);

namespace Afea\Einvoice\NES;

use Afea\Einvoice\Common\HttpClient;
use Afea\Einvoice\NES\General\GeneralClient;
use Afea\Einvoice\NES\Voucher\VoucherQuery;

class Client
{
    protected HttpClient $httpClient;
    protected HttpClient $generalHttpClient;

    public function __construct(?string $token = null)
    {
        $this->httpClient = new HttpClient('https://apitest.nes.com.tr', $token);
        $this->generalHttpClient = new HttpClient('https://apitest.nes.com.tr', $token);
    }

    public static function make(string $token): self
    {
        return new self($token);
    }

    public static function create(?string $token = null): self
    {
        return new self($token);
    }

    public function setToken(string $token): void
    {
        $this->httpClient->setToken($token);
        $this->generalHttpClient->setToken($token);
    }

    public function getToken(): ?string
    {
        return $this->httpClient->getToken();
    }

    public function voucher(): VoucherQuery
    {
        return new VoucherQuery($this->httpClient);
    }

    public function vouchers(): VoucherQuery
    {
        return $this->voucher();
    }

    public function general(): GeneralClient
    {
        return new GeneralClient($this->generalHttpClient);
    }

    protected function getHttpClient(): HttpClient
    {
        return $this->httpClient;
    }
}
