<?php

declare(strict_types=1);

namespace Afea\Einvoice\Nilvera\Voucher;

use Afea\Einvoice\Common\HttpClient;
use Afea\Einvoice\Nilvera\Voucher\DTOs\NilveraVoucherStatusResponseDTO;

class StatusQuery
{
    protected HttpClient $httpClient;
    protected string $uuid;

    public function __construct(HttpClient $httpClient, string $uuid)
    {
        $this->httpClient = $httpClient;
        $this->uuid = $uuid;
    }

    /**
     * Get status information for the voucher
     *
     * @return NilveraVoucherStatusResponseDTO
     */
    public function get(): NilveraVoucherStatusResponseDTO
    {
        $response = $this->httpClient->get("/evoucher/Vouchers/{$this->uuid}/Status");

        return NilveraVoucherStatusResponseDTO::fromArray($response);
    }
}
