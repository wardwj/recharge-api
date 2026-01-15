<?php

namespace Recharge\DTO\V2021_11;

/**
 * Subscription Data Transfer Object for API version 2021-11
 *
 * @package Recharge\DTO\V2021_11
 * @see https://developer.rechargepayments.com/2021-11/subscriptions#the-subscription-object
 */
class Subscription
{
    /**
     * @var int Unique numeric identifier for the Subscription
     */
    private int $id;

    /**
     * @var int Unique numeric identifier of the customer
     */
    private int $customerId;

    /**
     * @var string|null Price of the subscription
     */
    private ?string $price;

    /**
     * @var int|null Quantity of items in the subscription
     */
    private ?int $quantity;

    /**
     * @var string|null Status of the subscription
     */
    private ?string $status;

    /**
     * @var string|null Next charge date
     */
    private ?string $nextChargeScheduledAt;

    /**
     * @var string|null Created date and time
     */
    private ?string $createdAt;

    /**
     * @var string|null Updated date and time
     */
    private ?string $updatedAt;

    /**
     * @var array Additional subscription data
     */
    private array $data;

    /**
     * Subscription constructor
     *
     * @param array $data Subscription data from API
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? 0;
        $this->customerId = $data['customer_id'] ?? 0;
        $this->price = $data['price'] ?? null;
        $this->quantity = $data['quantity'] ?? null;
        $this->status = $data['status'] ?? null;
        $this->nextChargeScheduledAt = $data['next_charge_scheduled_at'] ?? null;
        $this->createdAt = $data['created_at'] ?? null;
        $this->updatedAt = $data['updated_at'] ?? null;
        $this->data = $data;
    }

    /**
     * Get subscription ID
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
     * Get price
     *
     * @return string|null
     */
    public function getPrice(): ?string
    {
        return $this->price;
    }

    /**
     * Get quantity
     *
     * @return int|null
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
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
     * Get next charge scheduled at
     *
     * @return string|null
     */
    public function getNextChargeScheduledAt(): ?string
    {
        return $this->nextChargeScheduledAt;
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
