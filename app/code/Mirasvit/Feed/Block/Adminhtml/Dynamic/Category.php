<?php

namespace Mirasvit\Feed\Block\Adminhtml\Dynamic;

use Magento\Backend\Block\Widget\Grid\Container;

class Category extends Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_dynamic_category';
        $this->_blockGroup = 'Mirasvit_Feed';
        $this->_headerText = __('Category Mapping');
        $this->_addButtonLabel = __('Add Category Mapping');

        parent::_construct();
    }
}
