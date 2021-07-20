<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_GroupedOptions
 */


declare(strict_types=1);

namespace Amasty\GroupedOptions\Ui\Button\Group;

class DeleteButton extends GenericButton
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];

        if ($groupId = $this->getGroupId()) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => sprintf(
                    'deleteConfirm("%s", "%s")',
                    __('Are you sure you want to delete this group?'),
                    $this->getUrl('*/*/delete', ['group_id' => $groupId])
                ),
                'sort_order' => 20,
            ];
        }

        return $data;
    }
}
