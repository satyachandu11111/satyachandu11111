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
namespace Aheadworks\Raf\Block\Advocate\Info;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\Raf\Api\AdvocateManagementInterface;

/**
 * Class ReferralUrl
 * @package Aheadworks\Raf\Block\Advocate\Info
 */
class ReferralUrl extends Template
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'Aheadworks_Raf::advocate/info/referral_url.phtml';

    /**
     * @var AdvocateManagementInterface
     */
    private $advocateManagement;

    /**
     * @param Context $context
     * @param AdvocateManagementInterface $advocateManagement
     * @param array $data
     */
    public function __construct(
        Context $context,
        AdvocateManagementInterface $advocateManagement,
        array $data = []
    ) {
        $this->advocateManagement = $advocateManagement;
        parent::__construct($context, $data);
    }

    /**
     * Get advocate referral url
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAdvocateReferralUrl()
    {
        /** @var \Aheadworks\Raf\Block\Advocate\Info $info */
        $info = $this->getParentBlock();
        list($customerId, $websiteId) = $info->getCustomerIdAndWebsiteId();
        return $this->advocateManagement->getReferralUrl($customerId, $websiteId);
    }
}
