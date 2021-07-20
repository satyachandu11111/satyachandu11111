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
namespace Aheadworks\Raf\Ui\DataProvider\Rule;

use Aheadworks\Raf\Model\ResourceModel\Rule\CollectionFactory;
use Aheadworks\Raf\Model\ResourceModel\Rule\Collection;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Aheadworks\Raf\Api\Data\RuleInterface;

/**
 * Class FormDataProvider
 *
 * @package Aheadworks\Raf\Ui\DataProvider\Rule
 */
class FormDataProvider extends AbstractDataProvider
{
    /**
     * Key for saving and getting form data from data persistor
     */
    const DATA_PERSISTOR_FORM_DATA_KEY = 'aw_raf_rule';

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->request = $request;
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $preparedData = [];
        $dataFromForm = $this->dataPersistor->get(self::DATA_PERSISTOR_FORM_DATA_KEY);

        if (!empty($dataFromForm) && (is_array($dataFromForm)) && (!empty($dataFromForm[RuleInterface::ID]))) {
            $id = $dataFromForm[RuleInterface::ID];
            $this->dataPersistor->clear(self::DATA_PERSISTOR_FORM_DATA_KEY);
            $preparedData[$id] = $dataFromForm;
        } else {
            $id = $this->request->getParam($this->getRequestFieldName());
            $rules = $this->getCollection()->addFieldToFilter(RuleInterface::ID, $id)->getItems();
            /** @var \Aheadworks\Raf\Model\Rule $rule */
            foreach ($rules as $rule) {
                if ($id == $rule->getId()) {
                    $preparedData[$id] = $this->getPreparedRuleData($rule->getData());
                }
            }
        }

        return $preparedData;
    }

    /**
     * Retrieve array with prepared rule data
     *
     * @param array $ruleData
     * @return array
     */
    private function getPreparedRuleData($ruleData)
    {
        $ruleData[RuleInterface::ADVOCATE_OFF] = $ruleData[RuleInterface::ADVOCATE_OFF] * 1;
        $ruleData[RuleInterface::FRIEND_OFF] = $ruleData[RuleInterface::FRIEND_OFF] * 1;
        $ruleData[RuleInterface::IS_APPLY_TO_SHIPPING] = $ruleData[RuleInterface::IS_APPLY_TO_SHIPPING] ? '1' : '0';
        $ruleData[RuleInterface::IS_REGISTRATION_REQUIRED] = $ruleData[RuleInterface::IS_REGISTRATION_REQUIRED]
            ? '1'
            : '0';
        return $ruleData;
    }
}
