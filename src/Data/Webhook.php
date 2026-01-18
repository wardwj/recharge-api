<?php

declare(strict_types=1);

namespace Recharge\Data;

use Carbon\CarbonImmutable;
use Recharge\Contracts\DataTransferObjectInterface;

/**
 * Webhook Data Transfer Object
 *
 * Immutable DTO representing a Recharge webhook.
 * Handles both API versions 2021-01 and 2021-11.
 *
 * @see https://developer.rechargepayments.com/2021-11/webhooks#the-webhook-object
 * @see https://developer.rechargepayments.com/2021-01/webhooks#the-webhook-object
 */
final readonly class Webhook implements DataTransferObjectInterface
{
    /**
     * @param int $id Unique numeric identifier for the Webhook
     * @param string $address Webhook URL address
     * @param array<string> $topics List of webhook topics/events this webhook subscribes to
     * @param CarbonImmutable|null $createdAt Created timestamp
     * @param CarbonImmutable|null $updatedAt Updated timestamp
     * @param array<string, mixed> $rawData Raw API response data
     */
    public function __construct(
        public int $id,
        public string $address,
        public array $topics = [],
        public ?CarbonImmutable $createdAt = null,
        public ?CarbonImmutable $updatedAt = null,
        public array $rawData = []
    ) {
    }

    /**
     * Create from API response array
     *
     * Works with both API versions 2021-01 and 2021-11.
     *
     * @param array<string, mixed> $data Webhook data from API
     */
    public static function fromArray(array $data): static
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            address: (string) ($data['address'] ?? ''),
            topics: $data['topics'] ?? [],
            createdAt: isset($data['created_at'])
                ? CarbonImmutable::parse($data['created_at'])
                : null,
            updatedAt: isset($data['updated_at'])
                ? CarbonImmutable::parse($data['updated_at'])
                : null,
            rawData: $data
        );
    }

    /**
     * Convert to array for serialization
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'address' => $this->address,
            'topics' => $this->topics,
            'created_at' => $this->createdAt?->toIso8601String(),
            'updated_at' => $this->updatedAt?->toIso8601String(),
        ];
    }

    /**
     * Get raw data from API response
     *
     * @return array<string, mixed>
     */
    public function getRawData(): array
    {
        return $this->rawData;
    }
}
