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
namespace Aheadworks\Raf\Model\Transaction\Comment\Processor;

use Aheadworks\Raf\Model\Source\Transaction\EntityType;
use Magento\Framework\UrlInterface;
use Magento\Framework\Phrase\Renderer\Placeholder;

/**
 * Class Order
 *
 * @package Aheadworks\Raf\Model\Transaction\Comment\Processor
 */
class OrderProcessor implements ProcessorInterface
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var Placeholder
     */
    private $placeholder;

    /**
     * @param UrlInterface $urlBuilder
     * @param Placeholder $placeholder
     */
    public function __construct(
        UrlInterface $urlBuilder,
        Placeholder $placeholder
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->placeholder = $placeholder;
    }

    /**
     * {@inheritdoc}
     */
    public function renderComment($entities, $isUrl)
    {
        $arguments = [];
        foreach ($entities as $entity) {
            if ($entity->getEntityType() != EntityType::ORDER_ID) {
                continue;
            }

            $orderIncrementId = '#' . $entity->getEntityLabel();
            if ($isUrl) {
                $url = $this->urlBuilder->getUrl('sales/order/view', ['order_id' => $entity->getEntityId()]);
                $orderIncrementId = $this->placeholder->render(
                    ['<a href="%order_url">%order_id</a>'],
                    ['order_id' => $orderIncrementId, 'order_url' => $url]
                );
            }
            $arguments['order_id'] = $orderIncrementId;
        }

        return $arguments;
    }
}
