<?php

declare(strict_types=1);

namespace Application\Service;

use Application\DataTransformer\DataTransformerInterface;

/**
 * Application service interface.
 *
 * @template T of DataTransformerInterface
 */
interface ServiceInterface
{
    /**
     * Execute the application service.
     *
     * @param RequestInterface $request request for service
     */
    public function execute(RequestInterface $request): void;

    /**
     * Return the data transformer.
     *
     * @return T
     */
    public function getDataTransformer(): DataTransformerInterface;
}
