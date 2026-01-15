<?php

namespace Recharge\DTO;

use Recharge\Client;
use Recharge\Exceptions\RechargeException;

/**
 * Factory for creating version-specific DTOs
 *
 * @package Recharge\DTO
 */
class DTOFactory
{
    /**
     * Create a Subscription DTO based on the client's API version
     *
     * @param Client $client The Recharge API client
     * @param array $data Subscription data from API
     * @return object Subscription DTO instance
     * @throws RechargeException If the API version is not supported
     */
    public static function createSubscription(Client $client, array $data): object
    {
        $apiVersion = $client->getApiVersion();
        
        if ($apiVersion === Client::API_VERSION_2021_11) {
            return new V2021_11\Subscription($data);
        } elseif ($apiVersion === Client::API_VERSION_2021_01) {
            return new V2021_01\Subscription($data);
        }
        
        throw new RechargeException("Unsupported API version for Subscription DTO: {$apiVersion}");
    }

    /**
     * Create a Customer DTO based on the client's API version
     *
     * @param Client $client The Recharge API client
     * @param array $data Customer data from API
     * @return object Customer DTO instance
     * @throws RechargeException If the API version is not supported
     */
    public static function createCustomer(Client $client, array $data): object
    {
        $apiVersion = $client->getApiVersion();
        
        if ($apiVersion === Client::API_VERSION_2021_11) {
            return new V2021_11\Customer($data);
        } elseif ($apiVersion === Client::API_VERSION_2021_01) {
            return new V2021_01\Customer($data);
        }
        
        throw new RechargeException("Unsupported API version for Customer DTO: {$apiVersion}");
    }

    /**
     * Create a Charge DTO based on the client's API version
     *
     * @param Client $client The Recharge API client
     * @param array $data Charge data from API
     * @return object Charge DTO instance
     * @throws RechargeException If the API version is not supported
     */
    public static function createCharge(Client $client, array $data): object
    {
        $apiVersion = $client->getApiVersion();
        
        if ($apiVersion === Client::API_VERSION_2021_11) {
            return new V2021_11\Charge($data);
        } elseif ($apiVersion === Client::API_VERSION_2021_01) {
            return new V2021_01\Charge($data);
        }
        
        throw new RechargeException("Unsupported API version for Charge DTO: {$apiVersion}");
    }

    /**
     * Create an Order DTO based on the client's API version
     *
     * @param Client $client The Recharge API client
     * @param array $data Order data from API
     * @return object Order DTO instance
     * @throws RechargeException If the API version is not supported
     */
    public static function createOrder(Client $client, array $data): object
    {
        $apiVersion = $client->getApiVersion();
        
        if ($apiVersion === Client::API_VERSION_2021_11) {
            return new V2021_11\Order($data);
        } elseif ($apiVersion === Client::API_VERSION_2021_01) {
            return new V2021_01\Order($data);
        }
        
        throw new RechargeException("Unsupported API version for Order DTO: {$apiVersion}");
    }

    /**
     * Create an Address DTO based on the client's API version
     *
     * @param Client $client The Recharge API client
     * @param array $data Address data from API
     * @return object Address DTO instance
     * @throws RechargeException If the API version is not supported
     */
    public static function createAddress(Client $client, array $data): object
    {
        $apiVersion = $client->getApiVersion();
        
        if ($apiVersion === Client::API_VERSION_2021_11) {
            return new V2021_11\Address($data);
        } elseif ($apiVersion === Client::API_VERSION_2021_01) {
            return new V2021_01\Address($data);
        }
        
        throw new RechargeException("Unsupported API version for Address DTO: {$apiVersion}");
    }

    /**
     * Create a Product DTO based on the client's API version
     *
     * @param Client $client The Recharge API client
     * @param array $data Product data from API
     * @return object Product DTO instance
     * @throws RechargeException If the API version is not supported
     */
    public static function createProduct(Client $client, array $data): object
    {
        $apiVersion = $client->getApiVersion();
        
        if ($apiVersion === Client::API_VERSION_2021_11) {
            return new V2021_11\Product($data);
        } elseif ($apiVersion === Client::API_VERSION_2021_01) {
            return new V2021_01\Product($data);
        }
        
        throw new RechargeException("Unsupported API version for Product DTO: {$apiVersion}");
    }

    /**
     * Create a Store DTO based on the client's API version
     *
     * @param Client $client The Recharge API client
     * @param array $data Store data from API
     * @return object Store DTO instance
     * @throws RechargeException If the API version is not supported
     */
    public static function createStore(Client $client, array $data): object
    {
        $apiVersion = $client->getApiVersion();
        
        if ($apiVersion === Client::API_VERSION_2021_11) {
            return new V2021_11\Store($data);
        } elseif ($apiVersion === Client::API_VERSION_2021_01) {
            return new V2021_01\Store($data);
        }
        
        throw new RechargeException("Unsupported API version for Store DTO: {$apiVersion}");
    }
}
