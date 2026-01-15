<?php

namespace Recharge\DTO\V2021_01;

/**
 * Product Data Transfer Object for API version 2021-01
 *
 * @package Recharge\DTO\V2021_01
 * @see https://developer.rechargepayments.com/2021-01/products#the-product-object
 */
class Product
{
    /**
     * @var int Unique numeric identifier for the Product
     */
    private int $id;

    /**
     * @var string|null Product title
     */
    private ?string $title;

    /**
     * @var string|null Product handle
     */
    private ?string $handle;

    /**
     * @var string|null Created date and time
     */
    private ?string $createdAt;

    /**
     * @var string|null Updated date and time
     */
    private ?string $updatedAt;

    /**
     * @var array Additional product data
     */
    private array $data;

    /**
     * Product constructor
     *
     * @param array $data Product data from API
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? 0;
        $this->title = $data['title'] ?? null;
        $this->handle = $data['handle'] ?? null;
        $this->createdAt = $data['created_at'] ?? null;
        $this->updatedAt = $data['updated_at'] ?? null;
        $this->data = $data;
    }

    /**
     * Get product ID
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get product title
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Get product handle
     *
     * @return string|null
     */
    public function getHandle(): ?string
    {
        return $this->handle;
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
