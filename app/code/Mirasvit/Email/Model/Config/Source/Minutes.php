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
 * @package   mirasvit/module-email
 * @version   2.1.6
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Email\Model\Config\Source;


use Magento\Framework\Option\ArrayInterface;

class Minutes implements ArrayInterface
{
    /**
     * {@inheritDoc}
     */
    public function toOptionArray()
    {
        $options = [];
        for ($i = 0; $i <= 55; $i += 5) {
            $options[] = [
                'value' => $i,
                'label' => $i . ' ' . __($this->pluralize($i, 'minute', 'minutes'))
            ];
        }

        return $options;
    }

    /**
     * @param int    $amount
     * @param string $singular
     * @param string $plural
     *
     * @return string
     */
    private function pluralize($amount, $singular, $plural) {
        if ($amount === 1) {
            return $singular;
        }

        return $plural;
    }
}