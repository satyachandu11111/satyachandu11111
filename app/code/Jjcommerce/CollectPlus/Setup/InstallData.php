<?php

/**
 * CollectPlus
 *
 * @category    CollectPlus
 * @package     Jjcommerce_CollectPlus
 * @version     2.0.0
 * @author      Jjcommerce Team
 *
 */

namespace Jjcommerce\CollectPlus\Setup;

use Magento\Cms\Model\Block;
use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetupFactory;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * Block factory
     *
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $_eavSetupFactory;

    /**
     * Quote setup factory
     *
     * @var QuoteSetupFactory
     */
    protected $_quoteSetupFactory;

    /**
     * Sales setup factory
     *
     * @var SalesSetupFactory
     */
    protected $_salesSetupFactory;

    /**
     * Init
     *
     * @param PageFactory $pageFactory
     */
    public function __construct(BlockFactory $blockFactory,
                                EavSetupFactory $eavSetupFactory,
                                QuoteSetupFactory $quoteSetupFactory,
                                SalesSetupFactory $salesSetupFactory)
    {
        $this->blockFactory = $blockFactory;
        $this->_eavSetupFactory = $eavSetupFactory;
        $this->_quoteSetupFactory = $quoteSetupFactory;
        $this->_salesSetupFactory = $salesSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        $varcharOptions = ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'visible' => true, 'required' => false];

        $booleanOptions = ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN, 'visible' => true, 'required' => false];

        /** @var \Magento\Quote\Setup\QuoteSetup $quoteSetup */
        $quoteSetup = $this->_quoteSetupFactory->create(['setup' => $setup]);
        $quoteSetup->addAttribute('quote', 'sms_alert', $varcharOptions);
        $quoteSetup->addAttribute('quote', 'agent_data', $varcharOptions);

        /** @var \Magento\Sales\Setup\SalesSetup $salesSetup */
        $salesSetup = $this->_salesSetupFactory->create(['setup' => $setup]);
        $salesSetup->addAttribute('order', 'sms_alert', $varcharOptions);
        $salesSetup->addAttribute('order', 'agent_data', $varcharOptions);
        $salesSetup->addAttribute('order', 'export_file_name', $varcharOptions);



        $staticBlock = array(
            'title' => 'CollectPlus Information',
            'identifier' => 'collectplus-information',
            'content' => '
                <div class="collectplus_information_image">
                    <ul>
                        <li>
                            <span id="close_collectplus_popup_information" onclick="hideInfoCollectPopup()">Close</span>
                        </li>
                         <li>
                            <img src=\'{{media url="collectplus/images/collect_logo.png"}}\' alt="CollectPlus" />
                        </li>
                    </ul>
                </div>
                <div class="collectplus_information_content">
                    <ul class="collectplus_information_question_answers">
                        <li class="collectplus_information_question">
                            1. How do I select to have my order delivered to a CollectPlus collection point?
                        </li>
                        <li class="collectplus_information_answer">
                            When placing your order you will be able to choose CollectPlus as a delivery option on the Delivery Options page before you get to the Payment page of checkout.
                        </li>

                        <li class="collectplus_information_question">
                2. Why is CollectPlus not available for my current order?
                        </li>
                        <li class="collectplus_information_answer">
                Unfortunately some items can’t be sent to a CollectPlus collection point. This is probably because the item
you wish to purchase is either too large or too heavy for this delivery method.<br />Please note that this service is only available for suitable items being shipped to customers in the UK.
                        </li>
                        <li class="collectplus_information_question">
                3. How many CollectPlus collection points are there?
                        </li>
                        <li class="collectplus_information_answer">
                CollectPlus has thousands of stores throughout the UK and Northern Ireland. These could be your local Co-Op, McColls, Budgens or Spar, as well as many independently owned convenience stores and newsagents and petrol stations. Unfortunately there are no collection points in the Channel Islands or at BFPO locations at the moment.
                        </li>
                        <li class="collectplus_information_question">
                4. Where is my nearest CollectPlus collection point?
                        </li>
                        <li class="collectplus_information_answer">
                During checkout, when you select CollectPlus as your delivery method, you will also be able to select your nearest collection point.  Alternatively the CollectPlus website offers a postcode or location search.
                        </li>
                        <li class="collectplus_information_question">
                5. When are CollectPlus collection points open?
                        </li>
                        <li class="collectplus_information_answer">
                Nearly all CollectPlus stores are open early &lsquo;til late, 7 days a week. The opening times for your local store will be shown as you choose your preferred collection point when placing your order.
                        </li>
                        <li class="collectplus_information_question">
                6. How long will it take for my order to arrive at my chosen CollectPlus collection point?
                        </li>
                        <li class="collectplus_information_answer">
                Once you have successfully placed and paid for your order, your item(s) will be processed and shipped to your chosen collection point. You will receive confirmation from CollectPlus via email and/or SMS once your parcel is available for collection. This will include your unique collection code. Parcels are delivered to CollectPlus collection points Monday to Saturday. A specific time for arrival cannot be given, please wait for your arrival notification from CollectPlus.
                        </li>
                        <li class="collectplus_information_question">
                7. What do I need to take when I collect my parcel?
                        </li>
                        <li class="collectplus_information_answer">
                        Please take your CollectPlus collection code and proof of ID with you when you go to the collection point to collect your parcel.
                        </li>
                        <li class="collectplus_information_question">
                8. When making a collection, what ID is accepted?
                        </li>
                        <li class="collectplus_information_answer">Please take your CollectPlus collection code and proof of ID with you
                            CollectPlus collection points accept the following forms of ID:
                            <ul class="collectplus_information_identification_types">
                                <li>Driving licence</li>
                                <li>Utility bill</li>
                                <li>Mobile phone bill</li>
                                <li>Wage slip</li>
                                <li>Bank statement</li>
                                <li>Cheque guarantee/credit/debit card</li>
                                <li>Bank/building society book</li>
                                <li>Passport</li>
                                <li>Cheque book</li>
                            </ul>
                        </li>
                        <li class="collectplus_information_question">
                9. What should I do if I lose my collection code from CollectPlus?
                        </li>
                        <li class="collectplus_information_answer">If you have lost the collection code sent to you by CollectPlus via email or SMS then please contact us to arrange for a new collection code to be issued, as you cannot collect your parcel without it.
                        </li>
                        <li class="collectplus_information_question">
                10. What should I do if I don’t receive my collection code from CollectPlus?
                        </li>
                        <li class="collectplus_information_answer">If you haven’t received an email or SMS from CollectPlus with your collection code and the parcel’s estimated date of arrival has passed, then please contact us as our Customer Service team will be able to help you.
                        </li>
                        <li class="collectplus_information_question">
                11. Can someone else collect my order on my behalf?
                        </li>
                        <li class="collectplus_information_answer">Yes, it is possible for someone to collect your parcel on your behalf, but they must have your proof of ID and your collection code.
                        </li>
                        <li class="collectplus_information_question">
                12. What if my order includes back-order items?
                        </li>
                        <li class="collectplus_information_answer">Your order will be split in two. Your in-stock items will be processed as usual for collection at your nominated CollectPlus collection point. Your back-order items will be processed and sent once they arrive in our warehouse. In this instance you will receive two notifications of delivery from CollectPlus and two barcodes, so do make sure that when you are collecting your back-order item that you take the correct collection barcode with you.
                        </li>
                        <li class="collectplus_information_question">
                13. How long will I have to collect my parcel?
                        </li>
                        <li class="collectplus_information_answer">
                        Your parcel will be held at by CollectPlus at their collection point for 10 days before being returned to us. You will receive reminders from CollectPlus after 3 days and 7 days of the parcel arriving at the collection point if you haven’t already collected your parcel. If your parcel is returned to us it will be processed as a return and a refund will be issued. You may still be charged for the CollectPlus delivery cost.
                        </li>
                        <li class="collectplus_information_question">
                14. What should I do if my parcel isn’t available when I arrive at the CollectPlus collection point?
                        </li>
                        <li class="collectplus_information_answer">
                        Please contact us and we will look into it.
                        </li>
                        <li class="collectplus_information_question">
                            15. Once my order has been despatched, can I track it?
                        </li>
                        <li class="collectplus_information_answer">
                        Yes, CollectPlus provides a tracking service which allows you to see where you parcel is. CollectPlus will also provide SMS updates if a mobile number has been provided.
                        <li class="collectplus_information_question">
                            16. What should I do if I don’t want my order anymore?
                        </li>
                        <li class="collectplus_information_answer">
                        If, your order has already been processed, please do not collect the parcel from the CollectPlus collection point. Any uncollected orders will be returned to us after 10 days and a refund will be issued. You may still be charged for the CollectPlus delivery cost.
                        </li>
                        <li class="collectplus_information_question">
                            17. What if I want to return an item I have collected from a CollectPlus collection point?
                        </li>
                        <li class="collectplus_information_answer">
                        If, when you pick up your parcel, you decide that you don’t want all or part of the order, or an incorrect item has been sent, please follow our normal returns process
                        </li>
                    </ul>
                </div>
                <div class="collectplus_information_footer_close">
                    <span class="close_collectplus_popup_information button" onclick="hideInfoCollectPopup()">Close</span>
                </div>',
            'is_active' => 1,
            'stores' => array(0),
        );

        /**
         * Insert block
         */

         $this->createBlock()->setData($staticBlock)->save();
    }

    /**
     * Create page
     *
     * @return Page
     */
    public function createBlock()
    {
        return $this->blockFactory->create();
    }
}
