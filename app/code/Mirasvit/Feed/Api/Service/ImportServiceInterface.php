<?php
namespace Mirasvit\Feed\Api\Service;

interface ImportServiceInterface
{
    /**
     * @param  object $object
     * @param  string $filePath
     * @return object
     */
    public function import($object, $filePath);

}