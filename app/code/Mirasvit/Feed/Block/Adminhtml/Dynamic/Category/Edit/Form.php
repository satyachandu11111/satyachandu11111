<?php
namespace Mirasvit\Feed\Block\Adminhtml\Dynamic\Category\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form as WidgetForm;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Mirasvit\Feed\Block\Adminhtml\Dynamic\Category\Edit\Renderer\Mapping;

class Form extends WidgetForm
{
    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Mapping
     */
    protected $mappingElement;

    /**
     * @param Mapping     $conditionsElement
     * @param FormFactory $formFactory
     * @param Registry    $registry
     * @param Context     $context
     */
    public function __construct(
        Mapping $conditionsElement,
        FormFactory $formFactory,
        Registry $registry,
        Context $context
    ) {
        $this->mappingElement = $conditionsElement;
        $this->formFactory = $formFactory;
        $this->registry = $registry;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $form = $this->formFactory->create()->setData([
            'id'      => 'edit_form',
            'action'  => $this->getUrl('*/*/save', ['id' => $this->getRequest()->getParam('id')]),
            'method'  => 'post',
            'enctype' => 'multipart/form-data',
        ]);

        $model = $this->getCategory();

        $fieldset = $form->addFieldset('dynamic_category_form', ['legend' => __('General Information')]);

        if ($model->getId()) {
            $fieldset->addField('mapping_id', 'hidden', [
                'name'  => 'mapping_id',
                'value' => $model->getId(),
            ]);
        }

        $fieldset->addField('name', 'text', [
            'label'    => __('Name'),
            'required' => true,
            'name'     => 'name',
            'value'    => $model->getName(),
        ]);

        $fieldset->addElement($this->mappingElement);

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Current Category
     *
     * @return \Mirasvit\Feed\Model\Dynamic\Category
     */
    public function getCategory()
    {
        return $this->registry->registry('current_model');
    }
}
