<?php

namespace Mirasvit\Feed\Service;

use Mirasvit\Feed\Api\Service\ImportServiceInterface;
use Mirasvit\Core\Service\YamlService;

class ImportService implements ImportServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function import($object, $filePath)
    {
        $content = file_get_contents($filePath);
        $data = YamlService::parse($content);

        $object->setData($data);

        $object
            ->setIsActive(1)
            ->save();

        return $object;
    }
}