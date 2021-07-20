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

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;
use Aheadworks\Raf\Api\RuleRepositoryInterface;

/**
 * Class Delete
 * @package Aheadworks\Raf\Controller\Adminhtml\Rule
 */
class Delete extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Raf::rules';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var RuleRepositoryInterface
     */
    protected $ruleRepository;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param RuleRepositoryInterface $ruleRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        RuleRepositoryInterface $ruleRepository
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->ruleRepository = $ruleRepository;
    }

    /**
     * Delete rule action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $ruleId = (int)$this->getRequest()->getParam('id');
        if ($ruleId) {
            try {
                $this->ruleRepository->deleteById($ruleId);
                $this->messageManager->addSuccessMessage(__('You deleted the rule.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            }
        }
        $this->messageManager->addErrorMessage(__('Something went wrong while deleting the rule.'));
        return $resultRedirect->setPath('*/*/');
    }
}
