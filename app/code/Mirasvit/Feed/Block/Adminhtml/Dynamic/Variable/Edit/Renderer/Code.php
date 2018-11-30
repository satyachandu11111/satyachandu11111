<?php
namespace Mirasvit\Feed\Block\Adminhtml\Dynamic\Variable\Edit\Renderer;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Escaper;
use Magento\Framework\Registry;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;

class Code extends AbstractElement
{
    /**
     * @var LayoutInterface
     */
    protected $layout;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Fieldset
     */
    protected $fieldset;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        Fieldset $fieldset,
        Registry $registry,
        LayoutInterface $layout,
        Factory $factory,
        CollectionFactory $collectionFactory,
        Escaper $escaper
    ) {
        $this->fieldset = $fieldset;
        $this->registry = $registry;
        $this->layout = $layout;

        parent::__construct($factory, $collectionFactory, $escaper);
    }

    /**
     * {@inheritdoc}
     */
    public function toHtml()
    {
        return $this->layout
            ->createBlock('Magento\Backend\Block\Template')
            ->setData('php_code', $this->getVariable()->getPhpCode())
            ->setData('id', $this->getVariable()->getId())
            ->setTemplate('Mirasvit_Feed::dynamic/variable/edit/form.phtml')
            ->toHtml();
    }

    /**
     * @return \Mirasvit\Feed\Model\Dynamic\Variable
     */
    public function getVariable()
    {
        return $this->registry->registry('current_model');
    }
}
