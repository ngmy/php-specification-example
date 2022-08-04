<?php

declare(strict_types=1);

namespace Application\DataTransformer\User;

use Application\DataTransformer\AbstractDataTransformer;
use Domain\Model\User\User;

/**
 * @extends AbstractDataTransformer<iterable<array-key, User>>
 */
abstract class AbstractUsersDataTransformer extends AbstractDataTransformer
{
}
