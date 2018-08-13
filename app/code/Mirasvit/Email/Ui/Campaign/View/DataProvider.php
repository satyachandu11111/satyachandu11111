<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-email
 * @version   2.1.6
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Email\Ui\Campaign\View;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as BaseDataProvider;

class DataProvider extends BaseDataProvider
{
    /**
     * @var PoolInterface
     */
    private $modifiers;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        PoolInterface $modifiers,
        array $meta = [],
        array $data = []
    ) {
        $this->modifiers = $modifiers;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $reporting, $searchCriteriaBuilder, $request,
            $filterBuilder, $meta, $data);
    }


    /**
     * {@inheritdoc}
     */
    protected function searchResultToOutput(SearchResultInterface $searchResult)
    {
        $result = [];

        foreach ($searchResult->getItems() as $item) {
            $data = [];
            foreach ($item->getCustomAttributes() as $attribute) {
                $data[$attribute->getAttributeCode()] = $attribute->getValue();
            }

            foreach ($this->modifiers->getModifiersInstances() as $modifier) {
                $data = $modifier->modifyData($data);
            }

            $result['items'][] = $data;
            if ($this->request->getParam($this->getRequestFieldName()) === $item->getId() && isset($data['report'])) {
                $result['report'] = $data['report'];
            }
        }

        return $result;
    }

}
