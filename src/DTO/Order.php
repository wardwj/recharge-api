<?php

namespace Recharge\DTO;

/**
 * Order Data Transfer Object
 *
 * @package Recharge\DTO
 * @see https://developer.rechargepayments.com/2021-11/orders#the-order-object
 */
class Order
{
    /**
     * @var int Unique numeric identifier for the Order
     */
    private int $id;

    /**
     * @var int Unique numeric identifier of the customer
     */
    private int $customerId;

    /**
     * @var string|null Order number
     */
    private ?string $orderNumber;

    /**
     * @var string|null Total price
     */
    private ?string $totalPrice;

    /**
     * @var string|null Financial status
     */
    private ?string $financialStatus;

    /**
     * @var string|null Created date and time
     */
    private ?string $createdAt;

    /**
     * @var string|null Updated date and time
     */
    private ?string $updatedAt;

    /**
     * @var array Additional order data
     */
    private array $data;

    /**
     * Order constructor
     *
     * @param array $data Order data from API
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? 0;
        $this->customerId = $data['customer_id'] ?? 0;
        $this->orderNumber = $data['order_number'] ?? null;
        $this->totalPrice = $data['total_price'] ?? null;
        $this->financialStatus = $data['financial_status'] ?? null;
        $this->createdAt = $data['created_at'] ?? null;
        $this->updatedAt = $data['updated_at'] ?? null;
        $this->data = $data;
    }

    /**
     * Get order ID
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
     * Get order number
     *
     * @return string|null
     */
    public function getOrderNumber(): ?string
    {
        return $this->orderNumber;
    }

    /**
     * Get total price
     *
     * @return string|null
     */
    public function getTotalPrice(): ?string
    {
        return $this->totalPrice;
    }

    /**
     * Get financial status
     *
     * @return string|null
     */
    public function getFinancialStatus(): ?string
    {
        return $this->financialStatus;
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
