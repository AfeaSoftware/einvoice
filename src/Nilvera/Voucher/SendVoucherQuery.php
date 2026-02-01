<?php

declare(strict_types=1);

namespace Afea\Einvoice\Nilvera\Voucher;

use Afea\Einvoice\Common\HttpClient;
use Afea\Einvoice\Nilvera\Voucher\DTOs\NilveraSendResponseDTO;

class SendVoucherQuery
{
    protected HttpClient $httpClient;
    protected ?string $templateUuid = null;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function templateUuid(?string $uuid): self
    {
        $this->templateUuid = $uuid;

        return $this;
    }

    /**
     * Send voucher from XML file path
     *
     * @param string $xmlFilePath Path to the UBL-TR XML file
     * @return NilveraSendResponseDTO
     */
    public function fromFile(string $xmlFilePath): NilveraSendResponseDTO
    {
        if (!file_exists($xmlFilePath)) {
            throw new \InvalidArgumentException("File not found: {$xmlFilePath}");
        }

        $query = [];
        if ($this->templateUuid !== null) {
            $query['TemplateUUID'] = $this->templateUuid;
        }

        $multipart = [
            [
                'name' => 'file',
                'contents' => fopen($xmlFilePath, 'r'),
                'filename' => basename($xmlFilePath),
            ],
        ];

        $response = $this->httpClient->postMultipart('/evoucher/Send/Xml', $multipart, [], $query);

        return NilveraSendResponseDTO::fromArray($response);
    }

    /**
     * Send voucher from XML string content
     *
     * @param string $xmlContent UBL-TR XML content as string
     * @param string $filename Optional filename (default: voucher.xml)
     * @return NilveraSendResponseDTO
     */
    public function fromXml(string $xmlContent, string $filename = 'voucher.xml'): NilveraSendResponseDTO
    {
        $query = [];
        if ($this->templateUuid !== null) {
            $query['TemplateUUID'] = $this->templateUuid;
        }

        $multipart = [
            [
                'name' => 'file',
                'contents' => $xmlContent,
                'filename' => $filename,
            ],
        ];

        $response = $this->httpClient->postMultipart('/evoucher/Send/Xml', $multipart, [], $query);

        return NilveraSendResponseDTO::fromArray($response);
    }

    /**
     * Send voucher from Base64 encoded ZIP file
     *
     * @param string $zipBase64 Base64 encoded ZIP file containing UBL-TR XML
     * @return NilveraSendResponseDTO
     */
    public function fromBase64(string $zipBase64): NilveraSendResponseDTO
    {
        $data = [
            'ZIPFileBase64' => $zipBase64,
        ];

        if ($this->templateUuid !== null) {
            $data['TemplateUUID'] = $this->templateUuid;
        }

        $response = $this->httpClient->post('/evoucher/Send/Base64String', $data);

        return NilveraSendResponseDTO::fromArray($response);
    }

    /**
     * Preview voucher from XML file path
     *
     * @param string $xmlFilePath Path to the UBL-TR XML file
     * @return string Preview content (HTML/text)
     */
    public function previewFromFile(string $xmlFilePath): string
    {
        if (!file_exists($xmlFilePath)) {
            throw new \InvalidArgumentException("File not found: {$xmlFilePath}");
        }

        $query = [];
        if ($this->templateUuid !== null) {
            $query['TemplateUUID'] = $this->templateUuid;
        }

        $multipart = [
            [
                'name' => 'file',
                'contents' => fopen($xmlFilePath, 'r'),
                'filename' => basename($xmlFilePath),
            ],
        ];

        $response = $this->httpClient->postMultipart('/evoucher/Send/Xml/Preview', $multipart, [], $query);

        return is_string($response) ? $response : (string) json_encode($response);
    }

    /**
     * Preview voucher from XML string content
     *
     * @param string $xmlContent UBL-TR XML content as string
     * @param string $filename Optional filename (default: voucher.xml)
     * @return string Preview content (HTML/text)
     */
    public function previewFromXml(string $xmlContent, string $filename = 'voucher.xml'): string
    {
        $query = [];
        if ($this->templateUuid !== null) {
            $query['TemplateUUID'] = $this->templateUuid;
        }

        $multipart = [
            [
                'name' => 'file',
                'contents' => $xmlContent,
                'filename' => $filename,
            ],
        ];

        $response = $this->httpClient->postMultipart('/evoucher/Send/Xml/Preview', $multipart, [], $query);

        return is_string($response) ? $response : (string) json_encode($response);
    }

    /**
     * Preview voucher from Base64 encoded ZIP file
     *
     * @param string $zipBase64 Base64 encoded ZIP file containing UBL-TR XML
     * @return string Preview content (HTML/text)
     */
    public function previewFromBase64(string $zipBase64): string
    {
        $data = [
            'ZIPFileBase64' => $zipBase64,
        ];

        if ($this->templateUuid !== null) {
            $data['TemplateUUID'] = $this->templateUuid;
        }

        $response = $this->httpClient->post('/evoucher/Send/Base64String/Preview', $data);

        return is_string($response) ? $response : (string) json_encode($response);
    }
}
