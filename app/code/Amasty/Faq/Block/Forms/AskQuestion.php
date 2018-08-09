<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Block\Forms;

use Amasty\Faq\Model\ConfigProvider;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;

class AskQuestion extends \Amasty\Faq\Block\AbstractBlock
{
    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * AskQuestion constructor.
     *
     * @param Template\Context $context
     * @param Registry         $coreRegistry
     * @param ConfigProvider   $configProvider
     * @param array            $data
     */
    public function __construct(
        Template\Context $context,
        Registry $coreRegistry,
        ConfigProvider $configProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->coreRegistry = $coreRegistry;
        $this->configProvider = $configProvider;
    }

    /**
     * Checks if user could receive email with answer
     *
     * @return bool
     */
    public function isNotifyUser()
    {
        return $this->configProvider->isNotifyUser();
    }

    /**
     * Get url for save on front
     *
     * @return string
     */
    public function getUrlAction()
    {
        return $this->_urlBuilder->getUrl('faq/index/save');
    }

    /**
     * @return array|null
     */
    public function getAdditionalField()
    {
        if ($product = $this->coreRegistry->registry('current_product')) {
            return ['field' => 'product_ids', 'value' => (int)$product->getId()];
        } elseif ($categoryId = (int)$this->coreRegistry->registry('current_faq_category_id')) {
            return ['field' => 'category_ids', 'value' => $categoryId];
        }

        return null;
    }

    /**
     * Check if GDPR consent enabled
     *
     * @return bool
     */
    public function isGDPREnabled()
    {
        return $this->configProvider->isGDPREnabled();
    }

    /**
     * Get text for GDPR
     *
     * @return string
     */
    public function getGDPRText()
    {
        return $this->configProvider->getGDPRText();
    }
}
