<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Indexer\Model\Indexer;
use Amasty\Sorting\Model\Indexer\Bestsellers\BestsellersProcessor;
use Amasty\Sorting\Model\Indexer\MostViewed\MostViewedProcessor;
use Amasty\Sorting\Model\Indexer\TopRated\TopRatedProcessor;
use Amasty\Sorting\Model\Indexer\Wished\WishedProcessor;
use Magento\Framework\App\State;

class InstallData implements InstallDataInterface
{
    /**
     * @var Indexer
     */
    private $indexer;

    /**
     * @var array
     */
    private $indexerIds = [
        BestsellersProcessor::INDEXER_ID,
        MostViewedProcessor::INDEXER_ID,
        TopRatedProcessor::INDEXER_ID,
        WishedProcessor::INDEXER_ID
    ];

    public function __construct(
        Indexer $indexer,
        State $state
    ) {
        try {
            $state->setAreaCode('adminhtml');
        } catch (\Exception $e) {
            //Area code is already set
        }

        $this->indexer = $indexer;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Exception
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        foreach ($this->indexerIds as $indexerId) {
            $indexer = $this->indexer
                ->load($indexerId);
            $indexer->reindexAll();
        }
    }
}
