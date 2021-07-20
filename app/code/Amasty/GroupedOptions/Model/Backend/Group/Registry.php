<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_GroupedOptions
 */


declare(strict_types=1);

namespace Amasty\GroupedOptions\Model\Backend\Group;

use Amasty\GroupedOptions\Api\Data\GroupAttrInterface;

class Registry
{
    /**
     * @var GroupAttrInterface|null
     */
    private $group;

    public function getGroup(): ?GroupAttrInterface
    {
        return $this->group;
    }

    public function setGroup(GroupAttrInterface $group): void
    {
        $this->group = $group;
    }
}
