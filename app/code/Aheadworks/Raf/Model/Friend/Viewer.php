<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Raf
 * @version    1.1.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Raf\Model\Friend;

use Aheadworks\Raf\Model\Config;
use Aheadworks\Raf\Model\Renderer\Cms\Block as CmsBlockRenderer;
use Aheadworks\Raf\Model\Source\Cms\Block as CmsBlockSource;

/**
 * Class Viewer
 *
 * @package Aheadworks\Raf\Model\Friend
 */
class Viewer
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var CmsBlockRenderer
     */
    private $cmsBlockRenderer;

    /**
     * @param Config $config
     * @param CmsBlockRenderer $cmsBlockRenderer
     */
    public function __construct(
        Config $config,
        CmsBlockRenderer $cmsBlockRenderer
    ) {
        $this->config = $config;
        $this->cmsBlockRenderer = $cmsBlockRenderer;
    }

    /**
     * Retrieve static block html for welcome popup
     *
     * @param int $storeId
     * @return string|null
     */
    public function getStaticBlockHtmlForWelcomePopup($storeId)
    {
        $blockId = $this->config->getStaticBlockIdForWelcomePopup($storeId);
        if ($blockId && $blockId != CmsBlockSource::DONT_DISPLAY) {
            return $this->cmsBlockRenderer->render($blockId, $storeId);
        }

        return null;
    }
}
