<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbySeo
 */


namespace Amasty\ShopbySeo\Plugin;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;

class ToolbarPlugin
{
    /**
     * @var  Registry
     */
    private $registry;

    public function __construct(
        Registry $registry
    ) {
        $this->registry = $registry;
    }

    public function beforeGetPagerUrl(Template $subject, $params = [])
    {
        $seo_parsed = $this->registry->registry('amasty_shopby_seo_parsed_params');
        if (is_array($seo_parsed)) {
			if (is_array($params)) {
            	$params = array_merge($seo_parsed, $params);
			} else {
				$params = array_merge($seo_parsed, [$params]);
			}
        }
        return [$params];
    }
}
