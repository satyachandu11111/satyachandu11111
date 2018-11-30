<?php

namespace Mirasvit\Feed\Block\Adminhtml\Rule\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Tabs as WidgetTabs;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;

class Tabs extends WidgetTabs
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * {@inheritdoc}
     * @param Registry         $registry
     * @param Context          $context
     * @param EncoderInterface $jsonEncoder
     * @param Session          $authSession
     */
    public function __construct(
        Registry $registry,
        Context $context,
        EncoderInterface $jsonEncoder,
        Session $authSession
    ) {
        $this->registry = $registry;

        parent::__construct($context, $jsonEncoder, $authSession);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Filter Information'));
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeToHtml()
    {
        $this->addTab('general_section', [
            'label'   => __('Filter Information'),
            'title'   => __('Filter Information'),
            'content' => $this->getLayout()
                ->createBlock('\Mirasvit\Feed\Block\Adminhtml\Rule\Edit\Tab\General')->toHtml(),
        ]);

        if ($this->registry->registry('current_model')->getId()) {
            $this->addTab('filter_section', [
                'label'   => __('Rules'),
                'title'   => __('Rules'),
                'content' => $this->getLayout()
                    ->createBlock('\Mirasvit\Feed\Block\Adminhtml\Rule\Edit\Tab\Rule')->toHtml(),
            ]);
        }

        return parent::_beforeToHtml();
    }
}
