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
namespace Aheadworks\Raf\Plugin\Model\Quote;

use Magento\Quote\Model\Quote;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\RequestInterface;

/**
 * Class QuoteRepositoryPlugin
 *
 * @package Aheadworks\Raf\Plugin\Model\Quote
 */
class QuoteRepositoryPlugin
{
    /**
     * RequestInterface
     */
    private $request;

    /**
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * Throw exception to display a message
     *
     * @param CartRepositoryInterface $subject
     * @param $result
     * @param Quote $quote
     * @return null
     * @throws LocalizedException
     */
    public function afterSave(CartRepositoryInterface $subject, $result, $quote)
    {
        if ($quote->getAwRafThrowException()) {
            throw new LocalizedException(__('Additional discount cannot be applied with referral discount'));
        }

        return $result;
    }
}
