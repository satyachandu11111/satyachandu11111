<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Scroll
 */


namespace Amasty\Scroll\Block;

class Init extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Amasty\Scroll\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Amasty\Scroll\Helper\Data $helper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\App\Request\Http $request,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->helper = $helper;
        $this->jsonEncoder = $jsonEncoder;
        $this->request = $request;
    }

    public function getHelper()
    {
        return $this->helper;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->helper->isEnabled();
    }

    /**
     * @return string
     */
    public function getProductsBlockSelector()
    {
        $originSelectors = $this->helper->getModuleConfig('advanced/product_container_group');

        //compatibility with Amasty_PromoBanners
        if ($originSelectors === null) {
            $selectors = ['.products.wrapper'];
        } else {
            $selectors = explode(',', $originSelectors);
        }
        foreach ($selectors as &$selector) {
            $selector = rtrim($selector);
            $selector .= ':not(.amasty-banners)';
        }

        return implode(',', $selectors);
    }

    /**
     * @return string
     */
    public function getConfig()
    {
        $currentPage = $this->request->getParam('p');
        if (!$currentPage) {
            $currentPage = 1;
        }

        $params = [
            'actionMode'                => $this->helper->getModuleConfig('general/loading'),
            'product_container'         => $this->getProductsBlockSelector(),
            'loadingImage'              => $this->getViewFileUrl(
                $this->helper->getModuleConfig('general/loading_icon')
            ),
            'pageNumbers'               => $this->helper->getModuleConfig('general/page_numbers'),
            'pageNumberContent'         => __('PAGE #'),
            'loadNextStyle'             => $this->helper->getModuleConfig('button/styles'),
            'loadingafterTextButton'    => $this->helper->getModuleConfig('button/label_after'),
            'loadingbeforeTextButton'   => $this->helper->getModuleConfig('button/label_before'),
            'progressbar'               => $this->helper->getModuleConfig('info'),
            'progressbarText'           => __('PAGE %1 of %2'),
            'current_page'              => $currentPage,
        ];

        return $this->jsonEncoder->encode($params);
    }
}
