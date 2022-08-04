<?php

declare(strict_types=1);

namespace Application\Service;

/**
 * Null request.
 */
class NullRequest implements RequestInterface
{
    /** @var null|self Singleton instance of this class. */
    private static $instance;

    private function __construct()
    {
    }

    /**
     * Return the singleton instance of this class.
     *
     * @return self singleton instance of this class
     */
    public static function getInstance(): self
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
