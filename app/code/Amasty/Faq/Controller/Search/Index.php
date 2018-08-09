<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Controller\Search;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Page\Config
     */
    private $pageConfig;

    public function __construct(Context $context, \Magento\Framework\View\Page\Config $pageConfig)
    {
        $this->pageConfig = $pageConfig;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->pageConfig->setRobots('NOINDEX,NOFOLLOW');
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
