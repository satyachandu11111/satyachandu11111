<?php
namespace Mirasvit\Feed\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class Feed extends Container
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_feed';
        $this->_blockGroup = 'Mirasvit_Feed';
        $this->_headerText = __('Manage Feeds');
        $this->_addButtonLabel = __('Add Feed');

        parent::_construct();
    }
}
