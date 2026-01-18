<?php

declare(strict_types=1);

namespace Recharge\Support;

use Recharge\Enums\ApiVersion;
use Recharge\RechargeClient;

/**
 * Version Context
 *
 * Automatically restores the original API version when the context is destroyed.
 * Useful for temporarily switching API versions for specific operations.
 */
final class VersionContext
{
    private bool $restored = false;

    /**
     * @param RechargeClient $client The Recharge API client
     * @param ApiVersion $originalVersion The original API version to restore
     */
    public function __construct(
        private readonly RechargeClient $client,
        private readonly ApiVersion $originalVersion
    ) {
    }

    /**
     * Restore the original API version
     */
    public function restore(): void
    {
        if ($this->restored) {
            return;
        }

        if ($this->client->getApiVersion() !== $this->originalVersion) {
            $this->client->setApiVersion($this->originalVersion);
        }

        $this->restored = true;
    }

    /**
     * Automatically restore on destruction
     */
    public function __destruct()
    {
        $this->restore();
    }
}
