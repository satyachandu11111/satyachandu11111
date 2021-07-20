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

use Aheadworks\Raf\Api\Data\TotalsInterface;
use Aheadworks\Raf\Api\QuoteManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;

/**
 * Class Quote
 *
 * @package Aheadworks\Raf\Model\Service
 */
class QuoteService implements QuoteManagementInterface
{
    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function updateReferralLink($quoteId, $referralLink)
    {
        try {
            /** @var Quote $quote */
            $quote = $this->quoteRepository->get($quoteId);
            $quote->setData(TotalsInterface::AW_RAF_REFERRAL_LINK, $referralLink);
            $this->quoteRepository->save($quote);
            return true;
        } catch (\Exception $e) {
        }

        return false;
    }
}
