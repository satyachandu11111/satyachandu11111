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
 * @package   mirasvit/module-search-ultimate
 * @version   2.0.33
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\SearchElastic\Plugin;

use Mirasvit\Search\Repository\ScoreRuleRepository;
use Mirasvit\Search\Model\ScoreRule\Indexer\ScoreRuleIndexer;
use Magento\Elasticsearch\Model\Adapter\Elasticsearch;

/**
 * @SuppressWarnings(PHPMD)
 * @see \Magento\Elasticsearch\Model\Adapter\Elasticsearch::addDocs()
 */
class PutScoreBoostBeforeAddDocsPlugin
{
    private $scoreRuleRepository;
    private $scoreRuleIndexer;

    public function __construct(
        ScoreRuleRepository $scoreRuleRepository,
        ScoreRuleIndexer $scoreRuleIndexer
    ) {
        $this->scoreRuleRepository     = $scoreRuleRepository;
        $this->scoreRuleIndexer     = $scoreRuleIndexer;
    }

    const EMPTY_SUM_VALUE = 0;
    const EMPTY_MULTIPLY_VALUE = 1;
    const SUM_ATTRIBUTE = 'mst_score_sum';
    const MULTIPLY_ATTRIBUTE = 'mst_score_multiply';

    public function beforeAddDocs(Elasticsearch $subject, array $docs, int $storeId, string $mappedIndexerId)
    {
        $productIds = array_keys($docs);
        $ids = [];
        $allowedScoreRules = [];
        $scoreFactors = [];
        
        foreach ($this->scoreRuleRepository->getCollection() as $scoreRule) {
            if (in_array($storeId, $scoreRule->getStoreIds()) || empty($scoreRule->getStoreIds())) { 
                $allowedScoreRules = $scoreRule->getStoreIds();
            }
        }

        foreach ($allowedScoreRules as $scoreRules) {
            $ids = $scoreRule->getRule()->getMatchingProductIds($productIds, intval ($storeId == 0 ? 1 : $storeId));
            $scoreFactors = $this->scoreRuleIndexer->getScoreFactors($scoreRule, $ids);
        }

        foreach ($docs as $productId => $data) {
            $docs[$productId][self::SUM_ATTRIBUTE] = self::EMPTY_SUM_VALUE;
            $docs[$productId][self::MULTIPLY_ATTRIBUTE] = self::EMPTY_MULTIPLY_VALUE;

            if (isset($scoreFactors[$productId])) {
                if (strripos($scoreFactors[$productId], '*') !== false) {
                    $docs[$productId][self::MULTIPLY_ATTRIBUTE] = (int) str_replace('*', '', $scoreFactors[$productId]);
                } else {
                    $docs[$productId][self::SUM_ATTRIBUTE] = (int) str_replace('+', '', $scoreFactors[$productId]);
                }
            }
        }

        return [$docs, $storeId, $mappedIndexerId];
    }
}
