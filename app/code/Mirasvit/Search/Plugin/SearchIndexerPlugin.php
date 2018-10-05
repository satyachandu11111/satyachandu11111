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
 * @package   mirasvit/module-search
 * @version   1.0.94
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Search\Plugin;

class SearchIndexerPlugin
{
    public function afterPrepareProductIndex(
        $dataProvider,
        $attributeData,
        $productData = null,
        $productAdditional = null,
        $storeId = null
    ) {
        if ($productData === null) {
            return $attributeData;
        }

        $productData = array_values($productData)[0];

        foreach ($attributeData as $attributeId => $value) {
            $attribute = $dataProvider->getSearchableAttribute($attributeId);

            if (!empty($value) && $attribute->getFrontendInput() == 'multiselect') {
                $attribute->setStoreId($storeId);
                $options = $attribute->getSource()->toOptionArray();
                $optionLabels = [];

                if (array_key_exists($attributeId, $productData)) {
                    foreach ($options as $optionValue) {
                        if (in_array($optionValue['value'], explode(',', $productData[$attributeId]))) {
                            $optionLabels[] = $optionValue['label'];
                        }
                    }
                }

                $attributeData[$attributeId] = implode(' | ', $optionLabels);
            }
        }

        return $attributeData;
    }
}
