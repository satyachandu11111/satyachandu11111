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



namespace Mirasvit\Email\Ui\Campaign\Listing\Column;


use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Mirasvit\Email\Api\Data\CampaignInterface;
use Mirasvit\Email\Api\Repository\CampaignRepositoryInterface;

class Info extends AbstractColumn
{
    /**
     * @var CampaignRepositoryInterface
     */
    private $campaignRepository;

    public function __construct(
        CampaignRepositoryInterface $campaignRepository,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components,
        array $data
    ) {
        $this->campaignRepository = $campaignRepository;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareItem(array $item)
    {
        $result = [];
        $campaign = $this->campaignRepository->get($item[CampaignInterface::ID]);

        $result[] = "{$campaign->getTitle()}";

        return implode($result);
    }
}
