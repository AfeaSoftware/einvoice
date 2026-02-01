<?php

declare(strict_types=1);

namespace Afea\Einvoice\NES\Voucher;

use Afea\Einvoice\Common\HttpClient;
use Afea\Einvoice\NES\Voucher\DTOs\NesPreviewResponseDTO;
use Afea\Einvoice\NES\Voucher\DTOs\NesSendResponseDTO;

class SendVoucherQuery
{
    protected HttpClient $httpClient;
    protected bool $isDirectSend = true;
    protected string $previewType = 'None';
    protected ?string $documentTemplate = null;
    protected string $sourceApp = 'Einvoice';
    protected ?string $sourceAppRecordId = null;
    protected bool $autoSaveCompany = false;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function isDirectSend(bool $value): self
    {
        $this->isDirectSend = $value;

        return $this;
    }

    public function previewType(string $type): self
    {
        $this->previewType = $type;

        return $this;
    }

    public function documentTemplate(?string $template): self
    {
        $this->documentTemplate = $template;

        return $this;
    }

    public function sourceApp(string $app): self
    {
        $this->sourceApp = $app;

        return $this;
    }

    public function sourceAppRecordId(?string $recordId): self
    {
        $this->sourceAppRecordId = $recordId;

        return $this;
    }

    public function autoSaveCompany(bool $value): self
    {
        $this->autoSaveCompany = $value;

        return $this;
    }

    /**
     * Send voucher from XML file path
     *
     * @param string $xmlFilePath Path to the UBL-TR XML file
     * @return NesSendResponseDTO
     */
    public function fromFile(string $xmlFilePath): NesSendResponseDTO
    {
        if (!file_exists($xmlFilePath)) {
            throw new \InvalidArgumentException("File not found: {$xmlFilePath}");
        }

        $multipart = [
            [
                'name' => 'File',
                'contents' => fopen($xmlFilePath, 'r'),
                'filename' => basename($xmlFilePath),
            ],
            [
                'name' => 'IsDirectSend',
                'contents' => $this->isDirectSend ? 'true' : 'false',
            ],
            [
                'name' => 'PreviewType',
                'contents' => $this->previewType,
            ],
            [
                'name' => 'SourceApp',
                'contents' => $this->sourceApp,
            ],
            [
                'name' => 'AutoSaveCompany',
                'contents' => $this->autoSaveCompany ? 'true' : 'false',
            ],
        ];

        if ($this->documentTemplate !== null) {
            $multipart[] = [
                'name' => 'DocumentTemplate',
                'contents' => $this->documentTemplate,
            ];
        }

        if ($this->sourceAppRecordId !== null) {
            $multipart[] = [
                'name' => 'SourceAppRecordId',
                'contents' => $this->sourceAppRecordId,
            ];
        }

        $response = $this->httpClient->postMultipart('/esmm/v1/uploads/document', $multipart);

        return NesSendResponseDTO::fromArray($response);
    }

    /**
     * Send voucher from XML string content
     *
     * @param string $xmlContent UBL-TR XML content as string
     * @param string $filename Optional filename (default: voucher.xml)
     * @return NesSendResponseDTO
     */
    public function fromXml(string $xmlContent, string $filename = 'voucher.xml'): NesSendResponseDTO
    {
        $multipart = [
            [
                'name' => 'File',
                'contents' => $xmlContent,
                'filename' => $filename,
            ],
            [
                'name' => 'IsDirectSend',
                'contents' => $this->isDirectSend ? 'true' : 'false',
            ],
            [
                'name' => 'PreviewType',
                'contents' => $this->previewType,
            ],
            [
                'name' => 'SourceApp',
                'contents' => $this->sourceApp,
            ],
            [
                'name' => 'AutoSaveCompany',
                'contents' => $this->autoSaveCompany ? 'true' : 'false',
            ],
        ];

        if ($this->documentTemplate !== null) {
            $multipart[] = [
                'name' => 'DocumentTemplate',
                'contents' => $this->documentTemplate,
            ];
        }

        if ($this->sourceAppRecordId !== null) {
            $multipart[] = [
                'name' => 'SourceAppRecordId',
                'contents' => $this->sourceAppRecordId,
            ];
        }

        $response = $this->httpClient->postMultipart('/esmm/v1/uploads/document', $multipart);

        return NesSendResponseDTO::fromArray($response);
    }

    /**
     * Preview voucher from XML file path
     *
     * @param string $xmlFilePath Path to the UBL-TR XML file
     * @param string|null $documentTemplateTitle Optional template title
     * @return NesPreviewResponseDTO
     */
    public function previewFromFile(string $xmlFilePath, ?string $documentTemplateTitle = null): NesPreviewResponseDTO
    {
        if (!file_exists($xmlFilePath)) {
            throw new \InvalidArgumentException("File not found: {$xmlFilePath}");
        }

        $multipart = [
            [
                'name' => 'File',
                'contents' => fopen($xmlFilePath, 'r'),
                'filename' => basename($xmlFilePath),
            ],
        ];

        if ($documentTemplateTitle !== null) {
            $multipart[] = [
                'name' => 'DocumentTemplateTitle',
                'contents' => $documentTemplateTitle,
            ];
        }

        $response = $this->httpClient->postMultipart('/esmm/v1/uploads/document/preview', $multipart);

        return NesPreviewResponseDTO::fromArray($response);
    }

    /**
     * Preview voucher from XML string content
     *
     * @param string $xmlContent UBL-TR XML content as string
     * @param string $filename Optional filename (default: voucher.xml)
     * @param string|null $documentTemplateTitle Optional template title
     * @return NesPreviewResponseDTO
     */
    public function previewFromXml(string $xmlContent, string $filename = 'voucher.xml', ?string $documentTemplateTitle = null): NesPreviewResponseDTO
    {
        $multipart = [
            [
                'name' => 'File',
                'contents' => $xmlContent,
                'filename' => $filename,
            ],
        ];

        if ($documentTemplateTitle !== null) {
            $multipart[] = [
                'name' => 'DocumentTemplateTitle',
                'contents' => $documentTemplateTitle,
            ];
        }

        $response = $this->httpClient->postMultipart('/esmm/v1/uploads/document/preview', $multipart);

        return NesPreviewResponseDTO::fromArray($response);
    }
}
