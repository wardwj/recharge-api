<?php

declare(strict_types=1);

namespace Recharge\Data;

use Carbon\CarbonImmutable;
use Recharge\Contracts\DataTransferObjectInterface;
use Recharge\Enums\PaymentMethodStatus;
use Recharge\Enums\PaymentType;
use Recharge\Enums\ProcessorName;

/**
 * Payment Method Data Transfer Object
 *
 * Immutable DTO representing a Recharge payment method.
 * Payment methods are primarily available in API version 2021-11.
 * Payment sources in 2021-01 are deprecated.
 *
 * @see https://developer.rechargepayments.com/2021-11/payment_methods#the-payment-method-object
 */
final readonly class PaymentMethod implements DataTransferObjectInterface
{
    /**
     * @param int $id Unique numeric identifier for the Payment Method
     * @param int $customerId Customer ID that owns this payment method
     * @param bool $default Whether this is the customer's default payment method
     * @param PaymentType|null $paymentType Type of payment method
     * @param ProcessorName|null $processorName Payment processor name
     * @param string|null $processorCustomerToken Customer token in the payment processor
     * @param string|null $processorPaymentMethodToken Payment method token in the processor
     * @param array<string, mixed>|null $paymentDetails Payment details (brand, last4, exp_month, exp_year for credit cards)
     * @param array<string, mixed>|null $billingAddress Billing address associated with the payment method
     * @param PaymentMethodStatus|null $status Payment method status
     * @param string|null $statusReason Reason for status (often populated when status is invalid)
     * @param bool|null $retryCharges Whether to retry charges with previous errors
     * @param CarbonImmutable|null $createdAt Created timestamp
     * @param CarbonImmutable|null $updatedAt Updated timestamp
     * @param array<string, mixed> $rawData Raw API response data
     */
    public function __construct(
        public int $id,
        public int $customerId,
        public bool $default = false,
        public ?PaymentType $paymentType = null,
        public ?ProcessorName $processorName = null,
        public ?string $processorCustomerToken = null,
        public ?string $processorPaymentMethodToken = null,
        public ?array $paymentDetails = null,
        public ?array $billingAddress = null,
        public ?PaymentMethodStatus $status = null,
        public ?string $statusReason = null,
        public ?bool $retryCharges = null,
        public ?CarbonImmutable $createdAt = null,
        public ?CarbonImmutable $updatedAt = null,
        public array $rawData = []
    ) {
    }

    /**
     * Create from API response array
     *
     * @param array<string, mixed> $data Payment method data from API
     */
    public static function fromArray(array $data): static
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            customerId: (int) ($data['customer_id'] ?? 0),
            default: isset($data['default']) ? (bool) $data['default'] : false,
            paymentType: isset($data['payment_type']) ? PaymentType::tryFrom($data['payment_type']) : null,
            processorName: isset($data['processor_name']) ? ProcessorName::tryFrom($data['processor_name']) : null,
            processorCustomerToken: $data['processor_customer_token'] ?? null,
            processorPaymentMethodToken: $data['processor_payment_method_token'] ?? null,
            paymentDetails: $data['payment_details'] ?? null,
            billingAddress: $data['billing_address'] ?? null,
            status: isset($data['status']) ? PaymentMethodStatus::tryFrom($data['status']) : null,
            statusReason: $data['status_reason'] ?? null,
            retryCharges: isset($data['retry_charges']) ? (bool) $data['retry_charges'] : null,
            createdAt: isset($data['created_at'])
                ? CarbonImmutable::parse($data['created_at'])
                : null,
            updatedAt: isset($data['updated_at'])
                ? CarbonImmutable::parse($data['updated_at'])
                : null,
            rawData: $data
        );
    }

    /**
     * Check if this payment method is valid
     */
    public function isValid(): bool
    {
        return $this->status === PaymentMethodStatus::VALID;
    }

    /**
     * Check if this payment method is a credit card
     */
    public function isCreditCard(): bool
    {
        return $this->paymentType === PaymentType::CREDIT_CARD;
    }

    /**
     * Get the last 4 digits of the card (if credit card)
     */
    public function getLast4(): ?string
    {
        if ($this->isCreditCard() && is_array($this->paymentDetails)) {
            return $this->paymentDetails['last4'] ?? null;
        }

        return null;
    }

    /**
     * Convert to array for serialization
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'customer_id' => $this->customerId,
            'default' => $this->default,
            'payment_type' => $this->paymentType?->value,
            'processor_name' => $this->processorName?->value,
            'processor_customer_token' => $this->processorCustomerToken,
            'processor_payment_method_token' => $this->processorPaymentMethodToken,
            'payment_details' => $this->paymentDetails,
            'billing_address' => $this->billingAddress,
            'status' => $this->status?->value,
            'status_reason' => $this->statusReason,
            'retry_charges' => $this->retryCharges,
            'created_at' => $this->createdAt?->toIso8601String(),
            'updated_at' => $this->updatedAt?->toIso8601String(),
        ], fn ($value): bool => $value !== null);
    }

    /**
     * Get raw API response data
     *
     * @return array<string, mixed>
     */
    public function getRawData(): array
    {
        return $this->rawData;
    }
}
