<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


declare(strict_types=1);

namespace Amasty\Shopby\Model;

class ConfigProvider extends \Amasty\Base\Model\ConfigProviderAbstract
{
    const TOOLTIP_IMAGE = 'tooltips/image';

    /**
     * @var string
     */
    protected $pathPrefix = 'amshopby/';

    public function getTooltipSrc(): string
    {
        return (string)$this->getValue(self::TOOLTIP_IMAGE);
    }
}
