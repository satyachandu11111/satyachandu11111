<?php
namespace Mirasvit\Feed\Plugin;

use Magento\Framework\App\FrontControllerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http as ResponseHttp;
use Mirasvit\Feed\Model\ReportFactory;

/**
 */
class FrontControllerPlugin
{
    /**
     * @var ReportFactory
     */
    private $reportFactory;

    public function __construct(
        ReportFactory $reportFactory
    ) {
        $this->reportFactory = $reportFactory;
    }

    /**
     * @param FrontControllerInterface $subject
     * @param RequestInterface         $request
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeDispatch(FrontControllerInterface $subject, RequestInterface $request)
    {
        $feedId = $request->getParam('ff');
        $productId = $request->getParam('fp');

        if ($feedId && $productId) {
            $this->reportFactory->create()
                ->addClick($this->getVisitorIdentifier($request), $feedId, $productId);
        }
    }

    /**
     * @param RequestInterface $request
     * @return string
     */
    private function getVisitorIdentifier(RequestInterface $request)
    {
        if (isset($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }

        return $request->getCookie('PHPSESSID', '');
    }
}
