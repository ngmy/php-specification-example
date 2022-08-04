<?php

declare(strict_types=1);

namespace Application\DataTransformer\User;

/**
 * @method string read()
 */
class UsersJsonDataTransformer extends AbstractUsersDataTransformer
{
    /**
     * {@inheritdoc}
     */
    public function write(mixed $data): self
    {
        $this->data = json_encode($data);

        return $this;
    }
}
