<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_GroupedOptions
 */


namespace Amasty\GroupedOptions\Api;

interface GroupRepositoryInterface
{
    const TABLE = 'amasty_grouped_options_group';
    const TABLE_OPTIONS = 'amasty_grouped_options_group_option';
    const TABLE_VALUES = 'amasty_grouped_options_group_value';

    /**
     * @param $groupCode
     * @return false or array
     */
    public function getGroupOptionsIds($groupCode);
}
