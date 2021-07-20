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
namespace Aheadworks\Raf\Ui\DataProvider\Advocate;

use Aheadworks\Raf\Model\ResourceModel\AdvocateSummary\CollectionFactory;
use Aheadworks\Raf\Model\ResourceModel\AdvocateSummary\Collection;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Aheadworks\Raf\Api\Data\AdvocateSummaryInterface;

/**
 * Class FormDataProvider
 *
 * @package Aheadworks\Raf\Ui\DataProvider\Advocate
 */
class FormDataProvider extends AbstractDataProvider
{
    /**
     * Key for saving and getting form data from data persistor
     */
    const DATA_PERSISTOR_FORM_DATA_KEY = 'aw_raf_advocate';

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

        if (!empty($dataFromForm)
            && (is_array($dataFromForm))
            && (!empty($dataFromForm[AdvocateSummaryInterface::ID]))
        ) {
            $id = $dataFromForm[AdvocateSummaryInterface::ID];
            $this->dataPersistor->clear(self::DATA_PERSISTOR_FORM_DATA_KEY);
            $preparedData[$id] = $dataFromForm;
        } else {
            $id = $this->request->getParam($this->getRequestFieldName());
            $advocates = $this->getCollection()->addFieldToFilter(AdvocateSummaryInterface::ID, $id)->getItems();

            /** @var \Aheadworks\Raf\Model\AdvocateSummary $advocate */
            foreach ($advocates as $advocate) {
                if ($id == $advocate->getId()) {
                    $preparedData[$id] = $this->getPreparedAdvocateData($advocate->getData());
                }
            }
        }
        return $preparedData;
    }

    /**
     * Retrieve array with prepared rule data
     *
     * @param array $advocateData
     * @return array
     */
    private function getPreparedAdvocateData($advocateData)
    {
        return $advocateData;
    }
}
