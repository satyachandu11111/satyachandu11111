<?php
namespace Mirasvit\Feed\Export\Liquid;

/**
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD)
 */

class BlankFileSystem
{
    /**
     * Retrieve a template file
     *
     * @param string $templatePath
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    function readTemplateFile($templatePath)
    {
        throw new \Exception("This liquid context does not allow includes.");
    }
}
