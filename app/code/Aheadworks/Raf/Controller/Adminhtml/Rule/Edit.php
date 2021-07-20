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
namespace Aheadworks\Raf\Controller\Adminhtml\Rule;

use Aheadworks\Raf\Api\RuleRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;

/**
 * Class Edit
 *
 * @package Aheadworks\Raf\Controller\Adminhtml\Rule
 */
class Edit extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Raf::rules';

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param RuleRepositoryInterface $ruleRepository;
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        ruleRepositoryInterface $ruleRepository,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->ruleRepository = $ruleRepository;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Edit action
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $ruleId = (int) $this->getRequest()->getParam('id');
        if ($ruleId) {
            try {
                $rule = $this->ruleRepository->get($ruleId);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('This rule no longer exists.')
                );
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/*/');
                return $resultRedirect;
            }
        }
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage
            ->setActiveMenu('Aheadworks_Raf::rules')
            ->getConfig()->getTitle()->prepend(
                $ruleId
                    ? __('Edit "%1" rule', $rule->getName())
                    : __('New Rule')
            );
        return $resultPage;
    }
}
