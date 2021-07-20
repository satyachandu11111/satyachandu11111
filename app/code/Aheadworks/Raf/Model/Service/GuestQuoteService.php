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
namespace Aheadworks\Raf\Model\Service;

use Aheadworks\Raf\Api\GuestQuoteManagementInterface;
use Aheadworks\Raf\Api\QuoteManagementInterface;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;

/**
 * Class GuestQuoteService
 *
 * @package Aheadworks\Raf\Model\Service
 */
class GuestQuoteService implements GuestQuoteManagementInterface
{
    /**
     * @var QuoteManagementInterface
     */
    private $quoteManagement;

    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @param QuoteManagementInterface $quoteManagement
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     */
    public function __construct(
        QuoteManagementInterface $quoteManagement,
        QuoteIdMaskFactory $quoteIdMaskFactory
    ) {
        $this->quoteManagement = $quoteManagement;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function updateReferralLink($maskedId, $referralLink)
    {
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($maskedId, 'masked_id');
        return $this->quoteManagement->updateReferralLink($quoteIdMask->getQuoteId(), $referralLink);
    }
}
