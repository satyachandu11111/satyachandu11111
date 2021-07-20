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
namespace Aheadworks\Raf\Model\Advocate\Email\Processor\VariableProcessor;

use Aheadworks\Raf\Model\Source\Customer\Advocate\Email\BaseAmountVariables;
use Magento\Framework\UrlInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class StoreUrl
 *
 * @package Aheadworks\Raf\Model\Advocate\Email\Processor\VariableProcessor
 */
class StoreUrl implements VariableProcessorInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareVariables($variables)
    {
        /** @var StoreInterface $store */
        $store = $variables[BaseAmountVariables::STORE];

        $variables[BaseAmountVariables::STORE_URL] = $store->getBaseUrl(UrlInterface::URL_TYPE_WEB);

        return $variables;
    }
}
