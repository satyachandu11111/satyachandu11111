<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Model\Import\Question;

use Amasty\Faq\Api\ImportExport\QuestionInterface;
use Amasty\Faq\Model\Import\AbstractImport;
use Amasty\Faq\Model\Import\ImportValidatorInterface;
use Amasty\Faq\Model\ResourceModel\Question;
use Magento\ImportExport\Model\Import as ModelImport;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingError;
use Magento\Store\Model\StoreManagerInterface;

class Validator implements ImportValidatorInterface
{

    const ERROR_COL_EMPTY_QUESTION = 'questionIsEmpty';

    const ERROR_COL_EMPTY_ANSWER = 'answerIsEmpty';

    const ERROR_COL_URL_KEY_FORMAT = 'formatUrlKey';

    const ERROR_DUPLICATE_URL_KEY = 'duplicateUrlKey';

    const ERROR_UNKNOWN_STORE_CODE = 'unknownStoreCode';

    const ERROR_EMPTY_QUESTION_ID = 'emptyQuestionId';

    const ERROR_COL_URL_KEY_EMPTY = 'emptyUrlKey';

    /**
     * @var array
     */
    private $messageTemplates = [
        self::ERROR_COL_EMPTY_QUESTION => '<b>Error!</b> Question Field Is Empty',
        self::ERROR_COL_EMPTY_ANSWER => '<b>Error!</b> Answer Field Is Empty',
        self::ERROR_COL_URL_KEY_FORMAT => '<b>Error!</b> Wrong Url Key format',
        self::ERROR_DUPLICATE_URL_KEY=> '<b>Error!</b> duplicate Url Key',
        self::ERROR_UNKNOWN_STORE_CODE => '<b>Error!</b> Unknown Store Code',
        self::ERROR_EMPTY_QUESTION_ID => 'Warning! Empty Question Id',
        self::ERROR_COL_URL_KEY_EMPTY => '<b>Error!</b> Url key is empty'
    ];

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var array
     */
    private $stores = [];

    /**
     * @var Question
     */
    private $question;

    /**
     * @var string
     */
    private $behavior = '';

    /**
     * @var array
     */
    private $errors = [];

    /**
     * Validator constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param Question              $question
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Question $question
    ) {
        $this->storeManager = $storeManager;
        $stores = $this->storeManager->getStores(true);
        foreach ($stores as $store) {
            $this->stores[$store->getCode()] = $store->getId();
        }
        $this->question = $question;
    }

    /**
     * @param array  $rowData
     * @param string $behavior
     *
     * @return array|bool
     */
    public function validateRow(array $rowData, $behavior)
    {
        $this->errors = [];
        $this->behavior = $behavior;

        if ($behavior === ModelImport::BEHAVIOR_DELETE) {
            if (empty($rowData[QuestionInterface::QUESTION_ID])) {
                $this->errors[self::ERROR_EMPTY_QUESTION_ID] = ProcessingError::ERROR_LEVEL_NOT_CRITICAL;
            }
        } else {
            if (empty($rowData[QuestionInterface::QUESTION])) {
                $this->errors[self::ERROR_COL_EMPTY_QUESTION] = ProcessingError::ERROR_LEVEL_CRITICAL;
            }

            $stores = $this->validateStores($rowData);

            if (!isset($this->errors[self::ERROR_UNKNOWN_STORE_CODE]) && !empty($stores)) {
                $this->validateUrlKey($rowData, $stores);
            }
        }

        if ($this->errors) {
            return $this->errors;
        }

        return true;
    }

    /**
     * @param $rowData
     *
     * @return array
     */
    private function validateStores($rowData)
    {
        $stores = [];

        if (empty($rowData[QuestionInterface::STORE_CODES])) {
            $stores[] = $this->storeManager->getDefaultStoreView()->getId();
        } else {
            $storeCodes = explode(
                AbstractImport::MULTI_VALUE_SEPARATOR,
                $rowData[QuestionInterface::STORE_CODES]
            );
            foreach ($storeCodes as $code) {
                $code = trim($code);
                if (isset($this->stores[$code])) {
                    $stores[] = $this->stores[$code];
                } else {
                    $this->errors[self::ERROR_UNKNOWN_STORE_CODE] = ProcessingError::ERROR_LEVEL_CRITICAL;
                    break;
                }
            }
        }

        return $stores;
    }

    /**
     * @param $rowData
     * @param $stores
     */
    private function validateUrlKey($rowData, $stores)
    {
        if (empty($rowData[QuestionInterface::URL_KEY])) {
            $this->errors[self::ERROR_COL_URL_KEY_EMPTY] = ProcessingError::ERROR_LEVEL_CRITICAL;
        } else {
            $urlKey = strtolower($rowData[QuestionInterface::URL_KEY]);
            if (!preg_match('/^[a-z0-9_-]+(\.[a-z0-9_-]+)?$/', $urlKey)) {
                $this->errors[self::ERROR_COL_URL_KEY_FORMAT] = ProcessingError::ERROR_LEVEL_CRITICAL;
            } else {
                if ($this->behavior === ModelImport::BEHAVIOR_CUSTOM) {
                    $questionId = 0;
                } else {
                    $questionId = (int) $rowData[QuestionInterface::QUESTION_ID];
                }
                if ($this->question->checkForDuplicateUrlKey($urlKey, $stores, $questionId)) {
                    $this->errors[self::ERROR_DUPLICATE_URL_KEY] = ProcessingError::ERROR_LEVEL_CRITICAL;
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getErrorMessages()
    {
        return $this->messageTemplates;
    }
}
