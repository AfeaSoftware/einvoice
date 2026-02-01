<?php

declare(strict_types=1);

namespace Afea\Einvoice\NES\Voucher;

use Afea\Einvoice\Common\HttpClient;
use Afea\Einvoice\NES\Voucher\DTOs\NesCancelResponseDTO;
use Afea\Einvoice\NES\Voucher\DTOs\NesCanceledListResponseDTO;
use Afea\Einvoice\NES\Voucher\DTOs\NesRevertCancelResponseDTO;

class CancelQuery
{
    protected HttpClient $httpClient;
    protected array $uuids = [];
    
    // Filtering parameters for list()
    protected ?int $pageSize = null;
    protected ?int $page = null;
    protected ?string $company = null;
    protected ?string $documentNote = null;
    protected ?string $documentNumber = null;
    protected ?string $startCreateDate = null;
    protected ?string $endCreateDate = null;
    protected ?string $startDate = null;
    protected ?string $endDate = null;
    protected ?string $sort = null;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Add voucher UUID(s) to cancel
     *
     * @param string|array<string> $uuids Single UUID or array of UUIDs
     * @return self
     */
    public function vouchers($uuids): self
    {
        if (is_string($uuids)) {
            $this->uuids[] = $uuids;
        } elseif (is_array($uuids)) {
            $this->uuids = array_merge($this->uuids, $uuids);
        }

        return $this;
    }

    /**
     * Set page size for listing
     *
     * @param int $pageSize
     * @return self
     */
    public function pageSize(int $pageSize): self
    {
        $this->pageSize = $pageSize;
        return $this;
    }

    /**
     * Set page number for listing
     *
     * @param int $page
     * @return self
     */
    public function page(int $page): self
    {
        $this->page = $page;
        return $this;
    }

    /**
     * Filter by company name or VKN/TNCK
     *
     * @param string $company
     * @return self
     */
    public function company(string $company): self
    {
        $this->company = $company;
        return $this;
    }

    /**
     * Filter by document note
     *
     * @param string $documentNote
     * @return self
     */
    public function documentNote(string $documentNote): self
    {
        $this->documentNote = $documentNote;
        return $this;
    }

    /**
     * Filter by document number
     *
     * @param string $documentNumber
     * @return self
     */
    public function documentNumber(string $documentNumber): self
    {
        $this->documentNumber = $documentNumber;
        return $this;
    }

    /**
     * Filter by start create date
     *
     * @param string $startCreateDate ISO 8601 date-time string
     * @return self
     */
    public function startCreateDate(string $startCreateDate): self
    {
        $this->startCreateDate = $startCreateDate;
        return $this;
    }

    /**
     * Filter by end create date
     *
     * @param string $endCreateDate ISO 8601 date-time string
     * @return self
     */
    public function endCreateDate(string $endCreateDate): self
    {
        $this->endCreateDate = $endCreateDate;
        return $this;
    }

    /**
     * Filter by start issue date
     *
     * @param string $startDate ISO 8601 date-time string
     * @return self
     */
    public function startIssueDate(string $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * Filter by end issue date
     *
     * @param string $endDate ISO 8601 date-time string
     * @return self
     */
    public function endIssueDate(string $endDate): self
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * Set sort order
     *
     * @param string $sort Sort string (e.g., "CreatedAt desc")
     * @return self
     */
    public function sort(string $sort): self
    {
        $this->sort = $sort;
        return $this;
    }

    /**
     * Cancel voucher(s)
     *
     * @return NesCancelResponseDTO
     */
    public function execute(): NesCancelResponseDTO
    {
        $uniqueUuids = array_unique($this->uuids);
        
        if (empty($uniqueUuids)) {
            throw new \InvalidArgumentException('At least one voucher UUID is required');
        }

        if (count($uniqueUuids) > 50) {
            throw new \InvalidArgumentException('Maximum 50 UUIDs allowed per request');
        }

        $data = [
            'uuids' => $uniqueUuids,
        ];

        $response = $this->httpClient->post('/esmm/v1/vouchers/cancel', $data);

        return NesCancelResponseDTO::fromArray($response);
    }

    /**
     * Revert cancellation for voucher(s)
     *
     * @return NesRevertCancelResponseDTO
     */
    public function revert(): NesRevertCancelResponseDTO
    {
        $uniqueUuids = array_unique($this->uuids);
        
        if (empty($uniqueUuids)) {
            throw new \InvalidArgumentException('At least one voucher UUID is required');
        }

        if (count($uniqueUuids) > 50) {
            throw new \InvalidArgumentException('Maximum 50 UUIDs allowed per request');
        }

        $data = [
            'uuids' => $uniqueUuids,
        ];

        $response = $this->httpClient->post('/esmm/v1/vouchers/canceled/withdraw', $data);

        return NesRevertCancelResponseDTO::fromArray($response);
    }

    /**
     * List canceled vouchers
     *
     * @return NesCanceledListResponseDTO
     */
    public function list(): NesCanceledListResponseDTO
    {
        if ($this->pageSize === null) {
            throw new \InvalidArgumentException('pageSize is required');
        }

        if ($this->page === null) {
            throw new \InvalidArgumentException('page is required');
        }

        if ($this->sort === null) {
            throw new \InvalidArgumentException('sort is required');
        }

        $query = [
            'pageSize' => $this->pageSize,
            'page' => $this->page,
            'sort' => $this->sort,
        ];

        if ($this->company !== null) {
            $query['company'] = $this->company;
        }

        if ($this->documentNote !== null) {
            $query['documentNote'] = $this->documentNote;
        }

        if ($this->documentNumber !== null) {
            $query['documentNumber'] = $this->documentNumber;
        }

        if ($this->startCreateDate !== null) {
            $query['startCreateDate'] = $this->startCreateDate;
        }

        if ($this->endCreateDate !== null) {
            $query['endCreateDate'] = $this->endCreateDate;
        }

        if ($this->startDate !== null) {
            $query['startDate'] = $this->startDate;
        }

        if ($this->endDate !== null) {
            $query['endDate'] = $this->endDate;
        }

        $response = $this->httpClient->get('/esmm/v1/vouchers/canceled', $query);

        return NesCanceledListResponseDTO::fromArray($response);
    }

    /**
     * Get last canceled voucher
     *
     * @return array<string, mixed>
     */
    public function last(): array
    {
        $response = $this->httpClient->get('/esmm/v1/vouchers/canceled/last');

        return is_array($response) ? $response : [];
    }
}
