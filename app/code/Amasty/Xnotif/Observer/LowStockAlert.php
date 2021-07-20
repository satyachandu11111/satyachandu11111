<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */


namespace Amasty\Xnotif\Observer;

use Amasty\Xnotif\Helper\Config;
use Amasty\Xnotif\Model\ResourceModel\Inventory as InventoryResolver;
use Amasty\Xnotif\Model\Notification\LowStockAlert as NotificationModel;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Shipment;
use Psr\Log\LoggerInterface;

class LowStockAlert implements ObserverInterface
{
    /**
     * @var NotificationModel
     */
    private $notificationModel;

    /**
     * @var InventoryResolver
     */
    private $inventoryResolver;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        InventoryResolver $inventoryResolver,
        NotificationModel $notificationModel,
        LoggerInterface $logger,
        Config $config
    ) {
        $this->notificationModel = $notificationModel;
        $this->inventoryResolver = $inventoryResolver;
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if ($this->config->isLowStockNotifications() || $this->config->isOutStockNotifications()) {
            try {
                /** @var Shipment $shipment */
                $shipment = $observer->getShipment();
                $sourceCode = $this->getSourceCode($shipment);
                $items = $shipment->getAllItems();
                $itemsSku = array_map(function ($productItem) {
                    return $productItem->getSku();
                }, $items);
                $lowStockItemsSku = $this->inventoryResolver->getLowStockItemsSku($itemsSku, $sourceCode);

                foreach ($items as $key => $item) {
                    if ($item->getQty() <= 0
                        || $item->getOrderItem()->isDummy(true)
                        || !in_array($item->getSku(), $lowStockItemsSku)
                    ) {
                        unset($items[$key]);
                    }
                }

                if (!empty($items)) {
                    $this->notificationModel->notify($items, $sourceCode);
                }
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }
    }

    /**
     * @param Shipment $shipment
     *
     * @return string|null
     */
    protected function getSourceCode(Shipment $shipment)
    {
        $sourceCode = null;
        $attributes = $shipment->getExtensionAttributes();

        if (!empty($attributes)
            && method_exists($attributes, 'getSourceCode')
            && $attributes->getSourceCode()
        ) {
            $sourceCode = $attributes->getSourceCode();
        }

        return $sourceCode;
    }
}
