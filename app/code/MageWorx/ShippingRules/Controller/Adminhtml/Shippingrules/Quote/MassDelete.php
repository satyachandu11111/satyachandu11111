<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Quote;

use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Base\MassDeleteAbstract as BaseMassDelete;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends BaseMassDelete
{
    /**
     * MassDelete constructor.
     * @param Context $context
     * @param Filter $filter
     * @param \MageWorx\ShippingRules\Model\ResourceModel\Rule\CollectionFactory $collectionFactory
     * @param string $aclResourceName
     */
    public function __construct(
        Context $context,
        Filter $filter,
        \MageWorx\ShippingRules\Model\ResourceModel\Rule\CollectionFactory $collectionFactory,
        $aclResourceName = 'MageWorx_ShippingRules::quote'
    ) {
        parent::__construct($context, $filter, $collectionFactory, $aclResourceName);
    }
}
