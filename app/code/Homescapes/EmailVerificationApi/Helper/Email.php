<?php
namespace Homescapes\EmailVerificationApi\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;

class Email extends \Magento\Framework\App\Helper\AbstractHelper
{
    const EMAIL_VERIFICATION_DEMO_TEMPLATE = 'email_verification/demo/template';
    const EMAIL_VERIFICATION_DEMO_TEMPLATE_NOTVALID = 'email_verification/demo/template_notvalid';
    const EMAIL_VERIFICATION_DEMO_TEMPLATE_EMAIL_VERIFIED = 'email_verification/demo/template_email_verified'; 

    protected $inlineTranslation;
    protected $escaper;
    protected $transportBuilder;
    protected $logger;
    protected $_storeManager;
    protected $_scopeConfig;

    public function __construct(
        Context $context,
        StateInterface $inlineTranslation,
        Escaper $escaper,
        TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->inlineTranslation = $inlineTranslation;
        $this->escaper = $escaper;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $context->getLogger();
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * @param $model
     * @param bool $valid
     * @param bool $emailVerified
     */
    public function sendEmail($model, $valid = false, $emailVerified = false)
    {
        try {
           
            $name = $this->_scopeConfig->getValue('email_verification/demo/name');
            $email = $this->_scopeConfig->getValue('email_verification/demo/email');
            $data = $model->getData();
            $this->inlineTranslation->suspend();
            $sender = [
                'name' => $this->escaper->escapeHtml($name),
                'email' => $this->escaper->escapeHtml($email),
            ];

            $emailTemplate = $this->_scopeConfig->getValue(self::EMAIL_VERIFICATION_DEMO_TEMPLATE_NOTVALID);
            $templateVars = [];
            if($valid)
            {
                $emailTemplate = $this->_scopeConfig->getValue(self::EMAIL_VERIFICATION_DEMO_TEMPLATE);
                $templateVars = ['verificationCode'  => $this->_storeManager->getStore()->getBaseUrl().'emailverification/page/index?code='.$data['verification_code']];
            }

            if($emailVerified)
            {
                $emailTemplate = $this->_scopeConfig->getValue(self::EMAIL_VERIFICATION_DEMO_TEMPLATE_EMAIL_VERIFIED);
                $templateVars = [];
            }

            $transport = $this->transportBuilder
                ->setTemplateIdentifier($emailTemplate)
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars($templateVars)
                ->setFromByScope($sender)
                ->addTo($data['email'])
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }
}