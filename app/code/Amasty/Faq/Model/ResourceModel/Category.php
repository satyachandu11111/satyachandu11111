<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Model\ResourceModel;

use Amasty\Faq\Api\Data\CategoryInterface;
use Amasty\Faq\Model\ImageProcessor;
use Amasty\Faq\Model\OptionSource\Category\Status;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\DB\Helper;
use Magento\Framework\DataObject;
use Magento\Store\Model\Store;
use Amasty\Faq\Model\ConfigProvider;
use Amasty\Faq\Setup\Operation;

class Category extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var Helper
     */
    private $dbHelper;

    /**
     * @var DataObject
     */
    private $associatedCategoryEntityMap;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var ImageProcessor
     */
    private $imageProcessor;

    /**
     * Category constructor.
     *
     * @param Context        $context
     * @param Helper         $dbHelper
     * @param DataObject     $associatedCategoryEntityMap
     * @param ConfigProvider $configProvider
     * @param null           $connectionName
     */
    public function __construct(
        Context $context,
        Helper $dbHelper,
        DataObject $associatedCategoryEntityMap,
        ConfigProvider $configProvider,
        ImageProcessor $imageProcessor,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->associatedCategoryEntityMap = $associatedCategoryEntityMap;
        $this->dbHelper = $dbHelper;
        $this->configProvider = $configProvider;
        $this->imageProcessor = $imageProcessor;
    }

    public function _construct()
    {
        $this->_init(Operation\CreateCategoryTable::TABLE_NAME, CategoryInterface::CATEGORY_ID);
    }

    /**
     * @param string $entityType
     *
     * @return array
     */
    public function getReferenceConfig($entityType = '')
    {
        return $this->associatedCategoryEntityMap->getData($entityType);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string        $field
     * @param mixed         $value
     * @param AbstractModel $object
     *
     * @return Select
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $select = $this->joinRelationTables($select);
        return $select;
    }

    /**
     * Perform actions after object load
     *
     * @param \Magento\Framework\Model\AbstractModel|\Amasty\Faq\Model\Category $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        $object->setOrigData();
        return parent::_afterLoad($object);
    }

    /**
     * Join relation tables
     *
     * @param Select $select
     *
     * @return Select
     */
    protected function joinRelationTables($select)
    {
        $categoryTable = $this->getTable(Operation\CreateCategoryTable::TABLE_NAME);
        foreach ($this->getReferenceConfig() as $entityType => $referenceConfig) {
            $select->joinLeft(
                [$referenceConfig['table'] => $this->getTable($referenceConfig['table'])],
                $categoryTable . '.category_id = ' . $referenceConfig['table'] . '.category_id',
                []
            );
            $this->dbHelper->addGroupConcatColumn(
                $select,
                $entityType,
                'DISTINCT ' . $referenceConfig['table'] . '.' . $referenceConfig['column']
            );
        }
        return $select;
    }

    /**
     * @param int    $categoryId
     * @param string $entityType
     *
     * @return Select
     */
    public function getReferenceSelect($categoryId, $entityType)
    {
        $connection = $this->getConnection();
        $config = $this->getReferenceConfig();
        $referenceConfig = $config[$entityType];
        $select = $connection->select()
            ->from($this->getTable($referenceConfig['table']), [$referenceConfig['column']])
            ->where('category_id = ?', $categoryId);

        return $select;
    }

    /**
     * @param string $urlKey
     * @param int[]|int $storeIds
     *
     * @return Select
     */
    private function getLoadByUrlKeySelect($urlKey, $storeIds)
    {
        if (!is_array($storeIds)) {
            $storeIds = [(int) $storeIds];
        }
        $storeIds[] = Store::DEFAULT_STORE_ID;

        return $this->getConnection()->select()
            ->from(['fc' => $this->getMainTable()])
            ->join(
                ['fcs' => $this->getTable(Operation\CreateCategoryStoreTable::TABLE_NAME)],
                'fc.category_id = fcs.category_id',
                []
            )
            ->where('fc.url_key = ?', $urlKey)
            ->where('fcs.store_id IN (?)', $storeIds)
            ->order('fcs.store_id DESC')
            ->limit(1)
            ->reset(Select::COLUMNS)
            ->columns('fc.category_id');
    }

    /**
     * @param string $urlKey
     * @param int $storeId
     *
     * @return string|bool
     */
    public function checkUrlKey($urlKey, $storeId)
    {
        $select = $this->getLoadByUrlKeySelect($urlKey, $storeId);
        $select->where('fc.status = ?', Status::STATUS_ENABLED, 'int');

        return $this->getConnection()->fetchOne($select);
    }

    /**
     * @param string $urlKey
     * @param int[]|int $storeIds
     * @param int $categoryId
     *
     * @return bool
     */
    public function checkForDuplicateUrlKey($urlKey, $storeIds, $categoryId)
    {
        $select = $this->getLoadByUrlKeySelect($urlKey, $storeIds);
        if ($categoryId) {
            $select->where('fc.category_id <> ?', $categoryId);
        }

        return (bool) $this->getConnection()->fetchOne($select);
    }

    /**
     * @param \Amasty\Faq\Model\Category $object
     *
     * @return bool
     */
    private function isValidUrlKey(\Amasty\Faq\Model\Category $object)
    {
        return (bool) preg_match('/^[a-z0-9_-]+(\.[a-z0-9_-]+)?$/', $object->getUrlKey());
    }

    /**
     * @param \Amasty\Faq\Model\Category $object
     *
     * @return bool
     */
    private function isDuplicateUrlKey(\Amasty\Faq\Model\Category $object)
    {
        return $this->checkForDuplicateUrlKey(
            $object->getUrlKey(),
            (array) $object->getData('store_ids'),
            $object->getCategoryId()
        );
    }

    /**
     * @param AbstractModel|\Amasty\Faq\Model\Category $object
     *
     * @return $this
     * @throws \Amasty\Faq\Exceptions\DuplicateUrlKeyException
     * @throws \Amasty\Faq\Exceptions\InvalidUrlKeyException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if (!$this->isValidUrlKey($object)) {
            throw new \Amasty\Faq\Exceptions\InvalidUrlKeyException();
        }
        if ($this->isDuplicateUrlKey($object)) {
            throw new \Amasty\Faq\Exceptions\DuplicateUrlKeyException();
        }

        if (($object->getOrigData('icon') && $object->getOrigData('icon') != $object->getIcon())) {
            $this->imageProcessor->deleteImage($object->getOrigData('icon'));
        }
        if (($image = $object->getData('icon_file')) && !empty($image['delete'])) {
            $this->imageProcessor->deleteImage($image[0]['name']);
            $object->setIcon(null);
        }

        return $this;
    }

    /**
     * Perform actions after object save
     *
     * @param AbstractModel|\Amasty\Faq\Model\Category $object
     *
     * @return $this
     */
    protected function _afterSave(AbstractModel $object)
    {
        $connection = $this->getConnection();
        $categoryId = $object->getId();
        foreach ($this->getReferenceConfig() as $entityType => $referenceConfig) {
            $table = $this->getTable($referenceConfig['table']);
            $select = $this->getReferenceSelect($categoryId, $entityType);
            $oldData = $connection->fetchCol($select);
            $newData = $object->getData($entityType);

            if (is_string($newData)) {
                $newData = explode(',', $newData);
            }

            if (is_array($newData)) {
                $toDelete = array_diff($oldData, $newData);
                $toInsert = array_diff($newData, $oldData);
                $toInsert = array_diff($toInsert, ['']);
            } else {
                $toDelete = $oldData;
                $toInsert = null;
            }

            if (!empty($toDelete)) {
                $deleteSelect = clone $select;
                $deleteSelect->where($referenceConfig['column'] . ' IN (?)', $toDelete);
                $query = $connection->deleteFromSelect($deleteSelect, $table);
                $connection->query($query);
            }

            if (!empty($toInsert)) {
                $insertArray = [];
                foreach ($toInsert as $value) {
                    $insertArray[] = ['category_id' => $categoryId, $referenceConfig['column'] => $value];
                }
                $connection->insertMultiple($table, $insertArray);
            }
        }
        if ($object->getIcon() && ($image = $object->getData('icon_file')) && isset($image[0]['size'])) {
            $this->imageProcessor->processCategoryIcon($object->getIcon());
        }

        return parent::_afterSave($object);
    }

    /**
     * Join relation table
     *
     * @param Select $select
     * @param string $entityType
     * @param bool $group
     * @return Select
     */
    protected function joinRelationTable($select, $entityType, $group = true)
    {
        $referenceConfig = $this->getReferenceConfig($entityType);
        $alias = $referenceConfig['table'];
        $fromPart = $select->getPart(\Zend_Db_Select::FROM);
        if (isset($fromPart[$alias])) {
            return $select;
        }
        $table = $this->getTable($this->getMainTable());
        if (!in_array($table, array_keys($select->getPart(\Zend_Db_Select::FROM)))) {
            $table = 'main_table';
        }
        $select->joinLeft(
            [$alias => $this->getTable($alias)],
            $table . '.' . $this->getIdFieldName() . '  = ' . $alias . '.'.$this->getIdFieldName(),
            []
        );
        if (!$group) {
            return $select;
        }
        $this->dbHelper->addGroupConcatColumn(
            $select,
            $entityType,
            'DISTINCT ' . $alias . '.' . $referenceConfig['column']
        );

        return $select;
    }

    /**
     * @param Select $select
     * @param int[]|int                    $entityIds
     * @param string                       $entityType
     *
     * @return $this
     */
    public function addRelationFilter(Select $select, $entityIds, $entityType)
    {
        $this->joinRelationTable($select, $entityType, false);
        $referenceConfig = $this->getReferenceConfig($entityType);
        $column = sprintf('%s.%s', $referenceConfig['table'], $referenceConfig['column']);
        if (is_array($entityIds)) {
            $select->where($column . ' IN (?)', $entityIds);
        } else {
            $select->where($column . ' = ?', $entityIds);
        }

        return $this;
    }

    /**
     * Perform actions after object delete
     *
     * @param \Magento\Framework\Model\AbstractModel|\Amasty\Faq\Model\Category $object
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _afterDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getIcon()) {
            $this->imageProcessor->deleteImage($object->getIcon());
        }

        return parent::_afterDelete($object);
    }
}
