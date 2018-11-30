<?php
namespace Mirasvit\Feed\Block\Adminhtml\Dynamic\Category\Edit\Renderer;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Escaper;
use Magento\Framework\Registry;

class Mapping extends AbstractElement
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
     * {@inheritdoc}
     */
    public function __construct(
        Registry $registry,
        LayoutInterface $layout,
        Factory $factory,
        CollectionFactory $collectionFactory,
        Escaper $escaper
    ) {
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
            ->setData('js_config', $this->getJsConfig())
            ->setTemplate('Mirasvit_Feed::dynamic/category/edit/form.phtml')
            ->toHtml();
    }

    /**
     * @return array
     */
    public function getJsConfig()
    {
        return [
            "*" => [
                'Magento_Ui/js/core/app' => [
                    'components' => [
                        'dynamic_category'        => [
                            'component' => 'Mirasvit_Feed/js/dynamic/category',
                            'config'    => [
                                'mapping' => $this->getCategory()->getMapping(),
                            ]
                        ],
                        'dynamic_category_search' => [
                            'component' => 'Mirasvit_Feed/js/dynamic/category/search',
                            'config'    => []
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @return \Mirasvit\Feed\Model\Dynamic\Category
     */
    public function getCategory()
    {
        return $this->registry->registry('current_model');
    }
}
