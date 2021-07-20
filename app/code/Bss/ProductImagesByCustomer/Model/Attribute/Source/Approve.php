<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_ProductImagesByCustomer
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\ProductImagesByCustomer\Model\Attribute\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Approve implements OptionSourceInterface
{

    /**
     *
     * Approve const
     *
     */
    const APPROVE = 1;

    /**
     * Disapprove const
     */
    const DISAPPROVE = 0;

    /**
     * Get Available Approve
     *
     * @return array
     */
    protected function getAvailableApprove()
    {
        return [self::APPROVE => __('Approved'), self::DISAPPROVE => __(' Not Approved')];
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->getAvailableApprove();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
