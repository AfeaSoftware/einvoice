<?php

declare(strict_types=1);

namespace Afea\Einvoice\NES\Voucher;

use Afea\Einvoice\Common\HttpClient;

class MailQuery
{
    protected HttpClient $httpClient;
    protected array $uuids = [];
    protected array $emailAddresses = [];

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Add voucher UUID(s) to send
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
     * Add email address(es) to send the voucher(s)
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
     * Send voucher(s) via email
     *
     * @return void
     */
    public function send(): void
    {
        if (empty($this->uuids)) {
            throw new \InvalidArgumentException('At least one voucher UUID is required');
        }

        if (empty($this->emailAddresses)) {
            throw new \InvalidArgumentException('At least one email address is required');
        }

        $data = [
            'uuids' => array_unique($this->uuids),
            'emailAdresses' => array_unique($this->emailAddresses),
        ];

        $this->httpClient->post('/esmm/v1/vouchers/email/send', $data);
    }
}
