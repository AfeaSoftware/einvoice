<?php

declare(strict_types=1);

namespace Afea\Einvoice\NES\Voucher;

use Afea\Einvoice\Common\HttpClient;
use Afea\Einvoice\NES\Voucher\DTOs\NesVoucherStatusResponseDTO;

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
     * NES doesn't have a dedicated status endpoint, so we query the vouchers
     * pagination endpoint with the UUID and a wide date range to find the record
     *
     * @return NesVoucherStatusResponseDTO
     */
    public function get(): NesVoucherStatusResponseDTO
    {
        $startDate = date('Y-m-d', strtotime('-10 years'));
        $endDate = date('Y-m-d', strtotime('+10 years'));

        $query = [
            'uuid' => $this->uuid,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'pageSize' => 1,
            'page' => 1,
            'sort' => 'CreatedAt desc',
        ];

        $response = $this->httpClient->get('/esmm/v1/vouchers', $query);

        // Extract the first record from the data array
        if (empty($response['data']) || !is_array($response['data'])) {
            throw new \RuntimeException("Voucher with UUID {$this->uuid} not found");
        }

        $voucherData = $response['data'][0];

        return NesVoucherStatusResponseDTO::fromArray($voucherData);
    }
}
