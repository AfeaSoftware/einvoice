<?php

declare(strict_types=1);

namespace Afea\Einvoice\Nilvera\Voucher;

use Afea\Einvoice\Common\HttpClient;
use Afea\Einvoice\Nilvera\Voucher\DTOs\NilveraPreviewResponseDTO;
use Afea\Einvoice\Nilvera\Voucher\DTOs\NilveraResponseDTO;

class VoucherQuery
{
    protected HttpClient $httpClient;
    protected ?int $pageSize = null;
    protected ?int $page = null;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function pageSize(int $size): self
    {
        $this->pageSize = $size;

        return $this;
    }

    public function page(int $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function get(): NilveraResponseDTO
    {
        $query = [];

        if ($this->pageSize !== null) {
            $query['PageSize'] = $this->pageSize;
        }

        if ($this->page !== null) {
            $query['Page'] = $this->page;
        }

        $response = $this->httpClient->get('/evoucher/Vouchers', $query);

        return NilveraResponseDTO::fromArray($response);
    }

    public function send(): SendVoucherQuery
    {
        return new SendVoucherQuery($this->httpClient);
    }

    /**
     * Download PDF for a voucher by UUID
     *
     * @param string $uuid Voucher UUID
     * @return string PDF binary content
     */
    public function downloadPdf(string $uuid): string
    {
        return $this->httpClient->downloadBinary("/evoucher/Vouchers/{$uuid}/pdf");
    }

    /**
     * Get HTML preview for a voucher by UUID
     *
     * @param string $uuid Voucher UUID
     * @return NilveraPreviewResponseDTO
     */
    public function preview(string $uuid): NilveraPreviewResponseDTO
    {
        $response = $this->httpClient->downloadHtml("/evoucher/Vouchers/{$uuid}/html");

        return NilveraPreviewResponseDTO::fromString($response['content']);
    }

    /**
     * Send voucher via email
     *
     * @param string $uuid Voucher UUID
     * @return MailQuery
     */
    public function mail(string $uuid): MailQuery
    {
        return new MailQuery($this->httpClient, $uuid);
    }

    /**
     * Get mail history for a voucher
     *
     * @param string $uuid Voucher UUID
     * @return MailHistoryQuery
     */
    public function mailHistory(string $uuid): MailHistoryQuery
    {
        return new MailHistoryQuery($this->httpClient, $uuid);
    }

    /**
     * Get status information for a voucher
     *
     * @param string $uuid Voucher UUID
     * @return StatusQuery
     */
    public function status(string $uuid): StatusQuery
    {
        return new StatusQuery($this->httpClient, $uuid);
    }

    /**
     * Get series list
     *
     * @return SeriesQuery
     */
    public function series(): SeriesQuery
    {
        return new SeriesQuery($this->httpClient);
    }

    /**
     * Cancel voucher
     *
     * @param string|null $uuid Optional voucher UUID
     * @return CancelQuery
     */
    public function cancel(?string $uuid = null): CancelQuery
    {
        return new CancelQuery($this->httpClient, $uuid);
    }
}
