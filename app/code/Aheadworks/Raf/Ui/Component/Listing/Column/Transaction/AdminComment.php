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
namespace Aheadworks\Raf\Ui\Component\Listing\Column\Transaction;

use Magento\Ui\Component\Listing\Columns\Column;
use Aheadworks\Raf\Model\Advocate\PriceFormatter;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Aheadworks\Raf\Model\Transaction\Comment\Processor as CommentProcessor;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Raf\Api\Data\TransactionEntityInterface;
use Aheadworks\Raf\Api\Data\TransactionEntityInterfaceFactory;
use Aheadworks\Raf\Model\Source\Transaction\Action as ActionSource;
use Magento\Framework\Escaper;

/**
 * Class AdminComment
 * @package Aheadworks\Raf\Ui\Component\Listing\Column\Transaction
 */
class AdminComment extends Column
{
    /**
     * @var PriceFormatter
     */
    private $priceFormatter;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var TransactionEntityInterfaceFactory
     */
    private $transactionEntityInterfaceFactory;

    /**
     * @var ActionSource
     */
    private $actionSource;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @var CommentProcessor
     */
    protected $commentProcessor;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param PriceFormatter $priceFormatter
     * @param CommentProcessor $commentProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param ActionSource $actionSource
     * @param TransactionEntityInterfaceFactory $transactionEntityInterfaceFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        PriceFormatter $priceFormatter,
        CommentProcessor $commentProcessor,
        DataObjectHelper $dataObjectHelper,
        ActionSource $actionSource,
        TransactionEntityInterfaceFactory $transactionEntityInterfaceFactory,
        Escaper $escaper,
        array $components = [],
        array $data = []
    ) {
        $this->transactionEntityInterfaceFactory = $transactionEntityInterfaceFactory;
        $this->priceFormatter = $priceFormatter;
        $this->commentProcessor = $commentProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->actionSource = $actionSource;
        $this->escaper = $escaper;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (in_array($item['action'], $this->actionSource->getActionListWithCommentPlaceholders())) {
                    $entities = [];
                    foreach ($item['entities'] as $entityArray) {
                        /** @var TransactionEntityInterface $entityObject */
                        $entityObject = $this->transactionEntityInterfaceFactory->create();
                        $this->dataObjectHelper->populateWithArray(
                            $entityObject,
                            $entityArray,
                            TransactionEntityInterface::class
                        );
                        $entities[] = $entityObject;
                    }
                    try {
                        $comment = $this->commentProcessor->renderComment($item['action'], $entities, true);
                    } catch (\Exception $exception) {
                        $comment = '';
                    }
                } else {
                    $comment = $this->escaper->escapeHtml($item['admin_comment']);
                }

                $item[$this->getData('name')] = $comment;
            }
            return $dataSource;
        }
    }
}
