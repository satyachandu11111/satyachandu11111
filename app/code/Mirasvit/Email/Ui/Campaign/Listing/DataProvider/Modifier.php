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



namespace Mirasvit\Email\Ui\Campaign\Listing\DataProvider;

use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Mirasvit\Email\Api\Data\CampaignInterface;
use Mirasvit\Email\Api\Data\TriggerInterface;
use Mirasvit\Email\Api\Repository\TriggerRepositoryInterface;

class Modifier implements ModifierInterface
{
    private $triggerRepository;

    private $urlBuilder;
    /**
     * @var ArrayManager
     */
    private $arrayManager;
    /**
     * @var PoolInterface
     */
    private $modifiers;

    public function __construct(
        ArrayManager $arrayManager,
        TriggerRepositoryInterface $triggerRepository,
        UrlInterface $urlBuilder,
        PoolInterface $modifiers
    ) {
        $this->triggerRepository = $triggerRepository;
        $this->urlBuilder = $urlBuilder;
        $this->arrayManager = $arrayManager;
        $this->modifiers = $modifiers;
    }

    /**
     * {@inheritDoc}
     */
    public function modifyData(array $data)
    {
        $data['triggers'] = $this->getTriggers($data);
        $data['report']   = $this->collectCampaignOverview($data);
        $data['view_url'] = $this->urlBuilder->getUrl(
            'email/campaign/view',
            [CampaignInterface::ID => $data[CampaignInterface::ID]]
        );
        $data['delete_url'] = $this->urlBuilder->getUrl(
            'email/campaign/delete',
            [CampaignInterface::ID => $data[CampaignInterface::ID]]
        );
        $data['duplicate_url'] = $this->urlBuilder->getUrl(
            'email/campaign/duplicate',
            [CampaignInterface::ID => $data[CampaignInterface::ID]]
        );

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
    private function getTriggers(array $data)
    {
        $triggersData = [];
        $triggers     = $this->triggerRepository->getCollection();
        $triggers->addFieldToFilter(CampaignInterface::ID, $data[CampaignInterface::ID]);

        foreach ($triggers as $trigger) {
            $triggerData = [
                'id_field_name'             => TriggerInterface::ID,
                TriggerInterface::ID        => $trigger->getId(),
                TriggerInterface::TITLE     => $trigger->getTitle(),
                TriggerInterface::IS_ACTIVE => $trigger->getIsActive(),
                'report'                    => [],
                'view_url'                  => $this->urlBuilder->getUrl(
                    'email/campaign/view',
                    [
                        CampaignInterface::ID => $trigger->getCampaignId(),
                        '_fragment'           => TriggerInterface::ID .'_'. $trigger->getId()
                    ]
                ),
            ];

            foreach ($this->modifiers->getModifiersInstances() as $modifier) {
                $triggerData = $modifier->modifyData($triggerData);
            }

            $triggersData[] = $triggerData;
        }

        return $triggersData;
    }

    /**
     * @param array $data
     *
     * @return int[]
     */
    private function collectCampaignOverview(array $data)
    {
        $totals = [];
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
