<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */


namespace Amasty\Xnotif\Block\Adminhtml\Catalog\Product\Edit\Tab\Alerts;

use Amasty\Xnotif\Block\Adminhtml\Catalog\Product\Edit\Tab\Alerts\Renderer\Email;
use Amasty\Xnotif\Block\Adminhtml\Catalog\Product\Edit\Tab\Alerts\Renderer\FirstName;
use Amasty\Xnotif\Block\Adminhtml\Catalog\Product\Edit\Tab\Alerts\Renderer\LastName;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\Framework\Module\Manager;
use Magento\Framework\ObjectManagerInterface;
use Magento\ProductAlert\Model\StockFactory;
use Magento\Store\Api\Data\WebsiteInterface;

class Stock extends \Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Alerts\Stock
{
    /**
     * @var \Magento\InventorySalesApi\Api\StockResolverInterface
     */
    private $stockResolver;

    public function __construct(
        Context $context,
        Data $backendHelper,
        StockFactory $stockFactory,
        Manager $moduleManager,
        ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $stockFactory, $moduleManager, $data);

        if ($this->isInventoryProductAlertsEnabled()) {
            //It is created using ObjectManager since in Magento CE versions these class does not exist
            $this->stockResolver = $objectManager->get(\Magento\InventorySalesApi\Api\StockResolverInterface::class);
        }
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn(
            'firstname',
            [
                'header' => __('First Name'),
                'index' => 'firstname',
                'renderer' => FirstName::class,
            ]
        );

        $this->addColumn(
            'lastname',
            [
                'header' => __('Last Name'),
                'index' => 'lastname',
                'renderer' => LastName::class,
            ]
        );

        $this->addColumn(
            'email',
            [
                'header' => __('Email'),
                'index' => 'email',
                'renderer' => Email::class,
            ]
        );

        $this->addColumn(
            'add_date',
            [
                'header' => __('Date Subscribed'),
                'index' => 'add_date',
                'type' => 'date'
            ]
        );

        $this->addColumn(
            'send_date',
            [
                'header' => __('Last Notification'),
                'index' => 'send_date',
                'type' => 'date'
            ]
        );

        $this->addColumn(
            'send_count',
            [
                'header' => __('Send Count'),
                'index' => 'send_count',
            ]
        );

        $this->addColumn(
            'action',
            [
                'header' => __('Action'),
                'width' => '50px',
                'type' => 'action',
                'getter' => 'getAlertStockId',
                'actions' => [
                    [
                        'caption' => __('Remove'),
                        'url' => [
                            'base' => 'xnotif/stock/delete',
                            'params' => [
                                'store' => $this->getRequest()->getParam(
                                    'store'
                                )
                            ]
                        ],
                        'field' => 'alert_stock_id'
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'alert_stock_id',
            ]
        );

        if ($this->isInventoryProductAlertsEnabled()) {
            $this->addColumn(
                'website_id',
                [
                    'header' => __('Website'),
                    'index' => 'website_id',
                    'type' => 'options',
                    'options' => $this->getWebsitesOptions(),
                ]
            );
            $this->addColumn('stock_name', ['header' => __('Stock'), 'index' => 'stock_name']);
        }

        return $this;
    }

    /**
     * @return array
     */
    protected function getWebsitesOptions()
    {
        $options = [];
        foreach ($this->_storeManager->getWebsites() as $website) {
            $options[$website->getId()] = $website->getName();
        }

        return $options;
    }

    /**
     * @return \Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Alerts\Stock
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _afterLoadCollection()
    {
        parent::_afterLoadCollection();

        if ($this->isInventoryProductAlertsEnabled()) {
            foreach ($this->getCollection()->getItems() as $item) {
                /** @var WebsiteInterface $website */
                $website = $this->_storeManager->getWebsite($item->getWebsiteId());
                $stock = $this->stockResolver->execute(
                    \Magento\InventorySalesApi\Api\Data\SalesChannelInterface::TYPE_WEBSITE,
                    $website->getCode()
                );
                $item->setStockName($stock->getName());
            }
        }

        return $this;
    }

    /**
     * @return bool
     */
    private function isInventoryProductAlertsEnabled(): bool
    {
        return $this->moduleManager->isEnabled('Magento_InventoryProductAlert');
    }
}
