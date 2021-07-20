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
namespace Aheadworks\Raf\Model\Advocate;

use Magento\Framework\UrlInterface;

/**
 * Class Url
 *
 * @package Aheadworks\Raf\Model\Advocate
 */
class Url
{
    /**
     * @var string
     */
    const REFERRAL_PARAM = 'awraf';

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param UrlInterface $urlBuilder
     */
    public function __construct(UrlInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Retrieve create referral link url
     *
     * @return string
     */
    public function getCreateReferralLinkUrl()
    {
        return $this->urlBuilder->getUrl('aw_raf/advocate/createLink');
    }

    /**
     * Retrieve referral url
     *
     * @param string $referralLink
     * @return string
     */
    public function getReferralUrl($referralLink)
    {
        $query = [self::REFERRAL_PARAM => $referralLink];
        $params = [
            '_query' => $query
        ];
        return $this->urlBuilder->getUrl(null, $params);
    }
}
