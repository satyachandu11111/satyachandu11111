<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Ui\Component\Form;

use Magento\Ui\Component\Form\Field;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Amasty\Faq\Model\ConfigProvider;

class Canonical extends Field
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * Canonical constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ConfigProvider $configProvider
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ConfigProvider $configProvider,
        $components,
        array $data = []
    ) {
        $this->configProvider = $configProvider;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Remove Canonical field from edit page if it is disabled
     */
    public function prepare()
    {
        if (!$this->configProvider->isCanonicalUrlEnabled()) {
            $config = $this->getData('config');
            $config['template'] = 'ui/form/element/hidden';
            $this->setData('config', $config);
        }

        parent::prepare();
    }
}