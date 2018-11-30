<?php

namespace Mirasvit\Feed\Block\Adminhtml;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;

class Import extends Container
{
    /**
     * {@inheritdoc}
     * @param Registry   $registry
     * @param Context    $context
     */
    public function __construct(
        Registry $registry,
        Context $context
    ) {
        $this->registry = $registry;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_blockGroup = 'Mirasvit_Feed';
        $this->_controller = 'adminhtml_import';

        $this->buttonList->remove('save');
        $this->buttonList->remove('reset');

    }

}
