<?php

namespace Mirasvit\Feed\Service;

use Mirasvit\Core\Service\YamlService;
use Mirasvit\Feed\Api\Service\ExportServiceInterface;

class ExportService implements ExportServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function export($entityModel, $path)
    {
        $yaml = YamlService::dump(
            $entityModel->toArray($entityModel->getRowsToExport()),
            10
        );

        file_put_contents($path, $yaml);
    }
}