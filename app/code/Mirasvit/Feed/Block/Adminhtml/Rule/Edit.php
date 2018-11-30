<?php
namespace Mirasvit\Feed\Block\Adminhtml\Rule;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;

class Edit extends Container
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * {@inheritdoc}
     * @param Registry $registry
     * @param Context  $context
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

        $this->_objectId = 'rule_id';
        $this->_blockGroup = 'Mirasvit_feed';
        $this->_controller = 'adminhtml_rule';

        if ($this->getRequest()->getParam('popup')) {
            $this->buttonList->remove('back');
            $this->buttonList->add('close', [
                'label'   => __('Close Window'),
                'class'   => 'cancel',
                'onclick' => 'window.close()',
                'level'   => -1,
            ]);
        } else {
            $this->buttonList->remove('save');

            $this->getToolbar()->addChild(
                'save-split-button',
                'Magento\Backend\Block\Widget\Button\SplitButton',
                [
                    'id'           => 'save-split-button',
                    'label'        => __('Save'),
                    'class_name'   => 'Magento\Backend\Block\Widget\Button\SplitButton',
                    'button_class' => 'widget-button-update',
                    'options'      => [
                        [
                            'id'             => 'save-button',
                            'label'          => __('Save'),
                            'default'        => true,
                            'data_attribute' => [
                                'mage-init' => [
                                    'button' => [
                                        'event'  => 'saveAndContinueEdit',
                                        'target' => '#edit_form'
                                    ]
                                ]
                            ]
                        ],
                        [
                            'id'             => 'save-continue-button',
                            'label'          => __('Save & Close'),
                            'data_attribute' => [
                                'mage-init' => [
                                    'button' => [
                                        'event'  => 'save',
                                        'target' => '#edit_form'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            );
        }

        $this->buttonList->update('save', 'label', __('Save Filter'));
    }
}
