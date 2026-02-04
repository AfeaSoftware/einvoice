<?php

declare(strict_types=1);

namespace Afea\Einvoice\Nilvera\Voucher\DTOs;

class NilveraVoucherStatusResponseDTO
{
    public ?string $statusDetail;
    public string $statusCode; // unknown, waiting, succeed, error
    public string $reportStatus; // NotReported, Reported
    public bool $cancelStatus;

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->statusDetail = $data['StatusDetail'] ?? null;
        $dto->statusCode = $data['StatusCode'] ?? 'unknown';
        $dto->reportStatus = $data['ReportStatus'] ?? 'NotReported';
        $dto->cancelStatus = (bool) ($data['CancelStatus'] ?? false);

        return $dto;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'StatusDetail' => $this->statusDetail,
            'StatusCode' => $this->statusCode,
            'ReportStatus' => $this->reportStatus,
            'CancelStatus' => $this->cancelStatus,
        ];
    }
}
