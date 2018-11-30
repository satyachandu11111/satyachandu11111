<?php
namespace Mirasvit\Feed\Export\Step;

use Magento\Framework\App\ResourceConnection;
use Mirasvit\Feed\Export\Context;
use Mirasvit\Feed\Model\RuleFactory;

class Filtration extends AbstractStep
{
    /**
     * @var RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var StepFactory
     */
    protected $stepFactory;

    public function __construct(
        RuleFactory $ruleFactory,
        ResourceConnection $resource,
        Context $context
    ) {
        $this->ruleFactory = $ruleFactory;
        $this->resource = $resource;
        $this->stepFactory = $context->getStepFactory();

        parent::__construct($context);
    }

    /**
     * Add assigned rules as sub steps
     * {@inheritdoc}
     */
    public function beforeExecute()
    {
        if ($this->context->isTestMode()) {
            return parent::beforeExecute();
        }

        foreach ($this->context->getFeed()->getRuleIds() as $ruleId) {
            $rule = $this->ruleFactory->create()->load($ruleId);
            $this->addStep(
                $this->stepFactory->create('Filtration\Rule', ['data' => ['rule_id' => $ruleId]])
                    ->setName($rule->getName())
            );
        }

        return parent::beforeExecute();
    }

    /**
     * Merge rules
     *
     * {@inheritdoc}
     */
    public function afterExecute()
    {
        $feed = $this->context->getFeed();

        $connection = $this->resource->getConnection();
        $feedId = intval($feed->getId());

        $columns = [
            'product_id' => 'product_id',
            'feed_id'    => new \Zend_Db_Expr($feedId),
            'is_new'     => new \Zend_Db_Expr('1'),
        ];

        $select = $connection->select();
        $select->from(['main_table' => $this->resource->getTableName('mst_feed_rule_product')], $columns)
            ->group(['main_table.product_id'])
            ->where('main_table.rule_id IN (?)', $feed->getRuleIds())
            ->having('count(main_table.product_id) = ?', count($feed->getRuleIds()))
            ->useStraightJoin();

        $feedProductTable = $this->resource->getTableName('mst_feed_feed_product');

        $connection->delete($feedProductTable, ['feed_id = ' . intval($feedId)]);

        $insertQuery = $select->insertFromSelect($feedProductTable, array_keys($columns));
        $connection->query($insertQuery);
    }
}
