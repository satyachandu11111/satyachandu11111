<?php
namespace Mirasvit\Feed\Block\Adminhtml\Feed\Edit\Tab\History;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Grid\Extended as ExtendedGrid;
use Magento\Backend\Helper\Data as BackendHelper;
use Magento\Framework\Registry;
use Mirasvit\Feed\Model\ResourceModel\Feed\History\CollectionFactory;

class Grid extends ExtendedGrid
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * {@inheritdoc}
     * @param CollectionFactory $collectionFactory
     * @param Registry          $registry
     * @param Context           $context
     * @param BackendHelper     $backendHelper
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        Registry $registry,
        Context $context,
        BackendHelper $backendHelper
    ) {
        $this->registry = $registry;
        $this->collectionFactory = $collectionFactory;

        parent::__construct($context, $backendHelper);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('history_grid');
        $this->setUseAjax(true);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        $model = $this->registry->registry('current_model');

        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('feed_id', $model->getId())
            ->setOrder('created_at', 'desc')
            ->setOrder('history_id', 'desc');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn('history_created_at', [
            'header' => __('Created At'),
            'index'  => 'created_at',
            'type'   => 'datetime',
        ]);

        $this->addColumn('history_type', [
            'header' => __('Type'),
            'index'  => 'type',
        ]);

        $this->addColumn('history_title', [
            'header' => __('Title'),
            'index'  => 'title',
        ]);

        $this->addColumn('history_message', [
            'header'   => __('Message'),
            'index'    => 'message',
            'renderer' => '\Mirasvit\Feed\Block\Adminhtml\Feed\Edit\Tab\History\Grid\Renderer\Message',
        ]);

        return parent::_prepareColumns();
    }

    /**
     * {@inheritdoc}
     */
    public function getRowUrl($item)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/historyGrid', ['_current' => true]);
    }
}
