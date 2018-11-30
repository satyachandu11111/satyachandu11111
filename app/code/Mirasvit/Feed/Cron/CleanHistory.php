<?php
namespace Mirasvit\Feed\Cron;

use Mirasvit\Feed\Model\ResourceModel\Feed\History\CollectionFactory as HistoryCollectionFactory;

class CleanHistory
{
    /**
     * @var HistoryCollectionFactory
     */
    protected $historyCollectionFactory;

    /**
     * @param HistoryCollectionFactory $historyCollectionFactory
     */
    public function __construct(
        HistoryCollectionFactory $historyCollectionFactory
    ) {
        $this->historyCollectionFactory = $historyCollectionFactory;
    }

    /**
     * Execute
     * @return void
     */
    public function execute()
    {
        $date = new \Zend_Date();
        $date->subDay(3);

        $collection = $this->historyCollectionFactory->create()
            ->addFieldToFilter('created_at', ['lt' => $date->toString('Y-MM-dd H:mm:s')]);

        foreach ($collection as $item) {
            $item->delete();
        }
    }
}