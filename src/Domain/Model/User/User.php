<?php

declare(strict_types=1);

namespace Domain\Model\User;

use JsonSerializable;

/**
 * User.
 */
class User implements JsonSerializable
{
    /**
     * Create a new user.
     *
     * @param positive-int $id    ID
     * @param string       $name  name
     * @param string       $email email address
     * @param int<0, max>  $age   age
     * @param Sex          $sex   sex
     */
    public function __construct(
        private int $id,
        private string $name,
        private string $email,
        private int $age,
        private Sex $sex,
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
     * Return the email address.
     *
     * @return string email address
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Return the age.
     *
     * @return int<0, max> age
     */
    public function getAge(): int
    {
        return $this->age;
    }

    /**
     * Return the sex.
     *
     * @return Sex sex
     */
    public function getSex(): Sex
    {
        return $this->sex;
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

    /**
     * Convert to array.
     *
     * @return array{id: positive-int, name: string, email: string, age: int<0, max>, sex: string}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'age' => $this->getAge(),
            'sex' => $this->getSex()->value,
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return array{id: positive-int, name: string, email: string, age: int<0, max>, sex: string}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
