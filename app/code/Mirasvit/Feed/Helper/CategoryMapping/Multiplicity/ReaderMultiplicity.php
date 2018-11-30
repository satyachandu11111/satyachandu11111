<?php

namespace Mirasvit\Feed\Helper\CategoryMapping\Multiplicity;

use Mirasvit\Feed\Helper\CategoryMapping\ReaderInterface;

abstract class ReaderMultiplicity implements ReaderMultiplicityInterface
{
    /**
     * @var array
     */
    protected $items = [];

    /**
     * {@inheritdoc}
     */
    public function addItem(ReaderInterface $item)
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public abstract function findAll();
}