<?php

namespace Recharge\DTO\V2021_01;

/**
 * Address Data Transfer Object for API version 2021-01
 *
 * @package Recharge\DTO\V2021_01
 * @see https://developer.rechargepayments.com/2021-01/addresses#the-address-object
 */
class Address
{
    /**
     * @var int Unique numeric identifier for the Address
     */
    private int $id;

    /**
     * @var int Unique numeric identifier of the customer
     */
    private int $customerId;

    /**
     * @var string|null First name
     */
    private ?string $firstName;

    /**
     * @var string|null Last name
     */
    private ?string $lastName;

    /**
     * @var string|null Address line 1
     */
    private ?string $address1;

    /**
     * @var string|null Address line 2
     */
    private ?string $address2;

    /**
     * @var string|null City
     */
    private ?string $city;

    /**
     * @var string|null Province/State code
     */
    private ?string $province;

    /**
     * @var string|null Zip/Postal code
     */
    private ?string $zip;

    /**
     * @var string|null Country code
     */
    private ?string $country;

    /**
     * @var string|null Phone number
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
     * @var array Additional address data
     */
    private array $data;

    /**
     * Address constructor
     *
     * @param array $data Address data from API
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? 0;
        $this->customerId = $data['customer_id'] ?? 0;
        $this->firstName = $data['first_name'] ?? null;
        $this->lastName = $data['last_name'] ?? null;
        $this->address1 = $data['address1'] ?? null;
        $this->address2 = $data['address2'] ?? null;
        $this->city = $data['city'] ?? null;
        $this->province = $data['province'] ?? null;
        $this->zip = $data['zip'] ?? null;
        $this->country = $data['country'] ?? null;
        $this->phone = $data['phone'] ?? null;
        $this->createdAt = $data['created_at'] ?? null;
        $this->updatedAt = $data['updated_at'] ?? null;
        $this->data = $data;
    }

    /**
     * Get address ID
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
     * Get address line 1
     *
     * @return string|null
     */
    public function getAddress1(): ?string
    {
        return $this->address1;
    }

    /**
     * Get address line 2
     *
     * @return string|null
     */
    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    /**
     * Get city
     *
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * Get province/state
     *
     * @return string|null
     */
    public function getProvince(): ?string
    {
        return $this->province;
    }

    /**
     * Get zip/postal code
     *
     * @return string|null
     */
    public function getZip(): ?string
    {
        return $this->zip;
    }

    /**
     * Get country code
     *
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
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
