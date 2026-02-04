<?php

declare(strict_types=1);

namespace Afea\Einvoice\NES\Voucher;

use Afea\Einvoice\Common\HttpClient;
use Afea\Einvoice\NES\Voucher\DTOs\NesPreviewResponseDTO;
use Afea\Einvoice\NES\Voucher\DTOs\NesResponseDTO;

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

    public function get(): NesResponseDTO
    {
        $query = [];

        if ($this->pageSize !== null) {
            $query['pageSize'] = $this->pageSize;
        }

        if ($this->page !== null) {
            $query['page'] = $this->page;
        }

        // NES API requires sort parameter
        if (!isset($query['sort'])) {
            $query['sort'] = 'CreatedAt desc';
        }

        $response = $this->httpClient->get('/esmm/v1/vouchers', $query);

        return NesResponseDTO::fromArray($response);
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
        return $this->httpClient->downloadBinary("/esmm/v1/vouchers/{$uuid}/pdf");
    }

    /**
     * Get HTML preview for a voucher by UUID
     *
     * @param string $uuid Voucher UUID
     * @return NesPreviewResponseDTO
     */
    public function preview(string $uuid): NesPreviewResponseDTO
    {
        $response = $this->httpClient->downloadHtml("/esmm/v1/vouchers/{$uuid}/html");

        $dto = new NesPreviewResponseDTO();
        $dto->content = $response['content'];
        
        // Check for x-default-xslt-used header
        if (isset($response['headers']['x-default-xslt-used'])) {
            $dto->isUsingDefaultTemplate = filter_var(
                $response['headers']['x-default-xslt-used'],
                FILTER_VALIDATE_BOOLEAN
            );
        }
        
        // Check for x-xslt-error header
        if (isset($response['headers']['x-xslt-error'])) {
            $dto->errorMessage = $response['headers']['x-xslt-error'];
        }

        return $dto;
    }

    /**
     * Send voucher(s) via email
     *
     * @return MailQuery
     */
    public function mail(): MailQuery
    {
        return new MailQuery($this->httpClient);
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
     * Cancel voucher(s)
     *
     * @return CancelQuery
     */
    public function cancel(): CancelQuery
    {
        return new CancelQuery($this->httpClient);
    }
}
