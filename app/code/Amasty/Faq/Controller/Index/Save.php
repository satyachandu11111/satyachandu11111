<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Controller\Index;

use Amasty\Faq\Model\ConfigProvider;
use Amasty\Faq\Utils\Email;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\LocalizedException;
use Amasty\Faq\Api\QuestionRepositoryInterface;
use Amasty\Faq\Model\QuestionFactory;
use Amasty\Faq\Model\OptionSource\Question\Status;
use Amasty\Faq\Api\Data\QuestionInterface;

class Save extends Action
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var QuestionRepositoryInterface
     */
    private $repository;

    /**
     * @var QuestionFactory
     */
    private $questionFactory;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var Email
     */
    private $email;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    private $formKeyValidator;

    /**
     * @var \Magento\Framework\Session\Generic
     */
    private $faqSession;

    public function __construct(
        Context $context,
        LoggerInterface $logger,
        StoreManagerInterface $storeManager,
        QuestionRepositoryInterface $repository,
        QuestionFactory $questionFactory,
        ConfigProvider $configProvider,
        Email $email,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\Session\Generic $faqSession
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->repository = $repository;
        $this->context = $context;
        $this->questionFactory = $questionFactory;
        $this->configProvider = $configProvider;
        $this->email = $email;
        $this->formKeyValidator = $formKeyValidator;
        $this->faqSession = $faqSession;
    }

    public function execute()
    {
        try {
            if (!$this->formKeyValidator->validate($this->getRequest())) {
                $this->faqSession->setFormData($this->getRequest()->getParams());
                $this->messageManager->addErrorMessage(__('Form Key is Invalid, please, reload page and try again'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setRefererOrBaseUrl();

                return $resultRedirect;
            }
            // clear session storage
            $this->faqSession->setFormData(false);
            /** @var  \Amasty\Faq\Model\Question $model */
            $model = $this->questionFactory->create();
            $model->setTitle($this->getRequest()->getParam(QuestionInterface::TITLE))
                ->setName($this->getRequest()->getParam(QuestionInterface::NAME))
                ->setStatus(Status::STATUS_PENDING)
                ->setProductIds($this->getRequest()->getParam('product_ids'))
                ->setCategoryIds($this->getRequest()->getParam('category_ids'))
                ->setStoreIds($this->storeManager->getStore()->getId());
            if ($this->getRequest()->getParam('notification')
                && $email = $this->getRequest()->getParam(QuestionInterface::EMAIL)
            ) {
                $model->setEmail($email);
            }
            $validate = $model->validate();
            if ($validate === true) {
                $this->repository->save($model);
                $this->sendAdminNotification($model);
                if ($model->getEmail()) {
                    $this->messageManager
                        ->addSuccessMessage(__('The question was sent. We\'ll notify you about the answer via email.'));
                } else {
                    $this->messageManager->addSuccessMessage(__('The question was sent.'));
                }
            } else {
                $this->faqSession->setFormData($this->getRequest()->getParams());
                if (is_array($validate)) {
                    foreach ($validate as $errorMessage) {
                        $this->messageManager->addErrorMessage($errorMessage);
                    }
                } else {
                    $this->messageManager->addErrorMessage(__('We can\'t post your question right now.'));
                }
            }
        } catch (LocalizedException $e) {
            $this->faqSession->setFormData($this->getRequest()->getParams());
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->faqSession->setFormData($this->getRequest()->getParams());
            $this->messageManager->addErrorMessage(__('We can\'t post your question right now.'));
            $this->logger->critical($e);
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setRefererOrBaseUrl();

        return $resultRedirect;
    }

    /**
     * @param QuestionInterface $question
     */
    private function sendAdminNotification(\Amasty\Faq\Api\Data\QuestionInterface $question)
    {
        if ($this->configProvider->isNotifyAdmin()) {
            $this->email->sendEmail(
                $this->configProvider->notifyAdminEmail(),
                ConfigProvider::ADMIN_NOTIFY_EMAIL_TEMPLATE,
                [
                    'sender_name' => $question->getName(),
                    'sender_email' => $question->getEmail(),
                    'question' => $question->getTitle()
                ],
                \Magento\Framework\App\Area::AREA_ADMINHTML
            );
        }
    }
}
