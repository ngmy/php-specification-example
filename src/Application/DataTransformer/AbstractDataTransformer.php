<?php

declare(strict_types=1);

namespace Application\DataTransformer;

/**
 * @template T
 * @implements DataTransformerInterface<T>
 */
abstract class AbstractDataTransformer implements DataTransformerInterface
{
    /** @var mixed Data. */
    protected $data;

    /**
     * {@inheritdoc}
     */
    public function read(): mixed
    {
        return $this->data;
    }
}
