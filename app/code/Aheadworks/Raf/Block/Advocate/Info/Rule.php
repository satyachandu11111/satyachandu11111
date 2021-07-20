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
namespace Aheadworks\Raf\Block\Advocate\Info;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\Raf\Model\Advocate\Account\Rule\Viewer as RuleViewer;

/**
 * Class Info
 *
 * @package Aheadworks\Raf\Block\Advocate
 */
class Rule extends Template
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'Aheadworks_Raf::advocate/info/rule.phtml';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var RuleViewer
     */
    private $ruleViewer;

    /**
     * @param Context $context
     * @param RuleViewer $ruleViewer
     * @param array $data
     */
    public function __construct(
        Context $context,
        RuleViewer $ruleViewer,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->storeManager = $context->getStoreManager();
        $this->ruleViewer = $ruleViewer;
    }

    /**
     * Get advocate off
     *
     * @return string
     */
    public function getAdvocateOff()
    {
        return $this->ruleViewer->getAdvocateOffFormatted($this->getStoreId());
    }

    /**
     * Get friend off
     *
     * @return string
     */
    public function getFriendOff()
    {
        return $this->ruleViewer->getFriendOffFormatted($this->getStoreId());
    }

    /**
     * Is registration required
     *
     * @return string
     */
    public function checkIfRegistrationIsRequired()
    {
        return $this->ruleViewer->checkIfRegistrationIsRequired($this->getStoreId());
    }

    /**
     * Get current store ID
     *
     * @return int
     */
    private function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }
}
