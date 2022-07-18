<?php

declare(strict_types=1);

namespace Domain\Model\User;

/**
 * Sex.
 */
enum Sex: string
{
    case Male = 'male';

    case Female = 'female';

    /**
     * Verify if the object is identical to this.
     *
     * @param mixed $other other object
     */
    public function equals($other): bool
    {
        return $other instanceof self && $other->value === $this->value;
    }
}
