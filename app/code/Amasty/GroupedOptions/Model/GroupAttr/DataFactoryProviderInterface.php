<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_GroupedOptions
 */


namespace Amasty\GroupedOptions\Model\GroupAttr;

interface DataFactoryProviderInterface
{
    public function create(array $data = []): DataProvider;
}
