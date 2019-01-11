<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Plugin\Catalog\Product;

use Amasty\Sorting\Model\MethodProvider;
use Magento\Catalog\Block\Product\ListProduct as NativeList;
use Magento\Catalog\Block\Product\ProductList\Toolbar;

class ListProduct
{
    /**
     * @var MethodProvider
     */
    private $methodProvider;

    public function __construct(MethodProvider $methodProvider)
    {
        $this->methodProvider = $methodProvider;
    }

    /**
     * @param NativeList $subject
     * @param array $identities
     *
     * @return array
     */
    public function afterGetIdentities(NativeList $subject, $identities)
    {
        /** @var Toolbar $toolbarBlock */
        $toolbarBlock = $subject->getLayout()->getBlock('product_list_toolbar');
        if ($toolbarBlock
            && in_array(
                $toolbarBlock->getCurrentOrder(),
                array_keys($this->methodProvider->getIndexedMethods())
            )
        ) {
            $identities[] = 'sorted_by_' . $toolbarBlock->getCurrentOrder();
        }

        return $identities;
    }
}
