<?php

declare(strict_types=1);

namespace Afea\Einvoice\Nilvera\Voucher;

use Afea\Einvoice\Common\HttpClient;

class MailQuery
{
    protected HttpClient $httpClient;
    protected string $uuid;
    protected array $emailAddresses = [];

    public function __construct(HttpClient $httpClient, string $uuid)
    {
        $this->httpClient = $httpClient;
        $this->uuid = $uuid;
    }

    /**
     * Add email address(es) to send the voucher
     *
     * @param string|array<string> $emails Single email or array of emails
     * @return self
     */
    public function to($emails): self
    {
        if (is_string($emails)) {
            $this->emailAddresses[] = $emails;
        } elseif (is_array($emails)) {
            $this->emailAddresses = array_merge($this->emailAddresses, $emails);
        }

        return $this;
    }

    /**
     * Send voucher via email
     *
     * @return string Response message
     */
    public function send(): string
    {
        if (empty($this->emailAddresses)) {
            throw new \InvalidArgumentException('At least one email address is required');
        }

        $data = [
            'UUID' => $this->uuid,
            'emailAddresses' => array_unique($this->emailAddresses),
        ];

        $response = $this->httpClient->post('/evoucher/Vouchers/Email/Send', $data);

        return is_string($response) ? $response : (string) json_encode($response);
    }
}
