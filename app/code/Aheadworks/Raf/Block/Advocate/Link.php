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
namespace Aheadworks\Raf\Block\Advocate;

use Aheadworks\Raf\Api\AdvocateManagementInterface;
use Magento\Framework\View\Element\Html\Link\Current as LinkCurrent;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\DefaultPathInterface;

/**
 * Class Link
 *
 * @package Aheadworks\Raf\Block\Advocate
 */
class Link extends LinkCurrent
{
    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var AdvocateManagementInterface
     */
    private $advocateManagement;

    /**
     * @param Context $context
     * @param DefaultPathInterface $defaultPath
     * @param CustomerSession $customerSession
     * @param AdvocateManagementInterface $advocateManagement
     * @param array $data
     */
    public function __construct(
        Context $context,
        DefaultPathInterface $defaultPath,
        CustomerSession $customerSession,
        AdvocateManagementInterface $advocateManagement,
        array $data = []
    ) {
        parent::__construct($context, $defaultPath, $data);
        $this->customerSession = $customerSession;
        $this->advocateManagement = $advocateManagement;
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        $customerId = $this->customerSession->getCustomerId();
        $websiteId = $this->_storeManager->getWebsite()->getId();
        if (!$this->advocateManagement->canUseReferralProgramAndSpend($customerId, $websiteId)) {
            return '';
        }

        return parent::_toHtml();
    }
}
