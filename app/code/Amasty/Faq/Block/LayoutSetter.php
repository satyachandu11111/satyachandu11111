<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Block;

use Amasty\Faq\Model\ConfigProvider;
use Magento\Framework\View\Element\Template;

class LayoutSetter extends Template
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * LayoutSetter constructor.
     *
     * @param ConfigProvider   $configProvider
     * @param Template\Context $context
     * @param array            $data
     */
    public function __construct(
        ConfigProvider $configProvider,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configProvider = $configProvider;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->setPageLayout($this->configProvider->getPageLayout());
        return parent::_prepareLayout();
    }
}
