<?php
namespace Mirasvit\Feed\Export\Resolver;

use Magento\Review\Model\Review;

class ReviewResolver extends AbstractResolver
{
    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return [];
    }

    /**
     * Associated product
     *
     * @param Review $review
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct($review)
    {
        return $review->getProductCollection()
            ->addFieldToFilter('entity_id', $review->getEntityPkValue())
            ->getFirstItem();
    }

    /**
     * @param Review $review
     * @return float
     */
    public function getRating($review)
    {
        $product = $this->getProduct($review);
        $review->getEntitySummary($product);
        $ratingSummary = $product->getRatingSummary()->getRatingSummary();

        if ($ratingSummary > 0) {
            return ($ratingSummary/100)*5;
        }

        return 5;
    }
}
