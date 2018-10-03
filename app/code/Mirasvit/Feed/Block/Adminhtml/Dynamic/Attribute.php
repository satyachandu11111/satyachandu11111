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
 * @package   mirasvit/module-feed
 * @version   1.0.82
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Feed\Block\Adminhtml\Dynamic;

use Magento\Backend\Block\Widget\Grid\Container;

class Attribute extends Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_dynamic_attribute';
        $this->_blockGroup = 'Mirasvit_Feed';
        $this->_headerText = __('Manage Dynamic Attributes');
        $this->_addButtonLabel = __('Add Attribute');

        parent::_construct();
    }
}
