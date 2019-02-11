<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Model\ResourceModel\Method;

/**
 * Class Saving
 */
class Saving extends AbstractMethod
{
    public function apply($collection, $direction)
    {
        $connection = $this->getConnection();
        $table      = $this->getPriceAlias($collection);

        /** LEAST(min_price, tier_price) */
        $least = $connection->getLeastSql(["$table.min_price", "$table.tier_price"]);
        $price = $table . '.price';
        /** tier_price IS NOT NULL */
        $tpNotNull = $connection->prepareSqlCondition("$table.tier_price", ['notnull' => true]);
        /** IF (tier_price IS NOT NULL, LEAST(min_price, tier_price), min_price) */
        $minPrice = $connection->getCheckSql($tpNotNull, $least, "$table.min_price");

        if ($this->helper->getScopeValue('biggest_saving/saving')) {
            $percent = "($price - $minPrice) * 100 / $price";
            $saving  = $connection->getCheckSql($price, $percent, 0);
        } else {
            $saving = "($price - $minPrice)";
        }

        $collection->getSelect()->columns([$this->getMethodCode() => new \Zend_Db_Expr($saving)]);
        $collection->addExpressionAttributeToSelect($this->getMethodCode(), $this->getMethodCode(), []);

        // remove last item from columns because e.saving from addExpressionAttributeToSelect not exist
        $columns = $collection->getSelect()->getPart(\Zend_Db_Select::COLUMNS);
        array_pop($columns);
        $collection->getSelect()->setPart(\Zend_Db_Select::COLUMNS, $columns);

        return $this;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->getMethodCode();
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     *
     * @return string
     */
    private function getPriceAlias($collection)
    {
        $tableAliases = array_keys($collection->getSelect()->getPart(\Magento\Framework\DB\Select::FROM));
        if (in_array($collection::INDEX_TABLE_ALIAS, $tableAliases)) {
            return $collection::INDEX_TABLE_ALIAS;
        }

        return reset($tableAliases);
    }
}
