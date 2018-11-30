<?php
namespace Mirasvit\Feed\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class Rule extends Container
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_rule';
        $this->_blockGroup = 'Mirasvit_Feed';
        $this->_headerText = __('Manage Filters');
        $this->_addButtonLabel = __('Add Filter');

        parent::_construct();
    }
}
