<?php

namespace Mirasvit\Feed\Block\Adminhtml\Import\Edit;

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
        $this->setDestElementId('import_form');
        $this->setTitle(__('Import/Export Information'));
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeToHtml()
    {
        $this->addTab('import_tab', [
            'label'   => __('Import Data'),
            'title'   => __('Import Data'),
            'content' => $this->getLayout()
                ->createBlock('\Mirasvit\Feed\Block\Adminhtml\Import\Edit\Tab\Import')->toHtml(),
        ]);

        $this->addTab('export_tab', [
            'label'   => __('Export Data'),
            'title'   => __('Export Data'),
            'content' => $this->getLayout()
                ->createBlock('\Mirasvit\Feed\Block\Adminhtml\Import\Edit\Tab\Export')->toHtml(),
        ]);

        return parent::_beforeToHtml();
    }
}
