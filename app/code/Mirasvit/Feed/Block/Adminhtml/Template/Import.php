<?php

namespace Mirasvit\Feed\Block\Adminhtml\Template;

use Magento\Backend\Block\Widget\Form\Container;

class Import extends Container
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Mirasvit_feed';
        $this->_mode = 'import';
        $this->_controller = 'adminhtml_template';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Import Templates'));
    }
}
