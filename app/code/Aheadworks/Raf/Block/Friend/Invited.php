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
namespace Aheadworks\Raf\Block\Friend;

use Aheadworks\Raf\Model\Friend\Viewer as FriendViewer;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Invited
 *
 * @package Aheadworks\Raf\Block\Friend
 */
class Invited extends Template
{
    /**
     * @var FriendViewer
     */
    private $friendViewer;

    /**
     * @param Context $context
     * @param FriendViewer $friendViewer
     * @param array $data
     */
    public function __construct(
        Context $context,
        FriendViewer $friendViewer,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->friendViewer = $friendViewer;
    }

    /**
     * Retrieve static block html for welcome popup
     *
     * @return string
     */
    public function getStaticBlockHtmlForWelcomePopup()
    {
        $storeId = $this->_storeManager->getStore()->getId();

        return $this->friendViewer->getStaticBlockHtmlForWelcomePopup($storeId);
    }
}
