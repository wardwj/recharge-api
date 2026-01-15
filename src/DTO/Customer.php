<?php

namespace Recharge\DTO;

/**
 * Customer Data Transfer Object
 *
 * @package Recharge\DTO
 * @see https://developer.rechargepayments.com/2021-11/customers#the-customer-object
 */
class Customer
{
    /**
     * @var int Unique numeric identifier for the Customer
     */
    private int $id;

    /**
     * @var string|null Email address of the customer
     */
    private ?string $email;

    /**
     * @var string|null First name of the customer
     */
    private ?string $firstName;

    /**
     * @var string|null Last name of the customer
     */
    private ?string $lastName;

    /**
     * @var string|null Phone number of the customer
     */
    private ?string $phone;

    /**
     * @var string|null Created date and time
     */
    private ?string $createdAt;

    /**
     * @var string|null Updated date and time
     */
    private ?string $updatedAt;

    /**
     * @var array Additional customer data
     */
    private array $data;

    /**
     * Customer constructor
     *
     * @param array $data Customer data from API
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? 0;
        $this->email = $data['email'] ?? null;
        $this->firstName = $data['first_name'] ?? null;
        $this->lastName = $data['last_name'] ?? null;
        $this->phone = $data['phone'] ?? null;
        $this->createdAt = $data['created_at'] ?? null;
        $this->updatedAt = $data['updated_at'] ?? null;
        $this->data = $data;
    }

    /**
     * Get customer ID
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get email address
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Get first name
     *
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * Get last name
     *
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * Get full name
     *
     * @return string
     */
    public function getFullName(): string
    {
        return trim(($this->firstName ?? '') . ' ' . ($this->lastName ?? ''));
    }

    /**
     * Get phone number
     *
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
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
