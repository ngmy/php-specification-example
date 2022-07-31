<?php

declare(strict_types=1);

namespace Application\Service\User;

/**
 * Request class for search user service.
 */
class SearchUserRequest
{
    /**
     * Create a new search user request.
     *
     * @param string $name  name
     * @param string $email email address
     * @param int<0, max>[] $generations generations
     * @param string[] $sexes sexes
     * @param string[] $roles roles
     */
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $email = null,
        public readonly array $generations = [],
        public readonly array $sexes = [],
        public readonly array $roles = [],
    ) {
    }
}
