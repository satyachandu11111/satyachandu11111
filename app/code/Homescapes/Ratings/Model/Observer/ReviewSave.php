<?php
namespace Homescapes\Ratings\Model\Observer;

use Magento\Framework\Event\ObserverInterface;

class ReviewSave implements ObserverInterface
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->_resource = $resource;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $review = $observer->getEvent()->getDataObject();
        $connection = $this->_resource;

        $tableName = $connection->getTableName('review_detail');
        $detail = [
            'email' => $review->getEmail(),
            'recommend' => $review->getRecommend(),
        ];

        if (empty($review->getEmail())) return;
        
        $select = $connection->getConnection()->select()->from($tableName)->where('review_id = :review_id');
        $detailId = $connection->getConnection()->fetchOne($select, [':review_id' => $review->getId()]);

        if ($detailId) {
            $condition = ["detail_id = ?" => $detailId];
            $connection->getConnection()->update($tableName, $detail, $condition);
        } else {
            $detail['store_id'] = $review->getStoreId();
            $detail['customer_id'] = $review->getCustomerId();
            $detail['review_id'] = $review->getId();
            $connection->getConnection()->insert($tableName, $detail);
        }   
    }
}