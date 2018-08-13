<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OptionTemplates\Model\Observer;

use Magento\Catalog\Model\ResourceModel\Product\Option\CollectionFactory as ProductOptionCollectionFactory;
use Magento\Framework\Event\Observer;
use MageWorx\OptionBase\Helper\Data as BaseHelper;
use MageWorx\OptionTemplates\Model\ResourceModel\Group\CollectionFactory as GroupCollectionFactory;
use MageWorx\OptionTemplates\Model\ProductAttributes;

/**
 * Observer class for add option groups to product
 */
class AddGroupOptionToProductObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var  \Magento\Framework\Registry
     */
    protected $registry;

    /**
     *
     * @var \MageWorx\OptionTemplates\Model\OptionSaver
     */
    protected $optionSaver;

    /**
     *
     * @var GroupCollectionFactory
     */
    protected $groupCollectionFactory;

    /**
     *
     * @var ProductOptionCollectionFactory
     */
    protected $productOptionCollectionFactory;

    /**
     * @var BaseHelper
     */
    protected $baseHelper;

    /**
     * @var ProductAttributes
     */
    protected $productAttributes;

    /**
     *
     * @param \Magento\Framework\Registry $registry
     * @param \MageWorx\OptionTemplates\Model\OptionSaver $optionSaver
     * @param BaseHelper $baseHelper
     * @param GroupCollectionFactory $groupCollectionFactory
     * @param ProductAttributes $productAttributes
     * @param ProductOptionCollectionFactory $productOptionCollectionFactory
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \MageWorx\OptionTemplates\Model\OptionSaver $optionSaver,
        GroupCollectionFactory $groupCollectionFactory,
        BaseHelper $baseHelper,
        ProductAttributes $productAttributes,
        ProductOptionCollectionFactory $productOptionCollectionFactory
    ) {
        $this->registry = $registry;
        $this->optionSaver = $optionSaver;
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->baseHelper = $baseHelper;
        $this->productAttributes = $productAttributes;
        $this->productOptionCollectionFactory = $productOptionCollectionFactory;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $request = $observer->getRequest();
        $productId = $request->getParam('id');
        $post = $request->getPostValue();

        if ($this->_out($request)) {
            return;
        }

        $productOptions = [];
        if ($this->isPostContainProductOptions($post)) {
            $productOptions = $post['product']['options'];
        }

        if ($this->isPostContainGroups($post)) {
            $postGroupIds = $post['product']['option_groups'];
        } else {
            $post['product']['option_groups'] = [];
            $postGroupIds = [];
        }

        $keepOptionOnUnlink = !empty($post['product']['keep_options_on_unlink']);

        $productOptions = $this->addGroupIdToValues($productOptions);
        $modProductOptions = $productOptions;

        /** @var \MageWorx\OptionTemplates\Model\ResourceModel\Group\Collection $collectionByProduct */
        $collectionByProduct = $this->groupCollectionFactory->create();
        $issetGroupIds = $productId ? $collectionByProduct->addProductFilter($productId)->getAllIds() : [];
        $issetGroupIds = array_map('strval', $issetGroupIds);

        $addedGroupIds = array_diff($postGroupIds, $issetGroupIds);
        $deletedGroupIds = array_diff($issetGroupIds, $postGroupIds);

        $groupIds = array_merge($addedGroupIds + $deletedGroupIds);

        if ($groupIds) {
            /** @var \MageWorx\OptionTemplates\Model\ResourceModel\Group\Collection $collection */
            $collection = $this->groupCollectionFactory->create()->addFieldToFilter('group_id', $groupIds);
            /** @var \MageWorx\OptionTemplates\Model\Group $group */
            foreach ($collection as $group) {
                if (in_array($group->getId(), $addedGroupIds)) {
                    $post['product'] = array_merge(
                        $post['product'],
                        $this->productAttributes->getProductAttributesFromGroup($group)
                    );
                    $modProductOptions = $this->optionSaver->addNewOptionProcess($modProductOptions, $group);
                }
                if (in_array($group->getId(), $deletedGroupIds)) {
                    if ($keepOptionOnUnlink) {
                        $modProductOptions = $this->optionSaver->unassignOptions($modProductOptions, $group);
                        $group->deleteProductRelation($productId);
                    } else {
                        $modProductOptions = $this->optionSaver->deleteOptionProcess($modProductOptions, $group);
                    }
                }
            }
        }

        $registryIds = [
            'productId' => $productId,
            'newGroupIds' => $addedGroupIds,
            'delGroupIds' => $deletedGroupIds,
        ];

        $this->registry->register('mageworx_optiontemplates_relation_data', $registryIds, true);

        //compatibility for 2.2.x
        $modProductOptions = $this->apply22xCompatibilityFix($modProductOptions);
        $post['product']['options'] = $modProductOptions;
        $request->setPostValue($post);
    }

    /**
     * Add group id to values
     *
     * @param array $productOptions
     * @return array
     */
    protected function addGroupIdToValues($productOptions)
    {
        foreach ($productOptions as $optionIndex => $productOption) {
            if (empty($productOption['group_id']) || empty($productOption['values'])) {
                continue;
            }
            foreach ($productOption['values'] as $valueIndex => $valueData) {
                $productOptions[$optionIndex]['values'][$valueIndex]['group_id'] = $productOption['group_id'];
            }
        }
        return $productOptions;
    }

    /**
     * Apply 2.1.10+/2.2.x compatibility fix for options
     *
     * @param array $modProductOptions
     * @return array
     */
    protected function apply22xCompatibilityFix($modProductOptions)
    {
        if (!$this->baseHelper->checkModuleVersion('101.0.10')) {
            return $modProductOptions;
        }

        foreach ($modProductOptions as $optionKey => $optionData) {
            $modProductOptions[$optionKey]['id'] = null;
            $modProductOptions[$optionKey]['option_id'] = null;
            if (!empty($optionData['values'])) {
                $values = [];
                foreach ($optionData['values'] as $valueKey => $value) {
                    $value['option_type_id'] = null;
                    $values[$valueKey] = $value;
                }
                $modProductOptions[$optionKey]['values'] = $values;
            }
        }
        return $modProductOptions;
    }

    /**
     * Check if go out
     *
     * @param $request
     * @return bool
     */
    protected function _out($request)
    {
        if (!in_array($request->getFullActionName(), $this->_getAvailableActions())) {
            return true;
        }

        $isCanSaveOptions = isset($request->getPost('product')['affect_product_custom_options']);

        if (!$isCanSaveOptions) {
            return true;
        }

        return false;
    }

    /**
     * Add group_option_id to product option post data
     * @todo modify option templates for avoid it
     *
     * @param array $postOptions
     * @param int $productId
     * @return array
     */
    protected function restoreGroupOptionIds($postOptions, $productId)
    {
        $productOptionCollection = $this->productOptionCollectionFactory->create();
        $productOptionCollection->addProductToFilter($productId);

        foreach ($productOptionCollection as $productOption) {
            if (!empty($productOption['group_option_id'])) {
                if (!empty($postOptions[$productOption->getId()])) {
                    $postOptions[$productOption->getId()]['group_option_id'] = $productOption['group_option_id'];
                }
            }
        }

        return $postOptions;
    }

    /**
     * Retrieve list of available actions
     *
     * @return array
     */
    protected function _getAvailableActions()
    {
        return ['catalog_product_save'];
    }

    /**
     * Check if post contains product options
     *
     * @param $post array
     * @return bool
     */
    protected function isPostContainProductOptions($post)
    {
        if (isset($post['product']['options']) && is_array($post['product']['options'])) {
            return true;
        }
        return false;
    }

    /**
     * Check if post contains groups
     *
     * @param $post array
     * @return bool
     */
    protected function isPostContainGroups($post)
    {
        if (!isset($post['product']['option_groups']) ||
            !is_array($post['product']['option_groups']) ||
            (count($post['product']['option_groups']) == 1 && $post['product']['option_groups'][0] == 'none')
        ) {
            return false;
        }
        return true;
    }
}
