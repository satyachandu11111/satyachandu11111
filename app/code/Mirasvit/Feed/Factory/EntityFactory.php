<?php

namespace Mirasvit\Feed\Factory;

use Mirasvit\Feed\Api\Factory\EntityFactoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Mirasvit\Feed\Model\TemplateFactory;
use Mirasvit\Feed\Model\RuleFactory;
use Mirasvit\Feed\Model\Dynamic\AttributeFactory;
use Mirasvit\Feed\Model\Dynamic\CategoryFactory;
use Mirasvit\Feed\Model\Dynamic\VariableFactory;

class EntityFactory implements EntityFactoryInterface
{
    public function __construct(
        Context $context,
        TemplateFactory $template,
        RuleFactory $rule,
        AttributeFactory $attribute,
        CategoryFactory $category,
        VariableFactory $variable
    ) {
        $this->context = $context;
        $this->template = $template;
        $this->rule = $rule;
        $this->attribute = $attribute;
        $this->category = $category;
        $this->variable = $variable;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityModelFactory($entityName)
    {
        switch ($entityName) {
            case 'template':
                $entityModel = $this->template->create();
                break;

            case 'rule':
                $entityModel = $this->rule->create();
                break;

            case 'dynamic_attribute':
                $entityModel = $this->attribute->create();
                break;

            case 'dynamic_category':
                $entityModel = $this->category->create();
                break;

            case 'dynamic_variable':
                $entityModel = $this->variable->create();
                break;

            default:
                $entityModel = '';
                break;
        }

        return $entityModel;
    }
}