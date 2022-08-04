<?php

declare(strict_types=1);

namespace Application\DataTransformer;

/**
 * Data transformer interface.
 *
 * @template T
 */
interface DataTransformerInterface
{
    /**
     * Write data to data transformer.
     *
     * @param T $data data
     *
     * @return $this
     */
    public function write(mixed $data): self;

    /**
     * Read data from data transformer.
     */
    public function read(): mixed;
}
