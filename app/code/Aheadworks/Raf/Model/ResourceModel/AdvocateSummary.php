<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Raf
 * @version    1.1.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Raf\Model\ResourceModel;

/**
 * Class AdvocateSummary
 * @package Aheadworks\Raf\Model\ResourceModel
 */
class AdvocateSummary extends AbstractResourceModel
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('aw_raf_summary', 'id');
    }

    /**
     * Get advocate summer item id by customer id
     *
     * @param int $customerId
     * @param int $websiteId
     * @return int|false
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAdvocateSummaryItemIdByCustomerId($customerId, $websiteId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), $this->getIdFieldName())
            ->where('customer_id = :customer_id')
            ->where('website_id = :website_id');

        return $connection->fetchOne($select, ['customer_id' => $customerId, 'website_id' => $websiteId]);
    }

    /**
     * Get advocate summer item id by referral link
     *
     * @param int $referralLink
     * @param int $websiteId
     * @return int|false
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAdvocateSummaryItemIdByReferralLink($referralLink, $websiteId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), $this->getIdFieldName())
            ->where('referral_link = :referral_link')
            ->where('website_id = :website_id');

        return $connection->fetchOne($select, ['referral_link' => $referralLink, 'website_id' => $websiteId]);
    }
}
