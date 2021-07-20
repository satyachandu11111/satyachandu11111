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
namespace Aheadworks\Raf\Model\Transaction\Comment;

use Aheadworks\Raf\Api\Data\TransactionEntityInterface;
use Aheadworks\Raf\Model\Transaction\Comment\Metadata\CommentMetadataPool;
use Aheadworks\Raf\Model\Transaction\Comment\Processor\ProcessorInterface;
use Magento\Framework\Phrase\Renderer\Placeholder;
use Aheadworks\Raf\Model\Source\Transaction\Action as ActionSource;

/**
 * Class Processor
 *
 * @package Aheadworks\Raf\Model\Transaction\Comment
 */
class Processor
{
    /**
     * @var CommentMetadataPool
     */
    private $commentMetadataPool;

    /**
     * @var Placeholder
     */
    private $placeholder;

    /**
     * @var ProcessorInterface[]
     */
    private $processors;

    /**
     * @var ActionSource
     */
    protected $actionSource;

    /**
     * @param CommentMetadataPool $commentMetadataPool
     * @param Placeholder $placeholder
     * @param ProcessorInterface[] $processors
     */
    public function __construct(
        CommentMetadataPool $commentMetadataPool,
        Placeholder $placeholder,
        ActionSource $actionSource,
        array $processors
    ) {
        $this->commentMetadataPool = $commentMetadataPool;
        $this->placeholder = $placeholder;
        $this->actionSource = $actionSource;
        $this->processors = $processors;
    }

    /**
     * Retrieve comment placeholder
     *
     * @param string $commentType
     * @return string
     * @throws \Exception
     */
    public function getPlaceholder($commentType)
    {
        if (in_array($commentType, $this->actionSource->getActionListWithCommentPlaceholders())) {
            return $this->commentMetadataPool->getMetadata($commentType)->getPlaceholder();
        }
        return '';
    }

    /**
     * Render comment
     *
     * @param string $commentType
     * @param TransactionEntityInterface[] $entities
     * @param bool $isUrl
     * @return string
     * @throws \Exception
     */
    public function renderComment($commentType, $entities, $isUrl)
    {
        if (in_array($commentType, $this->actionSource->getActionListWithCommentPlaceholders())) {
            $arguments = [];
            foreach ($this->processors as $processor) {
                $arguments = array_merge($arguments, $processor->renderComment($entities, $isUrl));
            }
            return $this->placeholder->render([$this->getPlaceholder($commentType)], $arguments);
        }
        return '';
    }
}
