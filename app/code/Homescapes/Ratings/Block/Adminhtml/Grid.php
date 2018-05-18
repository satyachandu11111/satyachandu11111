<?php
namespace Homescapes\Ratings\Block\Adminhtml;

class Grid extends \Magento\Review\Block\Adminhtml\Grid
{

    protected function _prepareColumns()
    {
        $this->addColumnAfter(
            'email',
            [
                'header' => __('Email'),
                'filter_index' => 'rdt.email',
                'filter' => false,
                'index' => 'email',
                'type' => 'text',
                'truncate' => 50,
                'escape' => true,
                'sortable' => 100,
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            ],'detail'
        );

        $this->addColumnAfter('recommend',
            array(
                    'header'=> __('Recommend'),
                    'filter_index' => 'rdt.recommend',
                    'width' => '50px',
                    'align' => 'right',
                    'sortable' => 101,
                    'filter' => false,
                    'truncate' => 50,
                    'escape' => true,
                    'index' => 'recommend',
                    'type'=>'options',
                    'header_css_class' => 'col-name',
                    'column_css_class' => 'col-name',
                    'options' => array('1' => 'Yes', '0' => 'No','' => 'No')
            ),'email');

      
        return parent::_prepareColumns();
    }
}
?>