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
namespace Aheadworks\Raf\Plugin\Model\Account;

use Magento\Customer\Model\AccountManagement;
use Aheadworks\Raf\Model\Friend\Quote\GuestSaver;

/**
 * Class AccountManagementPlugin
 * @package Aheadworks\Raf\Plugin\Model\Account
 */
class AccountManagementPlugin
{
    /**
     * @var GuestSaver
     */
    private $guestSaver;

    /**
     * @param GuestSaver $guestSaver
     */
    public function __construct(
        GuestSaver $guestSaver
    ) {
        $this->guestSaver = $guestSaver;
    }

    /**
     * Try to add customer email to guest quote if email is available
     *
     * @param AccountManagement $subject
     * @param bool $result
     * @param string $customerEmail
     * @return bool
     */
    public function afterIsEmailAvailable(AccountManagement $subject, $result, $customerEmail)
    {
        if ($result) {
            $this->guestSaver->addCustomerEmailToGuestQuote($customerEmail);
        }

        return $result;
    }
}
