<?php

declare(strict_types=1);

namespace Application\DataTransformer\User;

/**
 * @method list<array{id: positive-int, name: string, email: string, age: int<0, max>, sex: string}> read()
 */
class UsersArrayDataTransformer extends AbstractUsersDataTransformer
{
    protected $data;

    /**
     * {@inheritdoc}
     */
    public function write(mixed $data): self
    {
        $data2 = [];
        foreach ($data as $user) {
            $data2[] = $user->toArray();
        }

        $this->data = $data2;

        return $this;
    }
}
