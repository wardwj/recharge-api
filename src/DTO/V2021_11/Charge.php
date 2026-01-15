<?php

namespace Recharge\DTO\V2021_11;

/**
 * Charge Data Transfer Object for API version 2021-11
 *
 * @package Recharge\DTO\V2021_11
 * @see https://developer.rechargepayments.com/2021-11/charges#the-charge-object
 */
class Charge
{
    /**
     * @var int Unique numeric identifier for the Charge
     */
    private int $id;

    /**
     * @var int Unique numeric identifier of the customer
     */
    private int $customerId;

    /**
     * @var int|null Unique numeric identifier of the subscription
     */
    private ?int $subscriptionId;

    /**
     * @var string|null Amount to be charged
     */
    private ?string $amount;

    /**
     * @var string|null Status of the charge
     */
    private ?string $status;

    /**
     * @var string|null Scheduled charge date
     */
    private ?string $scheduledAt;

    /**
     * @var string|null Created date and time
     */
    private ?string $createdAt;

    /**
     * @var string|null Updated date and time
     */
    private ?string $updatedAt;

    /**
     * @var array Additional charge data
     */
    private array $data;

    /**
     * Charge constructor
     *
     * @param array $data Charge data from API
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? 0;
        $this->customerId = $data['customer_id'] ?? 0;
        $this->subscriptionId = $data['subscription_id'] ?? null;
        $this->amount = $data['amount'] ?? null;
        $this->status = $data['status'] ?? null;
        $this->scheduledAt = $data['scheduled_at'] ?? null;
        $this->createdAt = $data['created_at'] ?? null;
        $this->updatedAt = $data['updated_at'] ?? null;
        $this->data = $data;
    }

    /**
     * Get charge ID
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get customer ID
     *
     * @return int
     */
    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    /**
     * Get subscription ID
     *
     * @return int|null
     */
    public function getSubscriptionId(): ?int
    {
        return $this->subscriptionId;
    }

    /**
     * Get amount
     *
     * @return string|null
     */
    public function getAmount(): ?string
    {
        return $this->amount;
    }

    /**
     * Get status
     *
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Get scheduled at timestamp
     *
     * @return string|null
     */
    public function getScheduledAt(): ?string
    {
        return $this->scheduledAt;
    }

    /**
     * Get created at timestamp
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    /**
     * Get updated at timestamp
     *
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    /**
     * Get raw data array
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
