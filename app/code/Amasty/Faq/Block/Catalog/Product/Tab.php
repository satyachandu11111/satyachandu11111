<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Block\Catalog\Product;

use Amasty\Faq\Model\ConfigProvider;
use Magento\Framework\View\Element\Template;

class Tab extends \Amasty\Faq\Block\AbstractBlock implements \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * Tab constructor.
     *
     * @param Template\Context                 $context
     * @param ConfigProvider                   $configProvider
     * @param array                            $data
     */
    public function __construct(
        Template\Context $context,
        ConfigProvider $configProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configProvider = $configProvider;
    }

    /**
     * @return int
     */
    public function getShortAnswerBehavior()
    {
        return (int)$this->configProvider->getProductPageShortAnswerBehavior();
    }

    /**
     * @return bool
     */
    public function showAskQuestionForm()
    {
        return $this->configProvider->isShowAskQuestionOnProductPage();
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $count = $this->getChildBlock('amasty_faq_questions')->getCollection()->count();

        $this->setTitle(__('Product Questions') . (($count) ? ' (' . $count .')' : ''));

        return parent::_toHtml();
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Amasty\Faq\Model\ResourceModel\Question\Collection::CACHE_TAG];
    }
}
