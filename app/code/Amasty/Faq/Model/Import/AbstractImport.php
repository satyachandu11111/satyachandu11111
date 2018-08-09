<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Model\Import;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Stdlib\StringUtils;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\AbstractEntity;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingError;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\ImportFactory;
use Magento\ImportExport\Model\ResourceModel\Helper;

abstract class AbstractImport extends AbstractEntity
{
    const ALLOWED_ERROR_LIMIT = 'isErrorLimit';

    const MULTI_VALUE_SEPARATOR = ',';

    /**
     * @var bool
     */
    private $isImport = false;

    /**
     * @var ImportValidatorInterface
     */
    private $importValidator;

    public function __construct(
        ImportValidatorInterface $importValidator,
        StringUtils $string,
        ScopeConfigInterface $scopeConfig,
        ImportFactory $importFactory,
        Helper $resourceHelper,
        ProcessingErrorAggregatorInterface $errorAggregator,
        ResourceConnection $resource,
        array $data = []
    ) {
        $this->errorMessageTemplates = array_merge(
            $this->errorMessageTemplates,
            $importValidator->getErrorMessages()
        );
        $this->errorMessageTemplates[self::ALLOWED_ERROR_LIMIT] = __('<b>Allowed errors limit is reached.</b>');
        $this->importValidator = $importValidator;

        parent::__construct(
            $string,
            $scopeConfig,
            $importFactory,
            $resourceHelper,
            $resource,
            $errorAggregator,
            $data
        );
    }

    /**
     * Validation failure message template definitions
     *
     * @var array $rowData
     * @var int $rowNum
     * @return bool
     */
    public function validateRow(array $rowData, $rowNum)
    {
        /**
         * Import logic fix.
         * hasToBeTerminated doesn't check while validation
         */
        if (!$this->isImport && $this->getErrorAggregator()->hasToBeTerminated()) {
            $this->addRowError(self::ALLOWED_ERROR_LIMIT, 0, null, null, ProcessingError::ERROR_LEVEL_CRITICAL);

            return true;
        }

        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }
        $this->_validatedRows[$rowNum] = true;
        $this->_processedEntitiesCount++;

        $validationErrors = $this->importValidator->validateRow($rowData, $this->getBehavior());
        if ($validationErrors !== true) {
            foreach ($validationErrors as $errorCode => $errorLevel) {
                /**
                 * Error level import fix.
                 * Less then ProcessingError::ERROR_LEVEL_CRITICAL will pass validation
                 */
                if ($this->isImport && $errorLevel == ProcessingError::ERROR_LEVEL_NOT_CRITICAL) {
                    $errorLevel = ProcessingError::ERROR_LEVEL_CRITICAL;
                }
                $this->addRowError($errorCode, $rowNum, null, null, $errorLevel);
            }
        }

        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }

    /**
     * @return bool
     */
    protected function _importData()
    {
        $this->processImport();

        return true;
    }

    protected function processImport()
    {
        /**
         * Import fix. Errors less then ProcessingError::ERROR_LEVEL_CRITICAL validateRow as true.
         * Skip them because Import button is active.
         */
        $this->isImport = true;

        $behavior = $this->getBehavior();
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $importData = [];
            foreach ($bunch as $rowNum => $rowData) {
                if (!$this->validateRow($rowData, $rowNum)) {
                    continue;
                }
                $importData[] = $rowData;
            }
            switch ($behavior) {
                case Import::BEHAVIOR_CUSTOM:
                    $this->customBehaviorData($importData);
                    break;
                case Import::BEHAVIOR_ADD_UPDATE:
                    $this->addUpdateBehaviorData($importData);
                    break;
                case Import::BEHAVIOR_DELETE:
                    $this->deleteBehaviorData($importData);
                    break;
            }
        }
        /** Import logic fix. Clear error log after import */
        $this->getErrorAggregator()->clear();
    }

    /**
     * Process data with custom behavior option
     *
     * @param array $data
     */
    abstract protected function customBehaviorData(array $data);

    /**
     * * Process data with add/update behavior option
     *
     * @param array $data
     */
    abstract protected function addUpdateBehaviorData(array $data);

    /**
     * * Process data with delete behavior option
     *
     * @param array $data
     */
    abstract protected function deleteBehaviorData(array $data);
}
