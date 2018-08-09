<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Model\Indexer;

use Amasty\Sorting\Helper\Data;

/**
 * Class Summary
 */
class Summary
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var \Amasty\Sorting\Model\MethodProvider
     */
    private $methodProvider;

    /**
     * Summary constructor.
     *
     * @param Data                                 $helper
     * @param \Amasty\Sorting\Model\MethodProvider $methodProvider
     */
    public function __construct(
        \Amasty\Sorting\Helper\Data $helper,
        \Amasty\Sorting\Model\MethodProvider $methodProvider
    ) {
        $this->helper = $helper;
        $this->methodProvider = $methodProvider;
    }

    /**
     * @return void
     */
    public function reindexAll()
    {
        $methods = $this->methodProvider->getIndexedMethods();
        foreach ($methods as $methodWrapper) {
            // do full reindex if method not disabled
            $methodWrapper->getIndexer()->executeFull();
        }
    }
}
