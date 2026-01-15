<?php

namespace Recharge\DTO\V2021_11;

/**
 * Store Data Transfer Object for API version 2021-11
 *
 * @package Recharge\DTO\V2021_11
 * @see https://developer.rechargepayments.com/2021-11/store#the-store-object
 */
class Store
{
    /**
     * @var int Unique numeric identifier for the Store
     */
    private int $id;

    /**
     * @var string|null Store name
     */
    private ?string $name;

    /**
     * @var string|null Store domain
     */
    private ?string $domain;

    /**
     * @var string|null Store email
     */
    private ?string $email;

    /**
     * @var string|null Created date and time
     */
    private ?string $createdAt;

    /**
     * @var string|null Updated date and time
     */
    private ?string $updatedAt;

    /**
     * @var array Additional store data
     */
    private array $data;

    /**
     * Store constructor
     *
     * @param array $data Store data from API
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? 0;
        $this->name = $data['name'] ?? null;
        $this->domain = $data['domain'] ?? null;
        $this->email = $data['email'] ?? null;
        $this->createdAt = $data['created_at'] ?? null;
        $this->updatedAt = $data['updated_at'] ?? null;
        $this->data = $data;
    }

    /**
     * Get store ID
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get store name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Get store domain
     *
     * @return string|null
     */
    public function getDomain(): ?string
    {
        return $this->domain;
    }

    /**
     * Get store email
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
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
