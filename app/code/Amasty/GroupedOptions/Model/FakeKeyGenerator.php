<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_GroupedOptions
 */


declare(strict_types=1);

namespace Amasty\GroupedOptions\Model;

class FakeKeyGenerator
{
    const LAST_POSSIBLE_OPTION_ID = (2 << 30) - 1;
    
    public function generate(int $groupId): int
    {
        return self::LAST_POSSIBLE_OPTION_ID - $groupId;
    }
}
