<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Controller\Adminhtml\Question;

use Psr\Log\LoggerInterface;
use Magento\Backend\App\Action\Context;
use Amasty\Faq\Api\QuestionRepositoryInterface;

class Delete extends \Amasty\Faq\Controller\Adminhtml\AbstractQuestion
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var QuestionRepositoryInterface
     */
    private $repository;

    /**
     * Delete action constructor.
     *
     * @param Context $context
     * @param LoggerInterface $logger
     * @param QuestionRepositoryInterface $repository
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        QuestionRepositoryInterface $repository
    ) {
        parent::__construct($context);
        $this->repository = $repository;
        $this->logger = $logger;
    }

    /**
     * Delete action
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->repository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted the question.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t delete item right now. Please review the log and try again.')
                );
                $this->logger->critical($e);
            }
        }
        $this->_redirect('*/*/');
    }
}
