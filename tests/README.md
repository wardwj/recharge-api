# Test Suite

## Overview

Comprehensive test suite for the Recharge API SDK with unit tests covering all major components.

## Structure

```
tests/
├── Unit/                          # Unit tests (no API calls)
│   ├── ClientTest.php            # Client initialization & configuration
│   ├── CollectionTest.php        # Collection operations
│   ├── BatchProcessorTest.php    # Batch processing
│   ├── ValidationTest.php        # Request validation
│   └── EnumTest.php              # Enum functionality
└── README.md                      # This file
```

## Running Tests

### Prerequisites

```bash
composer install
```

### Run All Tests

```bash
composer test
# or
./vendor/bin/phpunit
```

### Run with Test Output

```bash
./vendor/bin/phpunit --testdox
```

### Run Specific Test Suite

```bash
./vendor/bin/phpunit tests/Unit/ClientTest.php
./vendor/bin/phpunit tests/Unit/CollectionTest.php
```

### Run with Coverage

```bash
composer test:coverage
# or
./vendor/bin/phpunit --coverage-html coverage
```

## Test Categories

### Unit Tests ✅

**No external dependencies or API calls**

- ✅ **ClientTest** - 8 tests
  - Client initialization
  - Version switching
  - Resource accessors
  - Configuration
  - Logger awareness

- ✅ **CollectionTest** - 20+ tests
  - Creation & basic operations
  - Functional operations (map, filter, reduce)
  - Iteration & array access
  - Immutability
  - JSON serialization

- ✅ **BatchProcessorTest** - 12+ tests
  - Batch processing
  - Error handling
  - Success/failure tracking
  - Async processing
  - Result metrics

- ✅ **ValidationTest** - 15+ tests
  - Request DTO validation
  - Builder pattern validation
  - Field-level errors
  - Validation rules

- ✅ **EnumTest** - 10+ tests
  - ApiVersion enum
  - IntervalUnit enum
  - SubscriptionStatus enum
  - Case-insensitive matching

## Test Coverage

Current coverage focuses on:

- ✅ Client initialization and configuration
- ✅ Type-safe collections
- ✅ Batch processing with error handling
- ✅ Request validation
- ✅ Enum functionality
- ✅ Builder pattern
- ✅ Immutability guarantees

## Writing New Tests

### Unit Test Template

```php
<?php

declare(strict_types=1);

namespace Recharge\Tests\Unit;

use PHPUnit\Framework\TestCase;

class MyFeatureTest extends TestCase
{
    public function testFeatureWorks(): void
    {
        // Arrange
        $input = 'test';
        
        // Act
        $result = myFunction($input);
        
        // Assert
        $this->assertEquals('expected', $result);
    }
}
```

### Best Practices

1. **Use descriptive test names**
   ```php
   public function testClientThrowsExceptionForEmptyToken(): void
   ```

2. **Follow AAA pattern**
   - Arrange: Set up test data
   - Act: Execute the code
   - Assert: Verify results

3. **Test one thing per test**
   - Each test should verify a single behavior

4. **Use type hints**
   ```php
   public function testSomething(): void
   ```

5. **Use data providers for multiple scenarios**
   ```php
   /**
    * @dataProvider invalidInputProvider
    */
   public function testValidation($input): void
   ```

## Integration Tests (Future)

Integration tests will require:
- Valid Recharge API token
- Set `RECHARGE_API_TOKEN` environment variable
- Use Guzzle MockHandler to avoid real API calls

Example:
```php
protected function setUp(): void
{
    $this->mockHandler = new MockHandler([
        new Response(200, [], json_encode(['data' => 'test'])),
    ]);
    
    // Inject mock handler into client
}
```

## Continuous Integration

### GitHub Actions Example

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
      - run: composer install
      - run: composer test
      - run: composer analyse
```

## Code Quality

### Static Analysis

```bash
composer analyse
# Runs PHPStan level 8
```

### Code Style

```bash
composer check-style  # Check PSR-12 compliance
composer fix-style    # Auto-fix style issues
```

## Test Metrics

### Current Status

- **Total Tests**: 65+
- **Assertions**: 150+
- **Test Coverage**: Unit tests complete
- **Code Quality**: PHPStan Level 8 ✅
- **Style**: PSR-12 Compliant ✅

### Coverage Goals

- [ ] Integration tests with MockHandler
- [ ] Paginator edge cases
- [ ] Rate limiting scenarios
- [ ] Error context verification
- [ ] Logging integration tests

## Troubleshooting

### Vendor Directory Missing

```bash
composer install
```

### PHPUnit Not Found

```bash
composer require --dev phpunit/phpunit
```

### Tests Fail Due to Missing Extensions

Check `php -m` for required extensions:
- json
- curl
- mbstring

### Memory Limit Issues

```bash
php -d memory_limit=512M vendor/bin/phpunit
```

## Contributing

When adding new features:

1. Write tests first (TDD)
2. Ensure all tests pass
3. Maintain code coverage
4. Follow existing patterns
5. Update this README if needed

## Resources

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [PHPStan Documentation](https://phpstan.org/user-guide/getting-started)
- [PSR-12 Coding Standard](https://www.php-fig.org/psr/psr-12/)
