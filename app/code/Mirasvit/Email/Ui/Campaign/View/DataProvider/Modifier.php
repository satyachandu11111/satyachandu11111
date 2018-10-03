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
 * @package   mirasvit/module-email
 * @version   2.1.11
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Email\Ui\Campaign\View\DataProvider;

use Magento\Framework\UrlInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Mirasvit\Email\Api\Data\CampaignInterface;
use Mirasvit\Email\Api\Data\ChainInterface;
use Mirasvit\Email\Api\Data\QueueInterface;
use Mirasvit\Email\Api\Data\TriggerInterface;
use Mirasvit\Email\Api\Repository\QueueRepositoryInterface;
use Mirasvit\Email\Api\Repository\TriggerRepositoryInterface;
use Mirasvit\EmailDesigner\Api\Data\TemplateInterface;
use Mirasvit\Email\Repository\EventRepository;
use Mirasvit\Event\Model\Rule;
use Mirasvit\Event\Model\RuleFactory;

class Modifier implements ModifierInterface
{
    private $triggerRepository;

    private $eventRepository;

    private $urlBuilder;
    /**
     * @var RuleFactory
     */
    private $ruleFactory;
    /**
     * @var PoolInterface
     */
    private $modifiers;
    /**
     * @var QueueRepositoryInterface
     */
    private $queueRepository;

    public function __construct(
        QueueRepositoryInterface $queueRepository,
        RuleFactory $ruleFactory,
        TriggerRepositoryInterface $triggerRepository,
        EventRepository $eventRepository,
        UrlInterface $urlBuilder,
        PoolInterface $modifiers
    ) {
        $this->triggerRepository = $triggerRepository;
        $this->eventRepository = $eventRepository;
        $this->urlBuilder = $urlBuilder;
        $this->ruleFactory = $ruleFactory;
        $this->modifiers = $modifiers;
        $this->queueRepository = $queueRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function modifyData(array $data)
    {
        $data['triggers'] = $this->getTriggers($data);
        $data['report']   = $this->collectCampaignOverview($data);

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function getTriggers($data)
    {
        $triggers = $this->triggerRepository->getCollection();
        $triggers->addFieldToFilter(CampaignInterface::ID, $data[CampaignInterface::ID]);

        return $this->getTriggersData($triggers);
    }

    /**
     * @param TriggerInterface[]|\Mirasvit\Email\Model\ResourceModel\Trigger\Collection $triggers
     *
     * @return array
     */
    private function getTriggersData($triggers)
    {
        $triggersData = [];
        foreach ($triggers as $trigger) {
            $triggerData = [
                'id_field_name'               => TriggerInterface::ID,
                TriggerInterface::ID          => $trigger->getId(),
                TriggerInterface::TITLE       => $trigger->getTitle(),
                TriggerInterface::IS_ACTIVE   => $trigger->getIsActive(),
                TriggerInterface::DESCRIPTION => $trigger->getDescription(),
                TriggerInterface::RULE        => $this->getRule($trigger)->toString(),
                'report'                      => [],
                'chain'                       => [],
                'duplicate_url'               => $this->urlBuilder->getUrl(
                    'email/trigger/move',
                    [
                        '_current' => 1,
                        TriggerInterface::ID => $trigger->getId(),
                        '_query' => ['campaigns' => [$trigger->getCampaignId()]],
                    ]
                ),
                'view_url'                    => $this->urlBuilder->getUrl(
                    'email/trigger/edit',
                    [TriggerInterface::ID => $trigger->getId()]
                ),
                'delete_url'                    => $this->urlBuilder->getUrl(
                    'email/trigger/delete',
                    [TriggerInterface::ID => $trigger->getId()]
                ),
                'toggle_url'                    => $this->urlBuilder->getUrl(
                    'email/trigger/toggle',
                    [TriggerInterface::ID => $trigger->getId()]
                ),
            ];

            if ($trigger->getEvent()) {
                $triggerData[TriggerInterface::EVENT] = $this->eventRepository->getInstance($trigger->getEvent())
                    ->getEvents()[$trigger->getEvent()];
            }

            foreach ($this->modifiers->getModifiersInstances() as $modifier) {
                $triggerData = $modifier->modifyData($triggerData);
            }

            $triggersData[] = $this->addChainData($triggerData, $trigger);
        }

        return $triggersData;
    }

    /**
     * @param array            $triggerData
     * @param TriggerInterface $trigger
     *
     * @return array
     */
    private function addChainData($triggerData, TriggerInterface $trigger)
    {
        foreach ($trigger->getChainCollection() as $chain) {
            $chainData = [
                'id_field_name' => QueueInterface::ID,
                ChainInterface::ID => $chain->getId(),
                TemplateInterface::TITLE  => $chain->getTemplate()
                    ? $chain->getTemplate()->getTitle()
                    : __('No Template Selected'),
                'info'                    => $chain->toString(),
                'report'                  => [],
                'delete_url'              => $this->urlBuilder->getUrl(
                    'email/chain/delete',
                    [ChainInterface::ID => $chain->getId()]
                ),
                'duplicate_url'           => $this->urlBuilder->getUrl(
                    'email/chain/duplicate',
                    [
                        '_current' => 1,
                        ChainInterface::ID => $chain->getId(),
                    ]
                ),
            ];

            $chainData['queue_id'] = ['in' => $this->getQueueIds($chain->getId())];
            foreach ($this->modifiers->getModifiersInstances() as $modifier) {
                $chainData = $modifier->modifyData($chainData);
            }

            $chainData['report']['pending'] = $this->countPendingEmails($chainData);
            $triggerData['chain'][] = $chainData;

            if (isset($triggerData['report']['pending'])) {
                $triggerData['report']['pending'] += $chainData['report']['pending'];
            } else {
                $triggerData['report']['pending'] = $chainData['report']['pending'];
            }
        }

        return $triggerData;
    }

    /**
     * @param int $chainId
     *
     * @return int[]
     */
    private function getQueueIds($chainId)
    {
        return $this->queueRepository->getCollection()
            ->addFieldToFilter(ChainInterface::ID, $chainId)
            ->getColumnValues(QueueInterface::ID);
    }

    /**
     * @param array $data
     *
     * @return int
     */
    private function countPendingEmails($data)
    {
        $queues = $this->queueRepository->getCollection();
        $queues->addFieldToFilter(ChainInterface::ID, $data[ChainInterface::ID])
            ->addFieldToFilter(QueueInterface::STATUS, QueueInterface::STATUS_PENDING);

        return $queues->count();
    }

    /**
     * Get trigger rule with loaded conditions.
     *
     * @param TriggerInterface $trigger
     *
     * @return Rule
     */
    private function getRule(TriggerInterface $trigger)
    {
        /** @var Rule $rule */
        $rule = $this->ruleFactory->create();
        $rule->loadPost($trigger->getRule());

        return $rule;
    }

    /**
     * @param array $data
     *
     * @return int[]
     */
    private function collectCampaignOverview(array $data)
    {
        $totals = [];
        if (!count($data['triggers'])) {
            $totals['visible'] = false;
        }

        foreach ($data['triggers'] as $trigger) {
            foreach($trigger['report'] as $key => $value) {
                if (isset($totals[$key])) {
                    $totals[$key] += $value;
                } else {
                    $totals[$key] = $value;
                }
            }
        }

        return $totals;
    }
}
