<?php 
namespace Bhs\DeliveryCountdown\Block;

class DeliveryCountdown extends \Magento\Framework\View\Element\Template 
{
	protected $scopeConfig;
    protected $_registry;
    protected $serialize;
    protected $_timezone;
    protected $_urlInterface;


	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Serialize\Serializer\Json $serialize,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,  // For date and time functions
        \Magento\Framework\Registry $registry,
        \Magento\Framework\UrlInterface $urlInterface

    ) {
        $this->_timezone = $context->getLocaleDate();
        $this->date = $date;
        $this->_urlInterface = $urlInterface;
		$this->scopeConfig = $scopeConfig;
        $this->serialize = $serialize;
        $this->_registry = $registry;
        parent::__construct(
            $context
        );
    }

    public function getCustomUrl(){
        
        return $this->_urlInterface->getUrl('deliveryCountdown/index/index');;

    }

    public function getCurrentProduct()
    {        
        return $this->_registry->registry('current_product');
    }   

    public function getCurrentSystemDate() {
        $date = strtotime($this->date->gmtDate('H:i:s'));
        return $date;
    }

    public function getAttrCode()
    {
        $attrCode = $this->scopeConfig->getValue('deliverycountdown/general/product',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $attrCode;
    }

    public function isEnable()
    {
        $isEnable = $this->scopeConfig->getValue('deliverycountdown/general/enable',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $isEnable;
    }

	public function getDeliveryDate() {
		$deliverydaysadmin = $this->scopeConfig->getValue('deliverycountdown/general/deliverytime', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $currenttime = $this->getCurrentTime();

		//$deliverydate = date('d/m/Y', strtotime(date('d-m-Y') . ' + ' . $deliverydaysadmin . ' weekdays'));
        $deliverydate = date('Y-m-d', strtotime(date('d-m-Y') . ' + ' . $deliverydaysadmin . ' days'));
        return $deliverydate;
	}

	// get excluded/ OFF days
    public function getOffDates() {
        $offDaysString = $this->scopeConfig->getValue('deliverycountdown/general/mapping', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

           $Offdates =array();
            
           $offDaysString =  $this->serialize->unserialize($offDaysString);
           foreach ($offDaysString as $value) {
                
                $Offdates[]=$value['field2'];
                
           }
                
            return $Offdates;     

    }


    public function excludeDays() {

        /* exclude holidays and week off days */

        $addDays = $this->scopeConfig->getValue('deliverycountdown/general/deliverytime', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $holidays = $this->getOffDates();
        //$todayDate = $this->_timezone->date()->format('Y-m-d H:i:s');
        
        $currentdate = $this->_timezone->date()->format('Y-m-d');
        $currentDateHour = $this->_timezone->date()->format('G.i');        
        
        $calclateDate = $currentdate;
        $closeWeekDays = $this->setWeekdays();
        $cuttOffTime = $this->scopeConfig->getValue('deliverycountdown/general/cutofftimemon',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $x = 0;
        $d = 1;
        $flag = false;

        $cuttOffTime = str_replace(":",".",$cuttOffTime);
        // check cut of time
        if($currentDateHour >= $cuttOffTime){
            //$flag = true;   
            $addDays = $addDays + 1;            
        }
        
       
        do {

            $calclateDate = date('Y-m-d', strtotime($calclateDate . ' + '.$d.' days'));  // problem seems to be here
            
            //$flag = true;
            // check holidays 
            if(in_array($calclateDate, $holidays)){
                continue;                
            }

            // check weekdays
            $weekDaynumber = date('N', strtotime($calclateDate));
            
            if(in_array($weekDaynumber, $closeWeekDays)){
                continue;
            }
            
            $x++;
            
            
        } while ($x < $addDays);

        
        /* exclude holidays and week off days */

        return $calclateDate;
   }


   public function setWeekdays()
   {
        $closeWeekDays = array();
        $weekdaysoff = $this->scopeConfig->getValue('deliverycountdown/general/weekdays', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);                       
        $closeWeekDays = explode(',', $weekdaysoff);
        return $closeWeekDays;

   }

	public function getCutOffTime() {
		$cuttoffadmin = $this->scopeConfig->getValue('deliverycountdown/general/cutofftimemon', \Magento\Store\Model\ScopeInterface::SCOPE_STORE); // Monday Cut off time
		$currenttime = $this->getCurrentTime();

		$cutofftime = strtotime(date('h:i:s A', strtotime(date('d-m-Y') . ' + ' . $cuttoffadmin . ' hours - 0 minutes')));
		return $cutofftime;
	}


    public function getCutOffTimeReal() {
        $cuttoffadmin = $this->scopeConfig->getValue('deliverycountdown/general/cutofftimemon', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
       // $cutoff = strtotime(date('d-m-Y') . ' + ' . $cuttoffadmin . ' hours - 0 minutes');
        $cuttoffadmin = strtotime($cuttoffadmin);
        return  $cuttoffadmin;
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
		$currenttime = strtotime($this->_timezone->date()->format("G:i:s"));
        //var_dump($this->_timezone->date()->format("G:i:s")); die('dddd');
		return $currenttime;
	}
	
	public function getTimeRemainingSeconds() {
		//return $interval = $this->getCutOffTime() - $this->getCurrentTime();
        $interval = $this->getCutOffTimeReal() - $this->getCurrentTimeReal();
        //$interval =  $this->getCurrentTimeReal()- $this->getCutOffTimeReal() ;
        $diffINHours = gmdate("H:i:s", $interval);

       $explodeDiff = explode(':', $diffINHours);
       $leftTime = $explodeDiff[0]. " Hours ". $explodeDiff[1]." minutes  ". $explodeDiff[2]. " secounds ";
       return $leftTime ;

	}
	
	public function buildString() {
        /*$todayDate = $this->_timezone->date()->format('Y-m-d H:i:s');
        var_dump($todayDate); die('rrrr');*/
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

