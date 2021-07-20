<?php  
namespace Homescapes\Updateattrpos\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Customer\Model\Customer;

class UpgradeData implements UpgradeDataInterface {

    public function __construct(\Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
    \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute
) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->_eavAttribute = $eavAttribute;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        $setup->startSetup();

        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);        
        if(version_compare($context->getVersion(), '1.0.0', '<'))
        {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/updatesort.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info('attribute id');


            
            $attributeId = $this->_eavAttribute->getIdByCode('customer_address', 'company');
            $logger->info($attributeId);
        	$eavSetup->updateAttribute('customer_address', $attributeId, 'sort_order', 130);
            

        	
        }
        
	}
}