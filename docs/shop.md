# Shop/Store

Get shop/store information from Recharge.

## Shop (2021-01)

Shop endpoints provide basic store information and shipping countries.

**Note:** Shop endpoints are available in API version 2021-01. In 2021-11, this was unified/renamed as the `/store` endpoint.

### Get Shop Information

```php
// Get shop information (automatically switches to 2021-01)
$shop = $client->shop()->get();

echo "Shop Name: {$shop->name}\n";
echo "Currency: {$shop->currency}\n";
echo "Timezone: {$shop->getTimezone()}\n";
```

### Get Shipping Countries

```php
$shippingCountries = $client->shop()->getShippingCountries();

foreach ($shippingCountries as $country) {
    echo "Country: {$country['name']} ({$country['code']})\n";
}
```

**Version Differences:**
- **2021-01**: Uses `/shop` endpoint for shop information
- **2021-11**: Uses `/store` endpoint for store information (replaces shop)
- The SDK automatically handles version switching when using `shop()` method

**Note:** For 2021-11, use `$client->store()->get()` instead of `$client->shop()->get()`.

## Store (2021-11)

Store endpoints provide store information in API version 2021-11.

```php
// Get store information (2021-11)
$store = $client->store()->get();

echo "Store Name: {$store->name}\n";
echo "Currency: {$store->currency}\n";
```
