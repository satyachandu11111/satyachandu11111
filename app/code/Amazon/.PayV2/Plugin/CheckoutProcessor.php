<?php
/**
 * Copyright © Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 *  http://aws.amazon.com/apache2.0
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */
namespace Amazon\PayV2\Plugin;

class CheckoutProcessor
{
    /**
     * @var \Amazon\PayV2\Model\AmazonConfig
     */
    private $amazonConfig;

    /**
     * @var  \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * CheckoutProcessor constructor.
     * @param \Amazon\PayV2\Model\AmazonConfig $amazonConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Amazon\PayV2\Model\AmazonConfig $amazonConfig,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->amazonConfig = $amazonConfig;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Checkout LayoutProcessor after process plugin.
     *
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $processor
     * @param array $jsLayout
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterProcess(\Magento\Checkout\Block\Checkout\LayoutProcessor $processor, $jsLayout)
    {
        $shippingConfig = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress'];
        $paymentConfig = &$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
        ['children']['payment'];

        if ($this->amazonConfig->isEnabled()) {
            $shippingConfig['component'] = 'Amazon_PayV2/js/view/shipping';
            $shippingConfig['children']['customer-email']['component'] = 'Amazon_PayV2/js/view/form/element/email';
            $shippingConfig['children']['address-list']['component'] = 'Amazon_PayV2/js/view/shipping-address/list';
            $shippingConfig['children']['address-list']['rendererTemplates']['new-customer-address']
            ['component'] = 'Amazon_PayV2/js/view/shipping-address/address-renderer/default';

            $shippingConfig['children']['shipping-address-fieldset']['children']['inline-form-manipulator'] = [
                'component' => 'Amazon_PayV2/js/view/shipping-address/inline-form',
                'sortOrder' => 1000,
            ];

            $paymentConfig['children']['payments-list']['component'] = 'Amazon_PayV2/js/view/payment/list';

            unset($paymentConfig['children']['renders']['children']['amazonlogin']); // legacy

            // "Show Amazon Pay in payment methods"?
            if (!$this->amazonConfig->isPayButtonAvailableAsPaymentMethod()) {
                unset($paymentConfig['children']['renders']['children']['amazon_payment_v2-button']);
            }

        } else {
            unset($shippingConfig['children']['customer-email']['children']['amazon-payv2-button-region']);
            unset($shippingConfig['children']['before-form']['children']['amazon-payv2-address']);
            unset($paymentConfig['children']['renders']['children']['amazon_payment_v2-method']);
            unset($paymentConfig['children']['renders']['children']['amazon_payment_v2-button']);
        }

        return $jsLayout;
    }
}
