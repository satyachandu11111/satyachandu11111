<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-search
 * @version   1.0.117
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Search\Plugin\TemplateMonster\AjaxCatalog\Helper\Catalog\View\ContentAjaxResponse;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Json\Helper\Data;

class GetAjaxSearchResult
{
    /**
     * @var PageFactory
     */
    private $pageFactory;

     /**
     * @var Data
     */
    private $helperData;

    public function __construct(
    	PageFactory $pageFactory,
    	Data $helperData
    ){
        $this->pageFactory = $pageFactory;
        $this->helperData = $helperData;
    }

    public function aroundGetAjaxSearchResult($subject, $proceed, $subjectParam, $proceedParam)
    {
        $response = $subjectParam->getResponse();
        $page = $this->pageFactory->create();
        $result = [];

        try {
            $result['content'] = $page->getLayout()->renderElement('content');
            $result['layer'] = $page->getLayout()->renderElement('sidebar.main');
        } catch (\Exception $e) {
            $result['error'] = true;
            $result['message'] = 'Can not finish request';
        }

        return $response->representJson(	
            $this->helperData->jsonEncode($result)
        );
    }
}
