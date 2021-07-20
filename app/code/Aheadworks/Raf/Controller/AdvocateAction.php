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
namespace Aheadworks\Raf\Controller;

use Aheadworks\Raf\Api\AdvocateManagementInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class AdvocateAction
 *
 * @package Aheadworks\Raf\Controller
 */
abstract class AdvocateAction extends Action
{
    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var AdvocateManagementInterface
     */
    protected $advocateManagement;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param StoreManagerInterface $storeManager
     * @param AdvocateManagementInterface $advocateManagement
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        StoreManagerInterface $storeManager,
        AdvocateManagementInterface $advocateManagement
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->advocateManagement = $advocateManagement;
    }

    /**
     * Check customer authentication for some actions
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NotFoundException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function dispatch(RequestInterface $request)
    {
        $customerId = $this->customerSession->getCustomerId();
        $websiteId = $this->storeManager->getWebsite()->getId();
        if (!$this->customerSession->authenticate()) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        if (!$this->customerSession->authenticate() ||
            !$this->advocateManagement->canUseReferralProgramAndSpend($customerId, $websiteId)
        ) {
            parent::dispatch($request);
            return $this->getResponse()->setRedirect(
                $this->_url->getBaseUrl()
            );
        }

        return parent::dispatch($request);
    }
}
