<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Block\Adminhtml\Question\Edit;

use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Amasty\Faq\Model\ConfigProvider;

class SaveAndSendButton implements ButtonProviderInterface
{
    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        Registry $coreRegistry,
        UrlInterface $urlBuilder,
        ConfigProvider $configProvider
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->urlBuilder = $urlBuilder;
        $this->configProvider = $configProvider;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        if ($this->coreRegistry->registry('canSendCustomerEmail') && !empty($this->configProvider->getNotifySender())) {
            return [
                'label' => __('Save and Send email to Customer'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'Magento_Ui/js/form/button-adapter' => [
                            'actions' => [
                                [
                                    'targetName' => 'amastyfaq_question_form.amastyfaq_question_form',
                                    'actionName' => 'save',
                                    'params' => [
                                        true,
                                        ['save_and_send' => 1],
                                    ]
                                ]
                            ]
                        ]
                    ],
                ],
                'on_click' => '',
                'sort_order' => 60
            ];
        }

        return [];
    }
}
