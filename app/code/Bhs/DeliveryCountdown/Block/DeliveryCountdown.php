<?php 
namespace Bhs\DeliveryCountdown\Block;

class DeliveryCountdown extends \Magento\Framework\View\Element\Template 
{
	protected $scopeConfig;
    protected $_registry;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,


        \Magento\Framework\Stdlib\DateTime\DateTime $date  // For date and time functions

    ) {
        $this->date = $date;
		$this->scopeConfig = $scopeConfig;
        parent::__construct(
            $context
        );
    }


    public function getCurrentSystemDate() {
        $date = strtotime($this->date->gmtDate('H:i:s'));
        return $date;
    }


	public function getDeliveryDate() {
		$deliverydaysadmin = $this->scopeConfig->getValue('deliverycountdown/general/deliverytime', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$currenttime = $this->getCurrentTime();
		//$deliverydate = date('d/m/Y', strtotime(date('d-m-Y') . ' + ' . $deliverydaysadmin . ' weekdays'));
        $deliverydate = date('Y-m-d', strtotime(date('d-m-Y') . ' + ' . $deliverydaysadmin . ' weekdays'));

        return $deliverydate;
	}

	// get excluded/ OFF days
    public function getOffDates() {
        $offDaysString = $this->scopeConfig->getValue('deliverycountdown/general/mapping', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

            $Offdates =array();
            //  $strlenth = strlen($offDaysString);
            //   if( $strlenth > 1 ) {

            //$Offdates = array();
           $needle = 's:10:"';
           $count = substr_count($offDaysString, $needle);

            $position = 0;
            for ($i = 0; $i < $count; $i++) {

                $pos = strpos($offDaysString, $needle, $position);
                $position = $pos + 6;
                $startPoint[] = $position;
                $Offdates[] = substr($offDaysString, $startPoint[$i], 10);

            }

            return $Offdates;
     /*   } else {

            return $Offdates = ['2015-12-25','2016-12-25'];
        }
    */

    }


    public function excludeDays() {
        $tmpDate = $this->getDeliveryDate();  // Delivery after skipping weekdays

      //  $holidays = ['2017-11-02','2017-12-25'];   // $holidayDays = ['*-12-25', '*-01-01', '2013-12-23']; # variable and fixed holidays

      $holidays  = $this->getOffDates();

        $i = 0;
        $nextBusinessDay = date('Y-m-d', strtotime($tmpDate . ' +' . $i . ' Weekday'));  // problem seems to be here


        while (in_array($nextBusinessDay, $holidays)) {
            $i++;
            $nextBusinessDay = date('Y-m-d', strtotime($tmpDate . ' +' . $i . ' Weekday'));
            }
        return $nextBusinessDay;
   }



	public function getCutOffTime() {
		$cuttoffadmin = $this->scopeConfig->getValue('deliverycountdown/delivery/cutofftimemon', \Magento\Store\Model\ScopeInterface::SCOPE_STORE); // Monday Cut off time
		$currenttime = $this->getCurrentTime();

		$cutofftime = strtotime(date('h:i:s A', strtotime(date('d-m-Y') . ' + ' . $cuttoffadmin . ' hours - 0 minutes')));
		return $cutofftime;
	}


    public function getCutOffTimeReal() {
        $cuttoffadmin = $this->scopeConfig->getValue('deliverycountdown/delivery/cutofftimemon', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
       // $cutoff = strtotime(date('d-m-Y') . ' + ' . $cuttoffadmin . ' hours - 0 minutes');

        return  strtotime($cuttoffadmin); //$cuttoffadmin ;
    }


    public function getCurrentTimeReal() {
        $currenttime = $this->getCurrentTime();
        return $currenttime;
    }



    public function getTimeRemaining() {
		$interval = date('H:i:s', mktime(0, 0, $this->getCufOffTime() - $this->getCurrentTime()));
		return $interval;
	}
	
	public function getCurrentTime() {
		$currenttime = strtotime(date("h:i:s A"));
		return $currenttime;
	}
	
	public function getTimeRemainingSeconds() {
		//return $interval = $this->getCutOffTime() - $this->getCurrentTime();
        $interval = $this->getCutOffTimeReal() - $this->getCurrentTimeReal();
        $diffINHours = gmdate("H:i:s", $interval);

       $explodeDiff = explode(':', $diffINHours);
       $leftTime = $explodeDiff[0]. " Hours ". $explodeDiff[1]." minutes  ". $explodeDiff[2]. " secounds ";
       return $leftTime ;

	}
	
	public function buildString() {
		$string = $this->scopeConfig->getValue('deliverycountdown/general/string', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $string = str_replace("{{delivery_date}}",'<span id="date">' . $this->excludeDays() . "</span>",$string);
		//$string = str_replace("{{delivery_date}}",'<span id="date">' . $this->getDeliveryDate() . "</span>",$string);
		$string = str_replace("{{time_remaining}}",'<span id="time">' . $this->getTimeRemainingSeconds() . "</span>",$string);
		return $string;
	}



} 

// need to check if cutoff include excludedays.

//issues need to get correct initial time left

//need to check delivery date includes exclude days

