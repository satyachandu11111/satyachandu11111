<?php

namespace Mirasvit\Feed\Block\Adminhtml\Dynamic;

use Magento\Backend\Block\Widget\Grid\Container;

class Variable extends Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_dynamic_variable';
        $this->_blockGroup = 'Mirasvit_Feed';
        $this->_headerText = __('Manage Dynamic Variables');
        $this->_addButtonLabel = __('Add Variable');

        parent::_construct();
    }
}
