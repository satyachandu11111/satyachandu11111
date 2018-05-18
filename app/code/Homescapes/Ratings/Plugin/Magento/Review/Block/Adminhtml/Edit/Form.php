<?php

namespace Homescapes\Ratings\Plugin\Magento\Review\Block\Adminhtml\Edit;

class Form extends \Magento\Review\Block\Adminhtml\Edit\Form
{
    public function beforeSetForm(\Magento\Review\Block\Adminhtml\Edit\Form $object, $form) {

        $review = $object->_coreRegistry->registry('review_data');

        $fieldset = $form->addFieldset(
            'review_details_extra',
            ['legend' => __('Review Details Extra Data'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'email',
            'text',
            ['label' => __('E-mail'), 'required' => true, 'name' => 'email']
        );

         $fieldset->addField(
            'recommend',
            'radios',
            [
                'label' => __('Recommend'),
                'title' => __('Recommend'),
                'name' => 'recommend',
                'required' => TRUE,
                'values' => array(
                            array('value'=>'1','label'=>'Yes'),
                            array('value'=>'0','label'=>'No')
                        ),
            ]
        );


        $form->setValues($review->getData());

        return [$form];
    }
}