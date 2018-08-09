<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Model\ResourceModel\Question;

use Amasty\Faq\Api\Data\QuestionInterface;
use Amasty\Faq\Model\ConfigProvider;
use Amasty\Faq\Model\OptionSource\Question\Status;
use Amasty\Faq\Model\OptionSource\Question\Visibility;
use Amasty\Faq\Model\Config\QuestionsSort;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Helper\Mysql\Fulltext;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Psr\Log\LoggerInterface;

/**
 * @method \Amasty\Faq\Model\Question[] getItems()
 * @method \Amasty\Faq\Model\ResourceModel\Question getResource()
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Limit to show autosuggest search
     */
    const AUTOSUGGEST_LIMIT = 10;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var Fulltext
     */
    private $fulltext;

    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        Fulltext $fulltext,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        ConfigProvider $configProvider,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->configProvider = $configProvider;
        $this->fulltext = $fulltext;
    }

    public function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\Faq\Model\Question::class, \Amasty\Faq\Model\ResourceModel\Question::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }

    /**
     * cache tag
     */
    const CACHE_TAG = 'amfaq_questions';

    /**
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

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
    private function addFilterForQuestions($entityIds, $entityType)
    {
        $this->getResource()->addRelationFilter($this->getSelect(), $entityIds, $entityType);

        return $this;
    }

    /**
     * @param int[]|int $productIds
     *
     * @return $this
     */
    public function addProductFilter($productIds)
    {
        $this->addFilterForQuestions($productIds, 'product_ids');

        return $this;
    }

    /**
     * @param int[]|int $categoryIds
     *
     * @return $this
     */
    public function addCategoryFilter($categoryIds)
    {
        $this->addFilterForQuestions($categoryIds, 'category_ids');

        return $this;
    }

    /**
     * @param int[]|int $storeIds
     *
     * @return $this
     */
    public function addStoreFilter($storeIds)
    {
        $this->addFilterForQuestions($storeIds, 'store_ids');

        return $this;
    }

    /**
     * @param string $value
     *
     * @return array|bool
     */
    public function loadByQueryText($value)
    {
        if (empty($value) || !(preg_match_all('/(\w{3,})/isu', $value, $words))) {
            return false;
        }

        $searchFields = [
            'main_table.' . QuestionInterface::TITLE,
            'main_table.' . QuestionInterface::ANSWER
        ];

        $this->getSelect()->columns([
            'rel' =>  new \Zend_Db_Expr(
                $this->fulltext->getMatchQuery(
                    $searchFields,
                    implode('* ', $words[1]) . '*',
                    Fulltext::FULLTEXT_MODE_BOOLEAN
                )
            )
        ]);

        $this->fulltext->match(
            $this->getSelect(),
            $searchFields,
            implode('* ', $words[1]) . '*',
            true,
            Fulltext::FULLTEXT_MODE_BOOLEAN
        );

        $this->getSelect()->order('rel');

        return $words[1];
    }

    /**
     * @param $query
     * @return $this
     */
    public function getAutosuggestCollection($query)
    {
        $this->loadByQueryText($query);
        $this->getSelect()->joinLeft(
            ['cq' => $this->getTable(\Amasty\Faq\Setup\Operation\CreateQuestionCategoryTable::TABLE_NAME)],
            'main_table.question_id = cq.question_id',
            null
        );
        $this->getSelect()->joinLeft(
            ['category' => $this->getTable(\Amasty\Faq\Setup\Operation\CreateCategoryTable::TABLE_NAME)],
            'cq.category_id = category.category_id',
            ['category' => 'category.title']
        );
        $this->getSelect()->limit(self::AUTOSUGGEST_LIMIT);
        $this->getSelect()->group('main_table.question_id');

        return $this;
    }

    /**
     * @param bool $isLoggedIn
     * @param null|int $storeId
     * @param null|string $sort
     *
     * @return $this
     */
    public function addFrontendFilters($isLoggedIn = false, $storeId = null, $sort = null)
    {
        $this->addVisibilityFilters($isLoggedIn);
        $this->addSortFilter($sort);
        $this->addFrontendStoreFilter($storeId);
    }

    /**
     * @param bool $isLoggedIn
     * @return $this
     */
    public function addVisibilityFilters($isLoggedIn = false)
    {
        $this->addFieldToFilter('main_table.status', Status::STATUS_ANSWERED);
        if ($isLoggedIn) {
            $this->addFieldToFilter('visibility', ['neq' => Visibility::VISIBILITY_NONE]);
        } else {
            $this->addFieldToFilter('visibility', Visibility::VISIBILITY_PUBLIC);
        }

        return $this;
    }

    /**
     * @param string $sort
     *
     * @return $this
     */
    public function addSortFilter($sort = null)
    {
        if ($sort === null) {
            $sort = $this->configProvider->getQuestionsSort();
        }
        switch ($sort) {
            case QuestionsSort::MOST_VIEWED:
                $this->setOrder('visit_count', 'DESC');
                break;
            case QuestionsSort::SORT_BY_NAME:
                $this->setOrder('title', 'ASC');
                break;
            case QuestionsSort::SORT_BY_POSITION:
            default:
                $this->setOrder('position', 'ASC');
                break;
        }

        return $this;
    }

    /**
     * @param null $storeId
     * @return $this
     */
    public function addFrontendStoreFilter($storeId = null)
    {
        $storeIds = [\Magento\Store\Model\Store::DEFAULT_STORE_ID];
        if ($storeId) {
            $storeIds[] = (int) $storeId;
        }
        $this->addStoreFilter($storeIds);

        return $this;
    }
}
