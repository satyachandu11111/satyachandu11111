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
 * @package   mirasvit/module-email
 * @version   2.1.6
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Email\Ui\Campaign;

use Magento\Ui\Component\AbstractComponent;

class Overview extends AbstractComponent
{
    public function getComponentName()
    {
        return 'overview';
    }

    public function prepare()
    {
        $config = $this->getData('config');
        $data = $this->context->getDataProvider()->getData();

        if (isset($data['report'])) {
            $report = $this->addRates($data['report']);
            $config = array_merge($config, $report);
            $this->setData('config', $config);
        }

        parent::prepare();
    }

    /**
     * Add rate values based on qty values.
     *
     * @param int[] $report
     *
     * @return int[]
     */
    private function addRates($report)
    {
        $rateRelation = [
            'open_rate'   => ['email_qty', 'open_qty'],
            'click_rate'  => ['open_qty', 'click_qty'],
            'order_rate'  => ['click_qty', 'order_qty'],
            'review_rate' => ['click_qty', 'review_qty']
        ];

        foreach ($rateRelation as $rateKey => $relation) {
            if (isset($report[$relation[0]], $report[$relation[1]]) && $report[$relation[0]] && $report[$relation[1]]) {
                $report[$rateKey] = round(100 / $report[$relation[0]] * $report[$relation[1]]);
            } else {
                $report[$rateKey] = 0;
            }
        }

        return $report;
    }
}
