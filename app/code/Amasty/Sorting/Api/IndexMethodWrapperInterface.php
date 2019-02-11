<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Api;

/**
 * Interface IndexMethodWrapper
 * @api
 */
interface IndexMethodWrapperInterface
{
    /**
     * @return \Amasty\Sorting\Api\IndexedMethodInterface
     */
    public function getSource();

    /**
     * @return \Magento\Framework\Indexer\ActionInterface
     */
    public function getIndexer();
}
