<?php

namespace Dividebuy\Payment\Model;

class AccountManagement extends \Magento\Customer\Model\AccountManagement {

    public function isEmailAvailable($customerEmail, $websiteId = null) {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart');
        $shippingAddress = $cart->getQuote()->getShippingAddress();

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/guestFriendsFamily.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('------------');
        try {
            if ($shippingAddress) {
                $shippingAddress->setData('email', $customerEmail);
                $shippingAddress->save();
            }
        } catch (NoSuchEntityException $e) {
            $logger->info($e->getMessage());
        }

        /*try {
            if ($websiteId === null) {
                $websiteId = $this->storeManager->getStore()->getWebsiteId();
            }
            $this->customerRepository->get($customerEmail, $websiteId);
            return false;
        } catch (NoSuchEntityException $e) {
            return true;
        }*/
    }
}
?>