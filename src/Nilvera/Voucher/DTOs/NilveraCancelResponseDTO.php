<?php

declare(strict_types=1);

namespace Afea\Einvoice\Nilvera\Voucher\DTOs;

class NilveraCancelResponseDTO
{
    /**
     * @var array<string>
     */
    public array $messages = [];

    /**
     * @param array<string>|array<string, mixed>|mixed $data Array of strings or array with messages key
     */
    public static function fromArray($data): self
    {
        $dto = new self();
        
        if (!is_array($data)) {
            $dto->messages = [];
            return $dto;
        }
        
        // Handle direct array of strings (indexed array)
        if (!empty($data) && array_keys($data) === range(0, count($data) - 1)) {
            // Check if all elements are strings
            $allStrings = true;
            foreach ($data as $item) {
                if (!is_string($item)) {
                    $allStrings = false;
                    break;
                }
            }
            if ($allStrings) {
                $dto->messages = $data;
                return $dto;
            }
        }
        
        // Handle array with messages key
        if (isset($data['messages']) && is_array($data['messages'])) {
            $dto->messages = $data['messages'];
        } else {
            $dto->messages = [];
        }

        return $dto;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'messages' => $this->messages,
        ];
    }

    /**
     * Check if there are any messages
     */
    public function hasMessages(): bool
    {
        return !empty($this->messages);
    }

    /**
     * Get first message
     */
    public function getFirstMessage(): ?string
    {
        return $this->messages[0] ?? null;
    }
}
