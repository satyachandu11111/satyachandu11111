<?php
namespace Mirasvit\Feed\Api\Service;

interface ExportServiceInterface
{
    /**
     * @param object $object
     * @param string $filePath
     * @return bool
     */
    public function export($object, $filePath);
}