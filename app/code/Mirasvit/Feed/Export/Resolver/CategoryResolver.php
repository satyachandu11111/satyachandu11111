<?php
namespace Mirasvit\Feed\Export\Resolver;

use Magento\Catalog\Model\Category;

class CategoryResolver extends AbstractResolver
{
    /**
     * Cache of loaded categories
     *
     * @var array
     */
    protected static $categories = [];

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return [];
    }

    /**
     * Parent category model
     *
     * @param Category $category
     * @return Category
     */
    public function getParentCategory($category)
    {
        return $category->getParentCategory();
    }

    /**
     * Array of parent categories names
     *
     * @param Category $category
     * @return array
     */
    public function getPath($category)
    {
        $value = [];
        foreach ($category->getParentCategories() as $parent) {
            $value[$parent->getLevel()] = $parent->getName();
        }
        if (!count($value)) {
            $value[$category->getLevel()] = $category->getName();
        }

        ksort($value);

        return $value;
    }

    /**
     * Array of parent categories names (Including store root category)
     *
     * @param Category $category
     * @return array
     */
    public function getFullPath($category)
    {
        /** @var \Magento\Catalog\Model\Category $root */
        $root = $this->objectManager->create('Magento\Catalog\Model\Category')
            ->load($this->getFeed()->getStore()->getRootCategoryId());

        $value = $this->getPath($category);
        array_unshift($value, $root->getName());

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function toString($value, $key = null)
    {
        if (is_object($value) && $value instanceof Category) {
            return $value->getName();
        }

        return parent::toString($value, $key);
    }

    /**
     * {@inheritdoc}
     *
     * @param Category $object
     * @param string $key
     * @param array $args
     *
     * @return string
     */
    public function resolve($object, $key, $args = [])
    {
        $category = $this->getCategory($object);
        $result = parent::resolve($category, $key, $args);

        if ($result === false) {
            $result = $category->getData($key);
        }

        return $result;
    }

    /**
     * Return category model
     *
     * @param Category $object
     *
     * @return Category
     */
    protected function getCategory($object)
    {
        if (!isset(self::$categories[$object->getId()])) {
            self::$categories[$object->getId()] = $object->load($object->getId());
        }

        return self::$categories[$object->getId()];
    }
}
