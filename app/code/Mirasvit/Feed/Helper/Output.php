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
 * @package   mirasvit/module-feed
 * @version   1.0.82
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Feed\Helper;

use Magento\Backend\Model\UrlInterface as BackendUrlInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Feed\Api\Repository\ValidationRepositoryInterface;
use Mirasvit\Feed\Export\Filter\Pool as FilterPool;
use Mirasvit\Feed\Export\Resolver\Pool as ResolverPool;
use Mirasvit\Feed\Model\Feed;
use Magento\Framework\ObjectManagerInterface;

class Output extends AbstractHelper
{
    /**
     * @var FilterPool
     */
    protected $filterPool;

    /**
     * @var ResolverPool
     */
    protected $resolverPool;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $operatorInputByType = [
        'string'      => ['==', '!=', '>=', '>', '<=', '<', '{}', '!{}'],
        'numeric'     => ['==', '!=', '>=', '>', '<=', '<'],
        'date'        => ['==', '>=', '<='],
        'select'      => ['==', '!='],
        'boolean'     => ['==', '!='],
        'multiselect' => ['{}', '!{}', '()', '!()'],
        'grid'        => ['()', '!()'],
    ];

    /**
     * @var array
     */
    protected $operatorOptions = [
        '=='  => 'is',
        '!='  => 'is not',
        '>='  => 'equals or greater than',
        '<='  => 'equals or less than',
        '>'   => 'greater than',
        '<'   => 'less than',
        '{}'  => 'contains',
        '!{}' => 'does not contain',
        '()'  => 'is one of',
        '!()' => 'is not one of',
    ];
    /**
     * @var ValidationRepositoryInterface
     */
    private $validationRepository;

    public function __construct(
        ValidationRepositoryInterface $validationRepository,
        FilterPool $filterPool,
        ResolverPool $resolverPool,
        ObjectManagerInterface $objectManager,
        Context $context
    ) {
        $this->validationRepository = $validationRepository;
        $this->filterPool = $filterPool;
        $this->resolverPool = $resolverPool;
        $this->objectManager = $objectManager;

        parent::__construct($context);
    }

    /**
     * List of defined attributes to export
     *
     * @return array
     */
    public function getAttributeOptions()
    {
        $options = [];

        foreach ($this->resolverPool->getResolvers() as $resolver) {
            $attributes = $resolver->getAttributes();

            asort($attributes);

            foreach ($attributes as $code => $label) {
                $group = $this->getAttributeGroup($code);
                $options[$group]['label'] = $group;
                $options[$group]['value'][] = ['value' => $code, 'label' => $label];
            }
        }

        usort($options, function ($a, $b) {
            return strcmp($a['label'], $b['label']);
        });

        return array_values($options);
    }


    /**
     * List of pattern types
     *
     * @return array
     */
    public function getPatternTypeOptions()
    {
        return [
            [
                'label' => 'Pattern',
                'value' => 'pattern'
            ],
            [
                'label' => 'Attribute',
                'value' => ''
            ],
            [
                'label' => 'Parent Product',
                'value' => 'parent'
            ],
            [
                'label' => 'Only Parent Product',
                'value' => 'only_parent',
            ],
            [
                'label' => 'Grouped Product',
                'value' => 'grouped',
            ],
        ];
    }

    /**
     * List of filters
     *
     * @return array
     */
    public function getFilterOptions()
    {
        return $this->filterPool->getFilters();
    }

    /**
     * List of validators.
     *
     * @return array
     */
    public function getValidatorOptions()
    {
        $options = [];
        foreach ($this->validationRepository->getValidators() as $validator) {
            $options[] = [
                'label' => $validator->getName(),
                'value' => $validator->getCode()
            ];
        }

        return $options;
    }

    /**
     * Attribute group
     *
     * @param string $code
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getAttributeGroup($code)
    {
        $primary = [
            'attribute_set',
            'attribute_set_id',
            'entity_id',
            'full_description',
            'meta_description',
            'meta_keyword',
            'meta_title',
            'name',
            'short_description',
            'description',
            'sku',
            'status',
            'status_parent',
            'url',
            'url_key',
            'visibility',
            'type_id'
        ];

        $stock = [
            'is_in_stock',
            'qty',
            'manage_stock',
        ];

        $price = [
            'tax_class_id',
            'special_from_date',
            'special_to_date',
            'cost',
            'msrp',
        ];

        if (in_array($code, $primary)) {
            $group = __('1. Primary Attributes');
        } elseif (in_array($code, $stock)) {
            $group = __('5. Stock Attributes');
        } elseif (in_array($code, $price) || strpos($code, 'price') !== false) {
            $group = __('2. Prices & Taxes');
        } elseif (strpos($code, 'image') !== false || strpos($code, 'thumbnail') !== false) {
            $group = __('4. Images');
        } elseif (strpos($code, 'category') !== false) {
            $group = __('3. Category');
        } elseif (strpos($code, 'dynamic') !== false) {
            $group = __('6. Dynamic Attributes');
        } elseif (strpos($code, 'variable') !== false) {
            $group = __('7. Dynamic Variables');
        } elseif (strpos($code, 'mapping') === 0) {
            $group = __('8. Category Mappings');
        } else {
            $group = __('9. Others Attributes');
        }

        return $group->__toString();
    }

    /**
     * @param string $attributeCode
     * @return array
     */
    public function getAttributeOperators($attributeCode)
    {
        $conditions = [];

        $attribute = $this->getAttribute($attributeCode);

        $type = 'string';

        if ($attribute) {
            switch ($attribute->getFrontendInput()) {
                case 'select':
                    $type = 'select';
                    break;

                case 'multiselect':
                    $type = 'multiselect';
                    break;

                case 'date':
                    $type = 'date';
                    break;

                case 'boolean':
                    $type = 'boolean';
                    break;

                default:
                    $type = 'string';
            }
        }
        foreach ($this->operatorInputByType[$type] as $operator) {
            $operatorTitle = __($this->operatorOptions[$operator]);
            $conditions[] = [
                'label' => $operatorTitle,
                'value' => $operator,
            ];
        }

        return $conditions;
    }

    /**
     * @param string $attributeCode
     * @return array
     */
    public function getAttributeValues($attributeCode)
    {
        $result = [];

        $attribute = $this->getAttribute($attributeCode);
        if ($attribute) {
            if ($attribute->getFrontendInput() == 'select' || $attribute->getFrontendInput() == 'multiselect') {
                $result[] = ['label' => __('not set'), 'value' => ''];
                foreach ($attribute->getSource()->getAllOptions() as $option) {
                    $result[] = [
                        'label' => $option['label'],
                        'value' => $option['value'],
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * @param string $code
     * @return \Magento\Eav\Model\Attribute|false
     */
    protected function getAttribute($code)
    {
        $entityTypeId = $this->objectManager->get('Magento\Eav\Model\Entity')
            ->setType('catalog_product')->getTypeId();

        /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection $attributes */
        $attributes = $this->objectManager
            ->create('Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection')
            ->setEntityTypeFilter($entityTypeId);

        $attribute = $attributes->getItemByColumnValue('attribute_code', $code);

        if ($attribute) {
            return $this->objectManager->create('Magento\Catalog\Model\ResourceModel\Eav\Attribute')
                ->setEntityTypeId($entityTypeId)
                ->load($attribute->getId());
        }

        return false;
    }
}