<?php

declare(strict_types=1);

namespace Afea\Einvoice\Nilvera\Voucher;

use Afea\Einvoice\Common\HttpClient;
use Afea\Einvoice\Nilvera\Voucher\DTOs\NilveraCancelResponseDTO;
use Afea\Einvoice\Nilvera\Voucher\DTOs\NilveraRevertCancelResponseDTO;

class CancelQuery
{
    protected HttpClient $httpClient;
    protected ?string $uuid = null;

    public function __construct(HttpClient $httpClient, ?string $uuid = null)
    {
        $this->httpClient = $httpClient;
        $this->uuid = $uuid;
    }

    /**
     * Set voucher UUID
     *
     * @param string $uuid
     * @return self
     */
    public function voucher(string $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * Cancel voucher
     *
     * @return NilveraCancelResponseDTO
     */
    public function execute(): NilveraCancelResponseDTO
    {
        if ($this->uuid === null || $this->uuid === '') {
            throw new \InvalidArgumentException('Voucher UUID is required');
        }

        $response = $this->httpClient->put("/evoucher/Vouchers/{$this->uuid}/Cancel", []);

        return NilveraCancelResponseDTO::fromArray($response);
    }

    /**
     * Revert cancellation
     *
     * @return NilveraRevertCancelResponseDTO
     */
    public function revert(): NilveraRevertCancelResponseDTO
    {
        if ($this->uuid === null || $this->uuid === '') {
            throw new \InvalidArgumentException('Voucher UUID is required');
        }

        $response = $this->httpClient->put("/evoucher/Vouchers/{$this->uuid}/RevertCancel", []);

        return NilveraRevertCancelResponseDTO::fromArray($response);
    }
}
