<?php

declare(strict_types=1);

namespace Application\Service;

use Application\DataTransformer\DataTransformerInterface;

/**
 * Abstract base implementation of the `ServiceInterface`.
 *
 * @template T of DataTransformerInterface
 * @implements ServiceInterface<T>
 */
abstract class AbstractService implements ServiceInterface
{
    /** @var T Data transformer. */
    protected DataTransformerInterface $dataTransformer;

    /**
     * {@inheritdoc}
     */
    abstract public function execute(RequestInterface $request): void;

    /**
     * {@inheritdoc}
     */
    public function getDataTransformer(): DataTransformerInterface
    {
        return $this->dataTransformer;
    }
}
