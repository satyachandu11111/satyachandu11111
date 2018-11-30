<?php

namespace Mirasvit\Feed\Controller\Adminhtml\Dynamic\Attribute;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Mirasvit\Feed\Helper\Output as OutputHelper;

class Attribute extends Action
{
    /**
     * @var OutputHelper
     */
    protected $outputHelper;

    /**
     * @param OutputHelper $outputHelper
     * @param Context      $context
     */
    public function __construct(
        OutputHelper $outputHelper,
        Context $context
    ) {
        $this->outputHelper = $outputHelper;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $attribute = $this->getRequest()->getParam('attribute');

        $result = [
            'operators'     => $this->outputHelper->getAttributeOperators($attribute),
            'attributeType' => 'select',
            'values'        => $this->outputHelper->getAttributeValues($attribute),
        ];

        /** @var \Magento\Framework\App\Response\Http\Interceptor $response */
        $response = $this->getResponse();

        return $response
            ->representJson(json_encode($result));
    }

    public function _processUrlKeys()
    {
        return true;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mirasvit_Feed::feed_dynamic_attribute');
    }
}
