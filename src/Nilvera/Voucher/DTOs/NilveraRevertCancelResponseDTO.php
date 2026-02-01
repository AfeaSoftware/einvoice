<?php

declare(strict_types=1);

namespace Afea\Einvoice\Nilvera\Voucher\DTOs;

class NilveraRevertCancelResponseDTO
{
    public bool $success = false;

    /**
     * @param bool|array<string, mixed> $data Boolean value or array with success key
     */
    public static function fromArray($data): self
    {
        $dto = new self();
        
        // Handle direct boolean
        if (is_bool($data)) {
            $dto->success = $data;
        } elseif (is_array($data)) {
            // Try different possible keys
            if (isset($data['success'])) {
                $dto->success = (bool) $data['success'];
            } elseif (isset($data['Success'])) {
                $dto->success = (bool) $data['Success'];
            } elseif (isset($data['result'])) {
                $dto->success = (bool) $data['result'];
            } elseif (isset($data['Result'])) {
                $dto->success = (bool) $data['Result'];
            } else {
                // Default to false if not found
                $dto->success = false;
            }
        } else {
            $dto->success = (bool) $data;
        }

        return $dto;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
        ];
    }

    /**
     * Check if operation was successful
     */
    public function isSuccessful(): bool
    {
        return $this->success;
    }
}
