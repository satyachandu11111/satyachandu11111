<?php
namespace Mirasvit\Feed\Api\Factory;

interface EntityFactoryInterface
{
    /**
     * @param  string $entityName
     * @return object
     */
    public function getEntityModelFactory($entityName);

}