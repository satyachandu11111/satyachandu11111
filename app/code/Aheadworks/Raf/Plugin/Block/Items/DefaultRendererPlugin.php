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
namespace Aheadworks\Raf\Plugin\Block\Items;

use Magento\Bundle\Model\Product\Type as BundleProduct;
use Magento\Sales\Block\Adminhtml\Order\View\Items\Renderer\DefaultRenderer;

/**
 * Class DefaultRendererPlugin
 *
 * @package Aheadworks\Raf\Plugin\Block\Items
 */
class DefaultRendererPlugin
{
    /**
     * Add RAF column after discount
     *
     * @param DefaultRenderer $subject
     * @param \Closure $proceed
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetColumns(
        DefaultRenderer $subject,
        \Closure $proceed
    ) {
        $columns = $proceed();
        foreach ($subject->getOrder()->getAllItems() as $orderItem) {
            if ($orderItem->getProductType() == BundleProduct::TYPE_CODE) {
                return $columns;
            }
        }
        $newColumns = [];
        foreach ($columns as $key => $column) {
            $newColumns[$key] = $column;
            if ($key == 'discont') {
                $newColumns['aw-raf'] = 'col-aw-raf';
            }
        }
        return $newColumns;
    }
}
