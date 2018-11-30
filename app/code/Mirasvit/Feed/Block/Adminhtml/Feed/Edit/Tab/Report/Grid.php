<?php
namespace Mirasvit\Feed\Block\Adminhtml\Feed\Edit\Tab\Report;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Grid\Extended as ExtendedGrid;
use Magento\Backend\Helper\Data as BackendHelper;
use Magento\Framework\Registry;
use Mirasvit\Feed\Api\Data\ValidationInterface;
use Mirasvit\Feed\Api\Repository\ValidationRepositoryInterface;

class Grid extends ExtendedGrid
{
    /**
     * @var Registry
     */
    protected $registry;
    /**
     * @var ValidationRepositoryInterface
     */
    private $validationRepository;

    public function __construct(
        ValidationRepositoryInterface $validationRepository,
        Registry $registry,
        Context $context,
        BackendHelper $backendHelper
    ) {
        $this->validationRepository = $validationRepository;
        $this->registry = $registry;

        parent::__construct($context, $backendHelper);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('report_grid');
        $this->setUseAjax(true);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        $model = $this->registry->registry('current_model');

        $collection = $this->validationRepository->getCollection()
            ->addFieldToFilter(ValidationInterface::FEED_ID, $model->getId());

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn(ValidationInterface::VALIDATOR, [
            'header' => __('Message'),
            'index'  => ValidationInterface::VALIDATOR,
            'frame_callback' => [$this, 'renderValidatorMessage'],
        ]);

        $this->addColumn(ValidationInterface::LINE_NUM, [
            'header' => __('Line #'),
            'index'  => ValidationInterface::LINE_NUM,
            'type'   => 'number',
        ]);

        $this->addColumn(ValidationInterface::ATTRIBUTE, [
            'header' => __('Attribute'),
            'index'  => ValidationInterface::ATTRIBUTE,
        ]);

        $this->addColumn(ValidationInterface::VALUE, [
            'header'   => __('Value'),
            'index'    => ValidationInterface::VALUE,
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
        return $this->getUrl('*/*/reportGrid', ['_current' => true]);
    }

    /**
     * Render validator error message.
     *
     * @param $renderedValue
     * @param $item
     * @param $column
     * @param $isExport
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function renderValidatorMessage($renderedValue, $item, $column, $isExport)
    {
        if ($this->validationRepository->getValidatorByCode($renderedValue)) {
            return $this->validationRepository->getValidatorByCode($renderedValue)->getMessage(true);
        }

        return $renderedValue;
    }
}
