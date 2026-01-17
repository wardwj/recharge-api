<?php

declare(strict_types=1);

namespace Recharge\Concerns;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Has Logging Trait
 *
 * Provides PSR-3 logging capabilities to classes.
 * Uses NullLogger by default to avoid forcing logger dependency.
 */
trait HasLogging
{
    private LoggerInterface $logger;

    /**
     * Set logger instance
     *
     * @param LoggerInterface $logger PSR-3 logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * Get logger instance
     */
    protected function getLogger(): LoggerInterface
    {
        if (!isset($this->logger)) {
            $this->logger = new NullLogger();
        }

        return $this->logger;
    }

    /**
     * Log debug message
     *
     * @param string $message Log message
     * @param array<string, mixed> $context Additional context
     */
    protected function logDebug(string $message, array $context = []): void
    {
        $this->getLogger()->debug($message, $context);
    }

    /**
     * Log info message
     *
     * @param string $message Log message
     * @param array<string, mixed> $context Additional context
     */
    protected function logInfo(string $message, array $context = []): void
    {
        $this->getLogger()->info($message, $context);
    }

    /**
     * Log warning message
     *
     * @param string $message Log message
     * @param array<string, mixed> $context Additional context
     */
    protected function logWarning(string $message, array $context = []): void
    {
        $this->getLogger()->warning($message, $context);
    }

    /**
     * Log error message
     *
     * @param string $message Log message
     * @param array<string, mixed> $context Additional context
     */
    protected function logError(string $message, array $context = []): void
    {
        $this->getLogger()->error($message, $context);
    }
}
