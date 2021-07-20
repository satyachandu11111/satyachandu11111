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
namespace Aheadworks\Raf\Ui\Component\Form\Advocate;

use Magento\Ui\Component\Form\Field;

/**
 * Class CustomerInfo
 * @package Aheadworks\Raf\Ui\Component\Form\Advocate
 */
class CustomerInfo extends Field
{
    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        parent::prepareDataSource($dataSource);

        if ($customerId = $dataSource['data']['customer_id']) {
            $dataSource['data']['customer_name_url'] = $this->getUrl(
                'customer/index/edit',
                ['id' => $customerId]
            );
        }
        return $dataSource;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    private function getUrl($route = '', $params = [])
    {
        return $this->getContext()->getUrl($route, $params);
    }
}
