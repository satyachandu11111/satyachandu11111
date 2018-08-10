<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-email-designer
 * @version   1.1.23
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\EmailDesigner\Service\TemplateEngine\Liquid\Variable;

use Magento\Sales\Model\OrderFactory;
use Magento\Store\Model\StoreManagerInterface;

class Order extends AbstractVariable
{
    protected $supportedTypes = ['Magento\Sales\Model\Order'];

    protected $whitelist = [
        'getUpdatedAt',
        'getStatusLabel',
    ];

    /**
     * Order constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param OrderFactory          $orderFactory
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        OrderFactory $orderFactory
    ) {
        parent::__construct();

        $this->storeManager = $storeManager;
        $this->orderFactory = $orderFactory;
    }

    /**
     * Get order object.
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        if ($this->context->getData('order')) {
            return $this->context->getData('order');
        }

        $order = $this->orderFactory->create();
        if ($this->context->getData('order_id')) {
            $order = $order->load($this->context->getData('order_id'));
            $this->context->setData('order', $order);
        }

        return $order;
    }

    /**
     * Get customer name
     *
     * @return string
     */
    public function getCustomerName()
    {
        $order = $this->context->getData('order');
        if (null === $order || $this->context->getData('customer_name')) {
            return $this->context->getData('customer_name');
        }

        $customerName = '';
        if ($order->getCustomerFirstname()) {
            $customerName = $order->getCustomerName();
        } elseif ($order->getBillingAddress()) {
            $customerName = $order->getBillingAddress()->getFirstname()
                . ' ' . $order->getBillingAddress()->getLastname();
        } elseif ($order->getShippingAddress()) {
            $customerName = $order->getShippingAddress()->getFirstname()
                . ' ' . $order->getShippingAddress()->getLastname();
        }

        return $customerName;
    }
}
