<?php

declare(strict_types=1);

namespace Recharge\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Recharge\Exceptions\ValidationException;
use Recharge\Requests\CreateSubscriptionData;

class ValidationTest extends TestCase
{
    public function testCreateSubscriptionDataRequiresCustomerId(): void
    {
        $this->expectException(ValidationException::class);

        $data = new CreateSubscriptionData(
            customerId: 0,
            interval: '1 month'
        );

        $data->validate();
    }

    public function testCreateSubscriptionDataRequiresValidQuantity(): void
    {
        $this->expectException(ValidationException::class);

        $data = new CreateSubscriptionData(
            customerId: 123,
            quantity: 0,
            interval: '1 month'
        );

        $data->validate();
    }

    public function testCreateSubscriptionDataValidatesIntervalFormat(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid interval format');

        $data = new CreateSubscriptionData(
            customerId: 123,
            interval: 'invalid'
        );
        $data->toArray(); // Validation happens here
    }

    public function testCreateSubscriptionDataValidatesIntervalUnit(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid interval unit');

        $data = new CreateSubscriptionData(
            customerId: 123,
            interval: '1 invalid'
        );
        $data->toArray(); // Validation happens here
    }

    public function testCreateSubscriptionDataValidatesIntervalFrequency(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid interval frequency');

        $data = new CreateSubscriptionData(
            customerId: 123,
            interval: '0 month'
        );
        $data->toArray(); // Validation happens here
    }

    public function testCreateSubscriptionDataAcceptsValidData(): void
    {
        $data = new CreateSubscriptionData(
            customerId: 123,
            addressId: 456,
            quantity: 2,
            price: 29.99,
            interval: '1 month',
            productTitle: 'Test Product'
        );

        $this->assertEquals(123, $data->customerId);
        $this->assertEquals(456, $data->addressId);
        $this->assertEquals(2, $data->quantity);
        $this->assertEquals(29.99, $data->price);
    }

    public function testCreateSubscriptionDataToArray(): void
    {
        $data = new CreateSubscriptionData(
            customerId: 123,
            quantity: 2,
            price: 29.99,
            interval: '1 month'
        );

        $array = $data->toArray();

        $this->assertEquals(123, $array['customer_id']);
        $this->assertEquals(2, $array['quantity']);
        $this->assertEquals(29.99, $array['price']);
        $this->assertEquals('month', $array['order_interval_unit']);
        $this->assertEquals(1, $array['order_interval_frequency']);
    }

    public function testValidationExceptionIncludesErrors(): void
    {
        try {
            $data = new CreateSubscriptionData(
                customerId: -1,
                quantity: 0,
                interval: '1 month'
            );
            $data->validate();

            $this->fail('Expected ValidationException to be thrown');
        } catch (ValidationException $e) {
            $errors = $e->getErrors();

            $this->assertIsArray($errors);
            $this->assertArrayHasKey('customer_id', $errors);
            $this->assertArrayHasKey('quantity', $errors);
        }
    }

    public function testValidationExceptionFieldAccess(): void
    {
        try {
            $data = new CreateSubscriptionData(
                customerId: 0,
                interval: '1 month'
            );
            $data->validate();

            $this->fail('Expected ValidationException to be thrown');
        } catch (ValidationException $e) {
            $this->assertTrue($e->hasError('customer_id'));
            $this->assertNotNull($e->getError('customer_id'));
            $this->assertFalse($e->hasError('nonexistent_field'));
            $this->assertNull($e->getError('nonexistent_field'));
        }
    }

    public function testCreateSubscriptionDataFromArray(): void
    {
        $data = CreateSubscriptionData::fromArray([
            'customer_id' => 123,
            'address_id' => 456,
            'quantity' => 2,
            'price' => 29.99,
            'interval' => '1 month',
            'product_title' => 'Test Product',
        ]);

        $this->assertInstanceOf(CreateSubscriptionData::class, $data);
        $this->assertEquals(123, $data->customerId);
        $this->assertEquals(456, $data->addressId);
        $this->assertEquals('Test Product', $data->productTitle);
    }
}
