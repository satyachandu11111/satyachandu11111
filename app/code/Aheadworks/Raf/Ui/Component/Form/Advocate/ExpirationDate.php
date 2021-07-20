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

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Form\Field;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class ExpirationDate
 * @package Aheadworks\Raf\Ui\Component\Form\Advocate
 */
class ExpirationDate extends Field
{
    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param TimezoneInterface $timezone,
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        TimezoneInterface $timezone,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->timezone = $timezone;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        parent::prepareDataSource($dataSource);

        $expirationDateField = $this->getData('name');
        $expirationDateValue = isset($dataSource['data'][$expirationDateField])
            ? $dataSource['data'][$expirationDateField]
            : null;

        if ($expirationDateValue) {
            $date = $this->timezone->date(new \DateTime($expirationDateValue));
            if (isset($this->getConfiguration()['timezone']) && !$this->getConfiguration()['timezone']) {
                $date = new \DateTime($expirationDateValue);
            }
            if (!$dataSource['data']['cumulative_amount_orig']) {
                $dataSource['data'][$expirationDateField] = __('N/A');
            } else {
                $dataSource['data'][$expirationDateField] = $date->setTime(0, 0, 0)->format('M d, Y');
            }
        } else {
            $dataSource['data'][$expirationDateField] = __('N/A');
        }
        return $dataSource;
    }
}
