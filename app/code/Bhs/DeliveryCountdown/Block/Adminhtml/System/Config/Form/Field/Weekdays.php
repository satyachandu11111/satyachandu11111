<?php
namespace Bhs\DeliveryCountdown\Block\Adminhtml\System\Config\Form\Field;

class Weekdays implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Framework\Locale\ListsInterface
     */
    protected $_localeLists;

    /**
     * @param \Magento\Framework\Locale\ListsInterface $localeLists
     */
    public function __construct(\Magento\Framework\Locale\ListsInterface $localeLists)
    {
        $this->_localeLists = $localeLists;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];

         $result = array([
                'value' => 7,
                'label' => 'Sunday'
            ],[
                'value' => 1,
                'label' => 'Monday'
            ],[
                'value' => 2,
                'label' => 'Tuesday'
            ],[
                'value' => 3,
                'label' => 'Wednesday'
            ],[
                'value' => 4,
                'label' => 'Thursday'
            ],[
                'value' => 5,
                'label' => 'Friday'
            ],[
                'value' => 6,
                'label' => 'Saturday'
            ]);        

        return $result;
    }
}
