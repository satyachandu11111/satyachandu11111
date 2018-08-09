<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Model\Import;

interface ImportValidatorInterface
{
    /**
     * @param array  $rowData
     * @param string $behavior
     *
     * @return mixed
     */
    public function validateRow(array $rowData, $behavior);

    /**
     * @return array
     */
    public function getErrorMessages();
}
