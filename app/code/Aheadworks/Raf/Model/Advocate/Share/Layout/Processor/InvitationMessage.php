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
namespace Aheadworks\Raf\Model\Advocate\Share\Layout\Processor;

use Magento\Framework\Stdlib\ArrayManager;
use Aheadworks\Raf\Model\Advocate\Share\MessageConfig;

/**
 * Class InvitationMessage
 * @package Aheadworks\Raf\Model\Advocate\Share\Layout\Processor
 */
class InvitationMessage
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var MessageConfig
     */
    private $messageConfig;

    /**
     * @param ArrayManager $arrayManager
     * @param MessageConfig $messageConfig
     */
    public function __construct(
        ArrayManager $arrayManager,
        MessageConfig $messageConfig
    ) {
        $this->arrayManager = $arrayManager;
        $this->messageConfig = $messageConfig;
    }

    /**
     * Process js layout of block
     *
     * @param array $jsLayout
     * @return array
     */
    public function process($jsLayout)
    {
        $optionsProviderPath = 'components/awRafMessageConfigProvider';
        $jsLayout = $this->arrayManager->merge(
            $optionsProviderPath,
            $jsLayout,
            [
                'data' => [
                    'messageConfig' => $this->messageConfig->getConfigData()
                ]
            ]
        );

        return $jsLayout;
    }
}
