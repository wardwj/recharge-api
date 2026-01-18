<?php

declare(strict_types=1);

namespace Recharge\Resources;

use Recharge\Data\Store as StoreDTO;
use Recharge\Enums\ApiVersion;
use Recharge\RechargeClient;

/**
 * Shop resource for interacting with Recharge shop endpoints
 *
 * Shop endpoints are available in API version 2021-01.
 * In 2021-11, this was unified/renamed as /store endpoint.
 *
 * @see https://developer.rechargepayments.com/2021-01/shop
 */
class Shop extends AbstractResource
{
    /**
     * @var string Resource endpoint base path
     */
    protected string $endpoint = '/shop';

    /**
     * Shop constructor
     *
     * @param RechargeClient $client The Recharge API client instance
     */
    public function __construct(RechargeClient $client)
    {
        parent::__construct($client);
    }

    /**
     * Retrieve shop information
     *
     * Shop endpoint is available in API version 2021-01.
     * This method automatically switches to 2021-01, makes the request, then restores the original version.
     *
     * @return StoreDTO Shop/Store DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-01/shop#get-shop
     */
    public function get(): StoreDTO
    {
        $originalVersion = $this->client->getApiVersion();

        try {
            // Shop endpoint requires 2021-01 API version
            if ($originalVersion !== ApiVersion::V2021_01) {
                $this->client->setApiVersion(ApiVersion::V2021_01);
            }

            $response = $this->client->get($this->endpoint);

            return StoreDTO::fromArray($response['shop'] ?? []);
        } finally {
            // Restore original API version if it was changed
            if ($this->client->getApiVersion() !== $originalVersion) {
                $this->client->setApiVersion($originalVersion);
            }
        }
    }

    /**
     * Get shipping countries for the shop
     *
     * Returns a list of countries that the shop ships to.
     * Shop endpoint is available in API version 2021-01.
     * This method automatically switches to 2021-01, makes the request, then restores the original version.
     *
     * @return array<int, array<string, mixed>> Array of shipping country data
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-01/shop#get-shipping-countries
     */
    public function getShippingCountries(): array
    {
        $originalVersion = $this->client->getApiVersion();

        try {
            // Shop endpoint requires 2021-01 API version
            if ($originalVersion !== ApiVersion::V2021_01) {
                $this->client->setApiVersion(ApiVersion::V2021_01);
            }

            $response = $this->client->get($this->buildEndpoint('shipping_countries'));

            return $response['shipping_countries'] ?? [];
        } finally {
            // Restore original API version if it was changed
            if ($this->client->getApiVersion() !== $originalVersion) {
                $this->client->setApiVersion($originalVersion);
            }
        }
    }
}
