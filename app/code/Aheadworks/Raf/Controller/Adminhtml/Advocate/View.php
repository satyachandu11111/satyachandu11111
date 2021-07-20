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
namespace Aheadworks\Raf\Controller\Adminhtml\Advocate;

use Aheadworks\Raf\Api\AdvocateSummaryRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;

/**
 * Class View
 * @package Aheadworks\Raf\Controller\Adminhtml\View
 */
class View extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Raf::advocates';

    /**
     * @var AdvocateSummaryRepositoryInterface
     */
    private $advocateSummaryRepository;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * View constructor.
     * @param Context $context
     * @param AdvocateSummaryRepositoryInterface $advocateSummaryRepository
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        AdvocateSummaryRepositoryInterface $advocateSummaryRepository,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->advocateSummaryRepository = $advocateSummaryRepository;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * View action
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $advocateSummaryItemId = (int) $this->getRequest()->getParam('id');
        if ($advocateSummaryItemId) {
            try {
                $advocateSummaryItem = $this->advocateSummaryRepository->get($advocateSummaryItemId);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('This advocate doesn\'t exist.')
                );
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/*/');
                return $resultRedirect;
            }
        }
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Aheadworks_Raf::advocates');
        $resultPage->getConfig()->getTitle()->prepend(__('Advocate Information'));
        return $resultPage;
    }
}
