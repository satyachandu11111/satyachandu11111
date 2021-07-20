<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Raf
 * @version    1.1.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Raf\Observer\ReferralLinkChecker;

use Aheadworks\Raf\Model\Advocate\Url;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;

/**
 * Class Redirect
 *
 * @package Aheadworks\Raf\Observer\ReferralLinkChecker
 */
class Redirect
{
    /**
     * @var RedirectInterface
     */
    private $redirect;

    /**
     * @param RedirectInterface $redirect
     */
    public function __construct(
        RedirectInterface $redirect
    ) {
        $this->redirect = $redirect;
    }

    /**
     * Create redirect
     *
     * @param $observer
     * @return void
     */
    public function createRedirect($observer)
    {
        /** @var RequestInterface $request */
        $request = $observer->getEvent()->getRequest();
        /** @var \Magento\Framework\App\Action\Action $controller */
        $controller = $observer->getControllerAction();

        $this->redirect->redirect(
            $controller->getResponse(),
            $this->prepareRequestPathForRedirect($request),
            $this->prepareRequestParamsForRedirect($request)
        );
    }

    /**
     * Prepare request path for redirect
     *
     * @param RequestInterface $request
     * @return string
     */
    private function prepareRequestPathForRedirect($request)
    {
        $path = implode('/', [$request->getModuleName(), $request->getControllerName(), $request->getActionName()]);

        return $path;
    }

    /**
     * Prepare request path for redirect
     *
     * @param RequestInterface $request
     * @return array
     */
    private function prepareRequestParamsForRedirect($request)
    {
        $params = $request->getParams();

        unset($params[Url::REFERRAL_PARAM]);
        $params['_use_rewrite'] = true;

        return $params;
    }
}
