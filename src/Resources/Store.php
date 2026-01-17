<?php

declare(strict_types=1);

namespace Recharge\Resources;

use Recharge\Data\Store as StoreDTO;
use Recharge\RechargeClient;

/**
 * Store resource for interacting with Recharge store endpoints
 *
 * @see https://developer.rechargepayments.com/2021-11/store
 */
class Store extends AbstractResource
{
    /**
     * @var string Resource endpoint base path
     */
    protected string $endpoint = '/store';

    /**
     * Store constructor
     *
     * @param RechargeClient $client The Recharge API client instance
     */
    public function __construct(RechargeClient $client)
    {
        parent::__construct($client);
    }

    /**
     * Retrieve store information
     *
     * @return StoreDTO Store DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/store#retrieve-a-store
     */
    public function get(): StoreDTO
    {
        $response = $this->client->get($this->endpoint);

        return StoreDTO::fromArray($response['store'] ?? []);
    }
}
