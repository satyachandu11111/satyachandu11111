<?php

namespace Homescapes\Career\Model\Config;

class Jobtype implements \Magento\Framework\Option\ArrayInterface
{

	 /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'Intern', 'label' => __('Intern')],
            ['value' => 'Marketing', 'label' => __('Marketing')],
            ['value' => 'Customer Service', 'label' => __('Customer Service')]
        ];
    }
}

