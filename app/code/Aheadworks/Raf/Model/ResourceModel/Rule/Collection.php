<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Raf
 * @version    1.1.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Raf\Model\ResourceModel\Rule;

use Aheadworks\Raf\Model\ResourceModel\AbstractCollection;
use Aheadworks\Raf\Model\ResourceModel\Rule as ResourceRule;
use Aheadworks\Raf\Model\Rule;
use Aheadworks\Raf\Api\Data\RuleInterface;

/**
 * Class Collection
 * @package Aheadworks\Raf\Model\ResourceModel\Rule
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Rule::class, ResourceRule::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        $this->attachRelationTable(
            'aw_raf_rule_website',
            'id',
            'rule_id',
            'website_id',
            'website_ids'
        );
        return parent::_afterLoad();
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderFiltersBefore()
    {
        $this->joinLinkageTable(
            'aw_raf_rule_website',
            'id',
            'rule_id',
            'website_ids',
            'website_id'
        );
        parent::_renderFiltersBefore();
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        $fieldsToProcess = $this->processAddFieldToFilter($field, $condition);

        if (!empty($fieldsToProcess)) {
            return parent::addFieldToFilter($fieldsToProcess, $condition);
        }

        return $this;
    }

    /**
     * Process adding fields to filter
     *
     * @param string|array $field
     * @param null|string|array $condition
     * @return array|string
     */
    private function processAddFieldToFilter($field, $condition = null)
    {
        $fieldsToProcess = null;
        if (is_array($field)) {
            $fieldsToProcess = [];
            foreach ($field as $fieldName) {
                if ($this->isNeedToApplyPublicFilterToField($fieldName)) {
                    $this->addFilter($fieldName, $condition, 'public');
                } else {
                    $fieldsToProcess[] = $fieldName;
                }
            }
        } else {
            if ($this->isNeedToApplyPublicFilterToField($field)) {
                $this->addFilter($field, $condition, 'public');
            } else {
                $fieldsToProcess = $field;
            }
        }

        return $fieldsToProcess;
    }

    /**
     * Check if need to apply public filter instead of native logic
     *
     * @param string $fieldName
     * @return bool
     */
    private function isNeedToApplyPublicFilterToField($fieldName)
    {
        return (in_array($fieldName, [RuleInterface::WEBSITE_IDS]));
    }
}
