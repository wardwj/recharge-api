# Async Batches

Perform bulk operations efficiently using async batches in Recharge.

Async batches allow you to perform bulk operations efficiently. Create a batch, add tasks to it, then process all tasks together.

## List Async Batches

```php
use Recharge\Enums\AsyncBatchType;
use Recharge\Enums\Sort\AsyncBatchSort;

// List async batches
foreach ($client->asyncBatches()->list() as $batch) {
    echo "Batch ID: {$batch->id}, Type: {$batch->batchType->value}, Status: {$batch->status->value}\n";
}

// With sorting
foreach ($client->asyncBatches()->list(['sort_by' => AsyncBatchSort::CREATED_AT_DESC]) as $batch) {
    // Batches sorted by creation date (newest first)
}
```

## Get Single Async Batch

```php
$batch = $client->asyncBatches()->get(123);
```

## Create Async Batch

```php
// Create a batch for discount creation
$batch = $client->asyncBatches()->create(AsyncBatchType::DISCOUNT_CREATE);
```

## Process Async Batch

```php
// Process the batch (submits all tasks for processing)
$batch = $client->asyncBatches()->process($batch->id);
```

## Add Tasks to Batch

```php
// Add tasks to the batch (up to 1,000 tasks per request)
$tasks = $client->asyncBatchTasks()->addTasks($batch->id, [
    ['body' => ['code' => 'DISCOUNT1', 'discount_type' => 'percentage', 'value' => 10]],
    ['body' => ['code' => 'DISCOUNT2', 'discount_type' => 'percentage', 'value' => 20]],
    ['body' => ['code' => 'DISCOUNT3', 'discount_type' => 'fixed_amount', 'value' => 500]],
]);
```

## List Tasks in Batch

```php
// List tasks in a batch
foreach ($client->asyncBatchTasks()->list($batch->id) as $task) {
    if ($task->isSuccessful()) {
        echo "Task {$task->id} succeeded\n";
    } elseif ($task->hasFailed()) {
        echo "Task {$task->id} failed\n";
    }
}

// Filter tasks by IDs
$specificTasks = $client->asyncBatchTasks()->list($batch->id, [
    'ids' => [1, 2, 3], // Comma-separated or array
]);
```

## Available Batch Types

Use the `AsyncBatchType` enum for type-safe batch types. Some batch types are version-specific:

```php
use Recharge\Enums\AsyncBatchType;
use Recharge\Enums\ApiVersion;

// Discount operations (both versions)
AsyncBatchType::DISCOUNT_CREATE
AsyncBatchType::DISCOUNT_UPDATE
AsyncBatchType::DISCOUNT_DELETE

// Plans operations (2021-11 only)
AsyncBatchType::BULK_PLANS_CREATE
AsyncBatchType::BULK_PLANS_UPDATE
AsyncBatchType::BULK_PLANS_DELETE

// One-time operations (both versions)
AsyncBatchType::ONETIME_CREATE
AsyncBatchType::ONETIME_DELETE

// Check if a batch type is available in a version
$batchType = AsyncBatchType::BULK_PLANS_CREATE;
if ($batchType->isAvailableIn(ApiVersion::V2021_11)) {
    // Batch type is supported in 2021-11
}
```

## Batch Status

```php
use Recharge\Enums\AsyncBatchStatus;

$batch = $client->asyncBatches()->get(123);

if ($batch->status === AsyncBatchStatus::NOT_STARTED) {
    // Batch hasn't been processed yet
} elseif ($batch->status === AsyncBatchStatus::PROCESSING) {
    // Batch is currently processing
} elseif ($batch->status === AsyncBatchStatus::COMPLETED) {
    // Batch completed successfully
} elseif ($batch->status === AsyncBatchStatus::FAILED) {
    // Batch failed
}

// Helper methods
if ($batch->isTerminal()) {
    // Batch is in a terminal state (completed or failed)
}
```

**Important Notes:**
- Maximum 1,000 tasks per request when adding tasks
- Maximum 10,000 tasks per batch total
- Batch types are version-specific - the SDK validates that batch types are valid for the current API version
- Tasks contain a `body` object with the payload for the operation (same as individual API calls)
- Use `process()` after adding all tasks to submit the batch for processing
- Monitor batch status using `get()` or via webhooks (`async_batch/processed` topic)

See [Sorting Documentation](sorting.md) for available sort options.
