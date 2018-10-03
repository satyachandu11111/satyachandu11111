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
 * @version   2.1.11
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Email\Ui\Campaign\Listing\Column;


class Click extends AbstractColumn
{
    /**
     * {@inheritDoc}
     */
    protected function prepareItem(array $item)
    {
        $value = $item[$this->getName()] ? $item[$this->getName()] : '―';

        return '<span class="fa fa-mouse-pointer"> ' . $value . '</span>';
    }
}
