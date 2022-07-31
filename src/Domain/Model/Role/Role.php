<?php

declare(strict_types=1);

namespace Domain\Model\Role;

use Domain\Model\User\User;

/**
 * Role.
 */
class Role
{
    /**
     * Create a new role.
     *
     * @param positive-int $id          ID
     * @param string       $name        name
     * @param string       $slug        slug
     * @param string       $description description
     * @param User[]       $users       users
     */
    public function __construct(
        private int $id,
        private string $name,
        private string $slug,
        private string $description,
        private array $users,
    ) {
    }

    /**
     * Return the ID.
     *
     * @return positive-int ID
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Return the name.
     *
     * @return string name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Return the slug.
     *
     * @return string slug
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Return the description.
     *
     * @return string description
     */
    public function getDescrition(): string
    {
        return $this->description;
    }

    /**
     * Return the users.
     *
     * @return User[] users
     */
    public function getUsers(): array
    {
        return $this->users;
    }

    /**
     * Verify if the object is identical to this.
     *
     * @param mixed $other other object
     */
    public function equals($other): bool
    {
        return $other instanceof self && $other->getId() === $this->getId();
    }
}
