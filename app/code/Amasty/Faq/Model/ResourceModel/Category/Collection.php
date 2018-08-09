<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Model\ResourceModel\Category;

use Amasty\Faq\Model\Config\CategoriesSort;

/**
 * @method \Amasty\Faq\Model\Category[] getItems()
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'category_id';

    protected $_eventPrefix = 'faq_category_collection';

    protected $_eventObject = 'category_collection';

    /**
     * cache tag
     */
    const CACHE_TAG = 'amfaq_category';

    /**
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * @var \Amasty\Faq\Model\ConfigProvider
     */
    private $configProvider;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * Collection constructor.
     *
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface    $entityFactory
     * @param \Psr\Log\LoggerInterface                                     $logger
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                    $eventManager
     * @param \Amasty\Faq\Model\ConfigProvider                             $configProvider
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null          $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null    $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Amasty\Faq\Model\ConfigProvider $configProvider,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->configProvider = $configProvider;
        $this->storeManager = $storeManager;
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\Faq\Model\Category::class, \Amasty\Faq\Model\ResourceModel\Category::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG];
    }

    /**
     * @param int[]|int $entityIds
     * @param string $entityType
     *
     * @return $this
     */
    private function addFilterForCategories($entityIds, $entityType)
    {
        $this->getResource()->addRelationFilter($this->getSelect(), $entityIds, $entityType);

        return $this;
    }

    /**
     * @param int[]|int $storeIds
     *
     * @return $this
     */
    public function addStoreFilter($storeIds)
    {
        $this->addFilterForCategories($storeIds, 'store_ids');

        return $this;
    }

    /**
     * @param null $storeId
     * @param null|string $sort
     *
     * @return $this
     */
    public function addFrontendFilters($storeId = null, $sort = null)
    {
        $this->addFieldToFilter('status', \Amasty\Faq\Model\OptionSource\Category\Status::STATUS_ENABLED);

        if ($sort === null) {
            $sort = $this->configProvider->getCategoriesSort();
        }
        switch ($sort) {
            case CategoriesSort::MOST_VIEWED:
                $this->setOrder('visit_count', 'DESC');
                break;
            case CategoriesSort::SORT_BY_NAME:
                $this->setOrder('title', 'ASC');
                break;
            case CategoriesSort::SORT_BY_POSITION:
            default:
                $this->setOrder('position', 'ASC');
                break;
        }

        $storeIds = [\Magento\Store\Model\Store::DEFAULT_STORE_ID];
        if ($storeId) {
            $storeIds[] = (int) $storeId;
        }
        $this->addStoreFilter($storeIds);

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstCategoryUrl()
    {
        $path = $this->configProvider->getUrlKey();
        $this->addFrontendFilters($this->storeManager->getStore()->getId())
            ->setPageSize(1)
            ->setCurPage(1);
        if ($this->getSize()) {
            $path .= '/' . $this->getFirstItem()->getUrlKey();
        }

        return $path;
    }
}
