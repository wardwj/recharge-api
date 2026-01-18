# Webhooks

Manage webhook resources and validate incoming webhook requests in Recharge.

## List Webhooks

```php
// List webhooks
foreach ($client->webhooks()->list() as $webhook) {
    echo "Webhook ID: {$webhook->id}, Address: {$webhook->address}\n";
    echo "Topics: " . implode(', ', $webhook->topics) . "\n";
}

// With sorting
use Recharge\Enums\Sort\WebhookSort;

foreach ($client->webhooks()->list(['sort_by' => WebhookSort::CREATED_AT_DESC]) as $webhook) {
    // Webhooks sorted by creation date (newest first)
}
```

## Get Single Webhook

```php
$webhook = $client->webhooks()->get(123);
```

## Create Webhook

```php
// Create a webhook (using WebhookTopic enum - recommended)
use Recharge\Enums\WebhookTopic;

$webhook = $client->webhooks()->create([
    'address' => 'https://your-app.com/webhooks/recharge',
    'topics' => [
        WebhookTopic::CHARGE_CREATED->value,
        WebhookTopic::CHARGE_PROCESSED->value,
        WebhookTopic::SUBSCRIPTION_CREATED->value,
        WebhookTopic::SUBSCRIPTION_UPDATED->value,
    ],
]);

// String values also work (for backward compatibility)
$webhook = $client->webhooks()->create([
    'address' => 'https://your-app.com/webhooks/recharge',
    'topics' => [
        'charge/created',
        'charge/processed',
        'subscription/created',
        'subscription/updated',
    ],
]);
```

## Update Webhook

```php
$webhook = $client->webhooks()->update(123, [
    'address' => 'https://your-app.com/webhooks/recharge-updated',
    'topics' => [
        WebhookTopic::CHARGE_CREATED->value,
        WebhookTopic::SUBSCRIPTION_CREATED->value,
    ],
]);
```

## Delete Webhook

```php
$client->webhooks()->delete(123);
```

## Test Webhook

```php
$result = $client->webhooks()->test(123);
```

## Available Webhook Topics

Use the `WebhookTopic` enum for type-safe topic values:

```php
use Recharge\Enums\WebhookTopic;

// Charge events
WebhookTopic::CHARGE_CREATED
WebhookTopic::CHARGE_PROCESSED
WebhookTopic::CHARGE_FAILED
WebhookTopic::CHARGE_PAID
WebhookTopic::CHARGE_REFUNDED

// Subscription events
WebhookTopic::SUBSCRIPTION_CREATED
WebhookTopic::SUBSCRIPTION_UPDATED
WebhookTopic::SUBSCRIPTION_CANCELLED
WebhookTopic::SUBSCRIPTION_SKIPPED
WebhookTopic::SUBSCRIPTION_UNSKIPPED

// Customer, Order, Address, Bundle, Discount events
// See WebhookTopic enum for complete list
```

See all available topics: [Recharge Webhooks documentation](https://developer.rechargepayments.com/2021-11/webhooks#available-webhooks)

## Validating Incoming Webhooks

Use `WebhookValidator` to verify incoming webhook requests are authentic:

```php
use Recharge\Support\WebhookValidator;

// Get your API Client Secret from Recharge merchant portal:
// Tools and apps → API tokens → [Your Token] → API Client Secret
$clientSecret = 'your_api_client_secret';

// Get raw request body (must be exact JSON string)
$requestBody = file_get_contents('php://input'); // or from your HTTP framework

// Get signature from request headers
$signature = $_SERVER['HTTP_X_RECHARGE_HMAC_SHA256'] ?? ''; // or from your HTTP framework

// Validate signature
if (WebhookValidator::isValid($clientSecret, $requestBody, $signature)) {
    // Webhook is authentic - process it
    $data = json_decode($requestBody, true);
    // ... handle webhook data
} else {
    // Invalid signature - reject request
    http_response_code(401);
    exit('Invalid webhook signature');
}

// Alternative: Extract signature from headers array
$headers = getallheaders(); // or from your HTTP framework
if (WebhookValidator::validateFromHeaders($clientSecret, $requestBody, $headers)) {
    // Webhook is valid
}

// Alternative: Use with PSR-7 ServerRequestInterface
use Psr\Http\Message\ServerRequestInterface;

function handleWebhook(ServerRequestInterface $request): void
{
    $clientSecret = 'your_api_client_secret';
    
    if (WebhookValidator::validateFromPsr7($clientSecret, $request)) {
        $data = json_decode((string) $request->getBody(), true);
        // ... process webhook
    }
}
```

**Important Notes:**
- The API Client Secret is **different** from your API token
- Find it in: Recharge merchant portal → Tools and apps → API tokens → [Your Token]
- The request body must be the **exact JSON string** as received (even one space difference will fail)
- Always use `WebhookValidator` to prevent unauthorized requests

See [Sorting Documentation](sorting.md) for available sort options.
