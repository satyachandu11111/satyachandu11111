<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Model\ResourceModel\Method;

use Amasty\Sorting\Api\MethodInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class AbstractMethod
 *
 * @package Amasty\Sorting\Model\Method
 */
abstract class AbstractMethod extends AbstractDb implements MethodInterface
{
    /**
     * @var bool
     */
    const ENABLED = true;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var string
     */
    protected $methodCode;

    /**
     * @var string
     */
    protected $methodName;

    /**
     * @var \Amasty\Sorting\Helper\Data
     */
    protected $helper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var AdapterInterface|null
     */
    protected $indexConnection = null;

    /**
     * @var array
     */
    private $data;

    public function __construct(
        Context $context,
        $connectionName = null,
        $methodCode = '',
        $methodName = '',
        AbstractDb $indexResource = null,
        $data = []
    ) {
        $this->scopeConfig      = $context->getScopeConfig();
        $this->request          = $context->getRequest();
        $this->storeManager     = $context->getStoreManager();
        $this->helper           = $context->getHelper();
        $this->logger           = $context->getLogger();
        $this->date             = $context->getDate();
        $this->methodCode       = $methodCode;
        $this->methodName       = $methodName;
        if ($indexResource) {
            $this->indexConnection = $indexResource->getConnection();
        }
        $this->data = $data;
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        // dummy
    }

    /**
     * {@inheritdoc}
     */
    abstract public function apply($collection, $direction);

    /**
     * Is sorting method enabled by config
     *
     * @return bool
     */
    public function isActive()
    {
        return !$this->helper->isMethodDisabled($this->getMethodCode());
    }

    /**
     * @return string
     */
    public function getMethodCode()
    {
        if (empty($this->methodCode)) {
            $this->logger->warning('Undefined Amasty sorting method code, add method code to di.xml');
        }
        return $this->methodCode;
    }

    /**
     * @return string
     */
    public function getMethodName()
    {
        if (empty($this->methodCode)) {
            $this->logger->warning('Undefined Amasty sorting method code, add method code to di.xml');
        }
        return $this->methodName;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethodLabel($store = null)
    {
        return __($this->getMethodName());
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    protected function getAdditionalData($key)
    {
        $result = null;
        if (isset($this->data[$key])) {
            $result = $this->data[$key];
        }

        return $result;
    }
}
