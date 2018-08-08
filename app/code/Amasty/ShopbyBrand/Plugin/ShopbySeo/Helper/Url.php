<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBrand
 */


namespace Amasty\ShopbyBrand\Plugin\ShopbySeo\Helper;

use Amasty\ShopbyBrand\Helper\Content as ContentHelper;

class Url
{
    /**
     * @var ContentHelper
     */
    private $contentHelper;

    public function __construct(
        ContentHelper $contentHelper
    ) {
        $this->contentHelper = $contentHelper;
    }

    /**
     * @param $subject
     * @param $result
     * @return string
     */
    public function afterGetCategoryRouteUrl($subject, $result)
    {
        return $this->contentHelper->getCurrentBranding() == null ? $result : '';
    }

    /**
     * @param $subject
     * @param $result
     * @return string
     */
    public function afterIsSeoUrlEnabled($subject, $result)
    {
        return $result ?: $this->contentHelper->getCurrentBranding() != null;
    }
}
