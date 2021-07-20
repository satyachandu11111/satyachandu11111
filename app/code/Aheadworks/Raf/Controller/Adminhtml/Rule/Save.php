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
use Aheadworks\Raf\Api\Data\RuleInterface;
use Aheadworks\Raf\Api\Data\RuleInterfaceFactory;
use Aheadworks\Raf\Ui\DataProvider\Rule\FormDataProvider as RuleFormDataProvider;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class Save
 *
 * @package Aheadworks\Raf\Controller\Adminhtml\Rule
 */
class Save extends Action
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
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var RuleInterfaceFactory
     */
    private $ruleInterfaceFactory;

    /**
     * @var PostDataProcessor
     */
    private $postDataProcessor;

    /**
     * @param Context $context
     * @param RuleRepositoryInterface $ruleRepository
     * @param RuleInterfaceFactory $ruleInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataPersistorInterface $dataPersistor
     * @param PostDataProcessor $postDataProcessor
     */
    public function __construct(
        Context $context,
        RuleRepositoryInterface $ruleRepository,
        RuleInterfaceFactory $ruleInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        DataPersistorInterface $dataPersistor,
        PostDataProcessor $postDataProcessor
    ) {
        parent::__construct($context);
        $this->ruleRepository = $ruleRepository;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPersistor = $dataPersistor;
        $this->ruleInterfaceFactory = $ruleInterfaceFactory;
        $this->postDataProcessor = $postDataProcessor;
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPostValue()) {
            try {
                $data = $this->postDataProcessor->prepareEntityData($data);

                $rule = $this->performSave($data);

                $this->dataPersistor->clear(RuleFormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY);
                $this->messageManager->addSuccessMessage(__('You saved the rule.'));

                if ($this->getRequest()->getParam('back') == 'edit') {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $rule->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the rule.')
                );
            }
            $this->dataPersistor->set(RuleFormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY, $data);
            $id = isset($data['id']) ? $data['id'] : false;
            if ($id) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $id, '_current' => true]);
            }
            return $resultRedirect->setPath('*/*/new', ['_current' => true]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Perform save
     *
     * @param array $data
     * @return RuleInterface
     * @throws LocalizedException | \Exception
     */
    private function performSave($data)
    {
        $id = isset($data['id']) ? $data['id'] : false;
        $ruleObject = $id
            ? $this->ruleRepository->get($id)
            : $this->ruleInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $ruleObject,
            $data,
            RuleInterface::class
        );

        return $this->ruleRepository->save($ruleObject);
    }
}
