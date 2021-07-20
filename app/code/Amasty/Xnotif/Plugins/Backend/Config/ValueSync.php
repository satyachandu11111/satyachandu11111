<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Xnotif
 */


namespace Amasty\Xnotif\Plugins\Backend\Config;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\Value;

class ValueSync
{
    const QTYFIELDS = [
            'amxnotif/admin_notifications/qty_below',
            'cataloginventory/item_options/notify_stock_qty',
        ];

    const SENDERS = [
            'amxnotif/admin_notifications/sender_email_identity',
            'amxnotif/admin_notifications/sender_email_identity_secondary',
            'amxnotif/admin_notifications/sender_email_identity_third',
        ];

    const EMAILS = [
            'amxnotif/admin_notifications/stock_alert_email',
            'amxnotif/admin_notifications/stock_alert_email_secondary',
            'amxnotif/admin_notifications/stock_alert_email_third'
        ];

    /**
     * @var WriterInterface
     */
    private $configWriter;

    public function __construct(
        WriterInterface $configWriter
    ) {
        $this->configWriter = $configWriter;
    }

    /**
     * @param Value $subject
     */
    public function beforeSave(Value $subject)
    {
        $this->syncValues($this::QTYFIELDS, $subject);
        $this->syncValues($this::SENDERS, $subject);
        $this->syncValues($this::EMAILS, $subject);
    }

    /**
     * @param array $syncFields
     * @param Value $subject
     */
    private function syncValues($syncFields, $subject)
    {
        if (in_array($subject->getPath(), $syncFields) && $subject->getOldValue() != $subject->getValue()) {
            foreach ($syncFields as $field) {
                if ($subject->getPath() != $field) {
                    $this->configWriter->save($field, $subject->getValue());
                }
            }
        }
    }
}
