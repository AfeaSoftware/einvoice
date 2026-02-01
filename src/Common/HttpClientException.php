<?php

declare(strict_types=1);

namespace Afea\Einvoice\Common;

use Exception;

class HttpClientException extends Exception
{
    protected ?array $errorDetails = null;
    protected ?string $rawResponse = null;

    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null, ?array $errorDetails = null, ?string $rawResponse = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errorDetails = $errorDetails;
        $this->rawResponse = $rawResponse;
    }

    public function getErrorDetails(): ?array
    {
        return $this->errorDetails;
    }

    public function getRawResponse(): ?string
    {
        return $this->rawResponse;
    }

    /**
     * Get errors array (supports both 'errors' and 'Errors' keys)
     *
     * @return array<int, array<string, mixed>>
     */
    public function getErrors(): array
    {
        if ($this->errorDetails === null) {
            return [];
        }

        // NES format: errors (lowercase)
        if (isset($this->errorDetails['errors']) && is_array($this->errorDetails['errors'])) {
            return $this->errorDetails['errors'];
        }

        // Nilvera format: Errors (uppercase)
        if (isset($this->errorDetails['Errors']) && is_array($this->errorDetails['Errors'])) {
            return $this->errorDetails['Errors'];
        }

        return [];
    }

    /**
     * Get invalid fields array
     *
     * @return array<int, array<string, mixed>>
     */
    public function getInvalidFields(): array
    {
        if ($this->errorDetails === null) {
            return [];
        }

        if (isset($this->errorDetails['invalidFields']) && is_array($this->errorDetails['invalidFields'])) {
            return $this->errorDetails['invalidFields'];
        }

        return [];
    }

    /**
     * Get main error message from response
     *
     * @return string|null
     */
    public function getResponseMessage(): ?string
    {
        if ($this->errorDetails === null) {
            return null;
        }

        // NES format: message (lowercase)
        if (isset($this->errorDetails['message'])) {
            return (string) $this->errorDetails['message'];
        }

        // Nilvera format: Message (uppercase)
        if (isset($this->errorDetails['Message'])) {
            return (string) $this->errorDetails['Message'];
        }

        return null;
    }

    public function getFormattedMessage(): string
    {
        $message = $this->getMessage();

        if ($this->errorDetails !== null) {
            // Get errors (both formats)
            $errors = $this->getErrors();
            if (!empty($errors)) {
                $errorStrings = [];
                foreach ($errors as $error) {
                    $errorStr = '';
                    
                    // Code (both formats: code/Code)
                    $code = $error['code'] ?? $error['Code'] ?? null;
                    if ($code !== null) {
                        $errorStr .= "[{$code}] ";
                    }
                    
                    // Description (both formats: description/Description)
                    $description = $error['description'] ?? $error['Description'] ?? null;
                    if ($description !== null) {
                        $errorStr .= $description;
                    }
                    
                    // Detail (both formats: detail/Detail)
                    $detail = $error['detail'] ?? $error['Detail'] ?? null;
                    if ($detail !== null && $detail !== '') {
                        $errorStr .= " - {$detail}";
                    }
                    
                    if ($errorStr) {
                        $errorStrings[] = $errorStr;
                    }
                }
                if (!empty($errorStrings)) {
                    $message .= "\n\nError Details:\n" . implode("\n", $errorStrings);
                }
            }

            // Get invalid fields
            $invalidFields = $this->getInvalidFields();
            if (!empty($invalidFields)) {
                $fieldStrings = [];
                foreach ($invalidFields as $field) {
                    $fieldStr = '';
                    if (isset($field['field'])) {
                        $fieldStr .= "Field: {$field['field']}";
                    }
                    if (isset($field['description'])) {
                        $fieldStr .= " - {$field['description']}";
                    }
                    if (isset($field['detail']) && $field['detail'] !== '') {
                        $fieldStr .= " ({$field['detail']})";
                    }
                    if ($fieldStr) {
                        $fieldStrings[] = $fieldStr;
                    }
                }
                if (!empty($fieldStrings)) {
                    $message .= "\n\nInvalid Fields:\n" . implode("\n", $fieldStrings);
                }
            }
        }

        return $message;
    }
}
