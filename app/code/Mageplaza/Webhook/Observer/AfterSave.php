<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Webhook
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Webhook\Observer;

use Exception;
use Homescapes\EmailVerificationApi\Model\Data\EmailVerificationFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Webhook\Helper\Data;
use Mageplaza\Webhook\Model\Config\Source\Schedule;
use Mageplaza\Webhook\Model\CronScheduleFactory;
use Mageplaza\Webhook\Model\HookFactory;
use Mageplaza\Webhook\Model\Config\Source\HookType;

/**
 * Class AfterSave
 * @package Mageplaza\Webhook\Observer
 */
abstract class AfterSave implements ObserverInterface
{
    /**
     * @var HookFactory
     */
    protected $hookFactory;

    /**
     * @var CronScheduleFactory
     */
    protected $scheduleFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var string
     */
    protected $hookType = '';

    /**
     * @var string
     */
    protected $hookTypeUpdate = '';

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var EmailVerificationFactory
     */
    protected $_emailVerificationFactory;

    /**
     * AfterSave constructor.
     *
     * @param HookFactory $hookFactory
     * @param CronScheduleFactory $cronScheduleFactory
     * @param ManagerInterface $messageManager
     * @param StoreManagerInterface $storeManager
     * @param Data $helper
     * @param EmailVerificationFactory $emailVerificationFactory
     */
    public function __construct(
        HookFactory $hookFactory,
        CronScheduleFactory $cronScheduleFactory,
        ManagerInterface $messageManager,
        StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customer,
        Data $helper,
        EmailVerificationFactory $emailVerificationFactory
    ) {
        $this->hookFactory     = $hookFactory;
        $this->helper          = $helper;
        $this->scheduleFactory = $cronScheduleFactory;
        $this->messageManager  = $messageManager;
        $this->storeManager    = $storeManager;
        $this->_customer = $customer;
        $this->_emailVerificationFactory = $emailVerificationFactory;
    }

    /**
     * @param Observer $observer
     *
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        $item = $this->getDataObjectFromObserver($observer);

        $schedule = $this->helper->getCronSchedule();
        if ($schedule !== Schedule::DISABLE && $schedule !== null) {
            $hookCollection = $this->hookFactory->create()->getCollection()
                ->addFieldToFilter('hook_type', $this->hookType)
                ->addFieldToFilter('status', 1)
                ->addFieldToFilter('store_ids', [
                    ['finset' => Store::DEFAULT_STORE_ID],
                    ['finset' => $this->helper->getItemStore($item)]
                ])
                ->setOrder('priority', 'ASC');
            if ($hookCollection->getSize() > 0) {
                $isUserVerified = $this->isUserVerified($item);
                if (!$isUserVerified) {
                    return $this;
                }

                $schedule = $this->scheduleFactory->create();
                $data     = [
                    'hook_type' => $this->hookType,
                    'event_id'  => $item->getId(),
                    'status'    => '0'
                ];

                try {
                    $schedule->addData($data);
                    $schedule->save();
                } catch (Exception $exception) {
                    $this->messageManager->addError($exception->getMessage());
                }
            }
        } else {
            $this->helper->send($item, $this->hookType);
        }
    }

    /**
     * @param $observer
     *
     * @throws Exception
     */
    protected function updateObserver($observer)
    {
        //$item     = $observer->getDataObject();
        $item = $this->getDataObjectFromObserver($observer);
        $schedule = $this->helper->getCronSchedule();
        if ($schedule !== Schedule::DISABLE && $schedule !== null) {
            $hookCollection = $this->hookFactory->create()->getCollection()
                ->addFieldToFilter('hook_type', $this->hookType)
                ->addFieldToFilter('status', 1)
                ->addFieldToFilter('store_ids', [
                    ['finset' => Store::DEFAULT_STORE_ID],
                    ['finset' => $this->helper->getItemStore($item)]
                ])
                ->setOrder('priority', 'ASC');
            if ($hookCollection->getSize() > 0) {
                $isUserVerified = $this->isUserVerified($item);
                if (!$isUserVerified) {
                    return $this;
                }

                $schedule = $this->scheduleFactory->create();
                $data     = [
                    'hook_type' => $this->hookTypeUpdate,
                    'event_id'  => $item->getId(),
                    'status'    => '0'
                ];
                try {
                    $schedule->addData($data);
                    $schedule->save();
                } catch (Exception $exception) {
                    $this->messageManager->addError($exception->getMessage());
                }
            }
        } else {
            $this->helper->send($item, $this->hookTypeUpdate);
        }
    }

    /**
     * @param $observer
     * @param $hookType
     *
     * @return bool
     */
    protected function isUserVerified($item)
    {
        if($this->hookType == HookType::NEW_CUSTOMER_VERIFICATION) return $item->getStatus();

        $item = $this->getOrderFromItem($item);

        $email = $item->getCustomerEmail();
        $model = $this->_emailVerificationFactory->create()->getCollection()->addFieldToFilter('email', $email)->addFieldToFilter('status', 1);
        $model->getSelect()->limit(1);
        if ($model->getSize() == 0) {
            return false;
        }
        return true;
    }

    /**
     * @param $item
     * @return
     */
    protected function getOrderFromItem($item)
    {
        return $item;
    }

    /**
     * @param $observer
     * @return
     */
    protected function getDataObjectFromObserver($observer)
    {
        return $observer->getDataObject();
    }
}
