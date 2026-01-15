<?php

namespace Recharge\Resources;

use Recharge\Client;
use Recharge\DTO\DTOFactory;

/**
 * Store resource for interacting with Recharge store endpoints
 *
 * @package Recharge\Resources
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
     * @param Client $client The Recharge API client instance
     */
    public function __construct(Client $client)
    {
        parent::__construct($client);
    }

    /**
     * Retrieve store information
     *
     * @return Store Store DTO
     * @throws \Recharge\Exceptions\RechargeException
     * @see https://developer.rechargepayments.com/2021-11/store#retrieve-a-store
     */
    public function get(): object
    {
        $response = $this->client->get($this->endpoint);
        return DTOFactory::createStore($this->client, $response['store'] ?? []);
    }
}
