<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_GroupedOptions
 */


declare(strict_types=1);

namespace Amasty\GroupedOptions\Ui\Button\Group;

class SaveAndContinueButton extends GenericButton
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save and Continue Edit'),
            'class' => 'save',
            'on_click'  => '',
            'sort_order' => 40,
        ];
    }
}
