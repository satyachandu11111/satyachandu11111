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
namespace Aheadworks\Raf\Observer;

use Aheadworks\Raf\Api\QuoteManagementInterface;
use Aheadworks\Raf\Model\Advocate\Url;
use Aheadworks\Raf\Model\Friend\Referral\CookieManagement;
use Aheadworks\Raf\Observer\ReferralLinkChecker\Redirect;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Psr\Log\LoggerInterface;
use Aheadworks\Raf\Api\FriendManagementInterface;
use Aheadworks\Raf\Model\Metadata\Friend\Builder as FriendMetadataBuilder;
use \Magento\Checkout\Model\Session as CheckoutSession;
use Aheadworks\Raf\Api\AdvocateSummaryRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class ReferralLinkChecker
 *
 * @package Aheadworks\Raf\Observer
 */
class ReferralLinkChecker implements ObserverInterface
{
    /**
     * @var CookieManagement
     */
    private $cookieManagement;

    /**
     * @var Redirect
     */
    private $redirect;

    /**
     * @var FriendManagementInterface
     */
    private $friendManagement;

    /**
     * @var QuoteManagementInterface
     */
    private $quoteManagement;

    /**
     * @var FriendMetadataBuilder
     */
    private $friendMetadataBuilder;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var AdvocateSummaryRepositoryInterface
     */
    private $advocateRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param CookieManagement $cookieManagement
     * @param Redirect $redirect
     * @param FriendManagementInterface $friendManagement
     * @param QuoteManagementInterface $quoteManagement
     * @param FriendMetadataBuilder $friendMetadataBuilder
     * @param CheckoutSession $checkoutSession
     * @param LoggerInterface $logger
     * @param AdvocateSummaryRepositoryInterface $advocateRepository
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CookieManagement $cookieManagement,
        Redirect $redirect,
        FriendManagementInterface $friendManagement,
        QuoteManagementInterface $quoteManagement,
        FriendMetadataBuilder $friendMetadataBuilder,
        CheckoutSession $checkoutSession,
        LoggerInterface $logger,
        AdvocateSummaryRepositoryInterface $advocateRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->cookieManagement = $cookieManagement;
        $this->redirect = $redirect;
        $this->friendManagement = $friendManagement;
        $this->quoteManagement = $quoteManagement;
        $this->friendMetadataBuilder = $friendMetadataBuilder;
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
        $this->advocateRepository = $advocateRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * Check url on referral param and set referral id to cookie
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var RequestInterface $request */
        $request = $observer->getEvent()->getRequest();
        $quote = $this->checkoutSession->getQuote();

        $cookieReferralValue = $this->cookieManagement->getReferralValue();
        $referralValue = $request->getParam(Url::REFERRAL_PARAM);
        $websiteId = $this->storeManager->getStore()->getWebsiteId();

        try {
            $isAdvocateExist = (bool)$this->advocateRepository->getByReferralLink($referralValue, $websiteId)->getId();
        } catch (NoSuchEntityException $e) {
            $isAdvocateExist = false;
        }

        if (!$cookieReferralValue && $referralValue && $isAdvocateExist) {
            try {
                $this->cookieManagement->setReferralValue($referralValue);
                $welcomePopupCookieValue = $this->cookieManagement->getWelcomePopupValue();
                if (empty($welcomePopupCookieValue)) {
                    if ($this->friendManagement->canApplyDiscount($this->friendMetadataBuilder->build($quote))) {
                        $this->cookieManagement->setWelcomePopupValue(
                            CookieManagement::WELCOME_POPUP_COOKIE_VALUE_SHOW
                        );
                    } else {
                        $this->cookieManagement->setWelcomePopupValue(
                            CookieManagement::WELCOME_POPUP_COOKIE_VALUE_DO_NOT_SHOW
                        );
                    }
                }
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }

        if ($referralValue) {
            $this->redirect->createRedirect($observer);
        }

        $cookieReferralValue = $this->cookieManagement->getReferralValue();
        if ($cookieReferralValue && $quote->getId() && empty($quote->getAwRafReferralLink())) {
            $this->quoteManagement->updateReferralLink($quote->getId(), $cookieReferralValue);
        }
    }
}
