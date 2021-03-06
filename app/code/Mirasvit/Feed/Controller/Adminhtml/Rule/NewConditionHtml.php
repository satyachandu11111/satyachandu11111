<?php

namespace Mirasvit\Feed\Controller\Adminhtml\Rule;

use Magento\Framework\Controller\ResultFactory;

/**
 * @SuppressWarnings(PHPMD)
 * @codingStandardsIgnoreFile
 */
class NewConditionHtml extends \Mirasvit\Feed\Controller\Adminhtml\Rule
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = $this->context->getObjectManager()->create($type)
            ->setId($id)
            ->setType($type)
            ->setRule($this->ruleFactory->create())
            ->setPrefix('conditions');

        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof \Mirasvit\Feed\Model\Rule\Condition\Product\ParentProduct) {
            $model->setIsParent(true);
        }

        if ($model instanceof \Magento\Rule\Model\Condition\AbstractCondition) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }
}
