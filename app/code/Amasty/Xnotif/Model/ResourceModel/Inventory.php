<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */


namespace Amasty\Xnotif\Model\ResourceModel;

use Magento\CatalogInventory\Model\Configuration;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Inventory extends AbstractDb
{
    /**
     * @var array
     */
    private $isInStock;

    /**
     * @var array
     */
    private $stockIds;

    /**
     * @var array
     */
    private $sourceCodes;

    /**
     * @var array
     */
    private $qty;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @var array
     */
    private $lowStockProducts;

    /**
     * @var int
     */
    private $defaultLowStockQty;

    /**
     * @var Magento\CatalogInventory\Model\Stock\StockItemRepository
     */
    private $stockItemRepository;

    /**
     * @var Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var array
     */
    private $stockItems = [];

    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        ScopeConfigInterface $config,
        StockItemRepository $stockItemRepository,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->moduleManager = $moduleManager;
        $this->stockRegistry = $stockRegistry;
        $this->scopeConfig = $config;
        $this->defaultLowStockQty = $config->getValue(Configuration::XML_PATH_NOTIFY_STOCK_QTY);
        $this->stockItemRepository = $stockItemRepository;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->isInStock = [];
        $this->stockIds = [];
        $this->sourceCodes = [];
        $this->qty = [];
        $this->lowStockProducts = [];
    }

    /**
     * @param string $productSku
     * @param int $stockId
     *
     * @return bool
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getIsInStock($productSku, $stockId)
    {
        if (!isset($this->isInStock[$productSku])) {
            $select = $this->getConnection()->select()
                ->from($this->getTable('inventory_stock_' . $stockId), ['is_salable'])
                ->where('sku = ?', $productSku);
            $this->isInStock[$productSku] = (bool) $this->getConnection()->fetchOne($select);
        }

        return $this->isInStock[$productSku];
    }

    /**
     * @param $productSku
     * @param $websiteCode
     *
     * @return float|int
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getQty($productSku, $websiteCode)
    {
        if ($this->moduleManager->isEnabled('Magento_Inventory')) {
            $qty = $this->getMsiQty($productSku, $websiteCode);
        } else {
            $qty = $this->getStockItem($productSku, $websiteCode)->getQty();
        }

        return $qty;
    }

    /**
     * @param string $productSku
     * @param int $websiteCode
     *
     * @return \Magento\CatalogInventory\Api\Data\StockItemInterface
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getStockItem($productSku, $websiteCode)
    {
        if (!isset($this->stockItems[$productSku])) {
            $this->stockItems[$productSku] = $this->stockRegistry->getStockItemBySku($productSku, $websiteCode);
        }

        return $this->stockItems[$productSku];
    }

    /**
     * For MSI. Need to get negative qty.
     * Emulate \Magento\InventoryReservations\Model\ResourceModel\GetReservationsQuantity::execute
     *
     * @param string $productSku
     * @param string $websiteCode
     *
     * @return float|int
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMsiQty($productSku, $websiteCode)
    {
        if (!isset($this->qty[$websiteCode][$productSku])) {
            $this->qty[$websiteCode][$productSku] = $this->getItemQty($productSku, $websiteCode)
                + $this->getReservationQty($productSku, $this->getStockId($websiteCode));
        }

        return $this->qty[$websiteCode][$productSku];
    }

    /**
     * @param string $productSku
     * @param string $websiteCode
     *
     * @return float|int
     */
    private function getItemQty($productSku, $websiteCode)
    {
        return $this->getQtyBySources($productSku, $this->getSourceCodes($websiteCode));
    }

    /**
     * @param string $productSku
     * @param array $sourcesCodes
     *
     * @return float|int
     */
    private function getQtyBySources($productSku, $sourcesCodes)
    {
        $select = $this->getConnection()->select()
            ->from($this->getTable('inventory_source_item'), ['SUM(quantity)'])
            ->where('source_code IN (?)', $sourcesCodes)
            ->where('sku = ?', $productSku)
            ->group('sku');

        return $this->getConnection()->fetchOne($select);
    }

    /**
     * @param string $productSku
     * @param string $sourcesCode
     *
     * @return float|int
     */
    public function getQtyBySource($productSku, $sourceCode)
    {
        return $this->getQtyBySources($productSku, [$sourceCode]);
    }

    /**
     * For MSI.
     *
     * @param string $websiteCode
     *
     * @return int
     */
    public function getStockId($websiteCode)
    {
        if (!isset($this->stockIds[$websiteCode])) {
            $select = $this->getConnection()->select()
                ->from($this->getTable('inventory_stock_sales_channel'), ['stock_id'])
                ->where('type = \'website\' AND code = ?', $websiteCode);

            $this->stockIds[$websiteCode] = (int)$this->getConnection()->fetchOne($select);
        }

        return $this->stockIds[$websiteCode];
    }

    /**
     * For MSI.
     *
     * @param string $websiteCode
     *
     * @return array
     */
    public function getSourceCodes($websiteCode)
    {
        if (!isset($this->sourceCodes[$websiteCode])) {
            $select = $this->getConnection()->select()
                ->from($this->getTable('inventory_source_stock_link'), ['source_code'])
                ->where('stock_id = ?', $this->getStockId($websiteCode));

            $this->sourceCodes[$websiteCode] = $this->getConnection()->fetchCol($select);
        }

        return $this->sourceCodes[$websiteCode];
    }

    /**
     * For MSI.
     *
     * @param string $sku
     * @param int $stockId
     *
     * @return int|string
     */
    private function getReservationQty($sku, $stockId)
    {
        $select = $this->getConnection()->select()
            ->from($this->getTable('inventory_reservation'), ['quantity' => 'SUM(quantity)'])
            ->where('sku = ?', $sku)
            ->where('stock_id = ?', $stockId)
            ->limit(1);

        $reservationQty = $this->getConnection()->fetchOne($select);
        if ($reservationQty === false) {
            $reservationQty = 0;
        }

        return $reservationQty;
    }

    /**
     * @param array $itemsSku
     * @param string|null $source
     * @return string[]
     */
    public function getLowStockItemsSku(array $itemsSku, ?string $source): array
    {
        $lowStockItemsSku = [];
        $fetchResult = $this->getLowStockQtyItems($itemsSku, $source);

        foreach ($fetchResult as $item) {
            $lowStockQty = (int)($item['notify_stock_qty'] ?? $this->defaultLowStockQty);

            if ((int)$item['quantity'] < $lowStockQty) {
                $lowStockItemsSku[] = $item['sku'];
            }
        }

        return $lowStockItemsSku;
    }

    private function getLowStockQtyItems(array $itemsSku, ?string $source): array
    {
        $select = $this->getConnection()->select();

        if ($this->moduleManager->isEnabled('Magento_Inventory')
            && $this->moduleManager->isEnabled('Magento_InventoryLowQuantityNotification')) {
            $fromCondition = [
                ['isi' => $this->getTable('inventory_source_item')],
                ['quantity', 'sku']
            ];
            $joinCondition = [
                ['ilsnc' => $this->getTable('inventory_low_stock_notification_configuration')],
                'ilsnc.sku = isi.sku AND ilsnc.source_code = isi.source_code',
                ['notify_stock_qty']
            ];
            $skuCondition = ['isi.sku IN (?)', $itemsSku];
            $select->where('isi.source_code = ?', $source);
        } else {
            $fromCondition = [
                ['csi' => $this->getTable('cataloginventory_stock_item')],
                ['quantity' => 'qty', 'notify_stock_qty']
            ];
            $joinCondition = [
                ['cpe' => $this->getTable('catalog_product_entity')],
                'csi.item_id = cpe.entity_id',
                ['sku']
            ];
            $skuCondition = ['cpe.sku IN (?)', $itemsSku];
        }

        $select->from(...$fromCondition)->joinLeft(...$joinCondition)->where(...$skuCondition);

        return $this->getConnection()->fetchAll($select);
    }

    /**
     * @param string $itemSku
     * @param int $websiteCode
     * @return float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */

    public function getItemMinQty($itemSku, $websiteCode)
    {
        $item = $this->getStockItem($itemSku, $websiteCode);
        return $item->getMinQty();
    }

    /**
     * @param string $sourceCode
     * @return string
     */
    public function getSourceName($sourceCode)
    {
        $select = $this->getConnection()->select()
            ->from($this->getTable('inventory_source'), ['name'])
            ->where('source_code = ?', $sourceCode)
            ->limit(1);

        return $this->getConnection()->fetchOne($select);
    }
}
