<?php

declare(strict_types=1);

namespace Afea\Einvoice\Nilvera\Voucher;

use Afea\Einvoice\Common\HttpClient;
use Afea\Einvoice\Nilvera\Voucher\DTOs\NilveraMailHistoryResponseDTO;

class MailHistoryQuery
{
    protected HttpClient $httpClient;
    protected string $uuid;

    public function __construct(HttpClient $httpClient, string $uuid)
    {
        $this->httpClient = $httpClient;
        $this->uuid = $uuid;
    }

    /**
     * Get mail history for the voucher
     *
     * @return NilveraMailHistoryResponseDTO
     */
    public function get(): NilveraMailHistoryResponseDTO
    {
        $response = $this->httpClient->get("/evoucher/Vouchers/{$this->uuid}/Mailhistories");

        return NilveraMailHistoryResponseDTO::fromArray($response);
    }
}
