<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Faq
 */


namespace Amasty\Faq\Ui\Component\Form\Question;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Form\Field;
use Amasty\Faq\Model\ResourceModel\Tag\Collection;

class Tags extends Field
{
    /**
     * @var Collection
     */
    private $tagCollection;

    /**
     * Field constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Collection $tagCollection
     * @param array|\Magento\Framework\View\Element\UiComponentInterface[] $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Collection $tagCollection,
        $components,
        array $data = []
    ) {
        $this->tagCollection = $tagCollection;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare component configuration
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepare()
    {
        $tags = $this->tagCollection->getItems();
        $tagsArray = [];
        foreach ($tags as $tag) {
            $tagsArray[] = $tag->getTitle();
        }
        $config = $this->getData('config');
        $config['tags'] = $tagsArray;
        $this->setData('config', $config);

        parent::prepare();
    }
}
