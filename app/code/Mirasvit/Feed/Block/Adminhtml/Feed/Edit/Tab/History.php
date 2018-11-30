<?php

namespace Mirasvit\Feed\Block\Adminhtml\Feed\Edit\Tab;

use Magento\Backend\Block\Widget\Container;

class History extends Container
{
    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        return $this->getLayout()->createBlock('\Mirasvit\Feed\Block\Adminhtml\Feed\Edit\Tab\History\Grid')->toHtml();
    }
}
