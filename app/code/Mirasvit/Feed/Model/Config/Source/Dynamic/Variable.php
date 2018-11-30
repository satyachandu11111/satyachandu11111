<?php
namespace Mirasvit\Feed\Model\Config\Source\Dynamic;

use Magento\Framework\Option\ArrayInterface;
use Mirasvit\Feed\Model\Config;
use Mirasvit\Feed\Model\ResourceModel\Dynamic\Variable\CollectionFactory as VariableCollectionFactory;

class Variable implements ArrayInterface
{
    /**
     * @var VariableCollectionFactory
     */
    protected $variableCollectionFactory;

    /**
     * @var Config
     */
    protected $config;

    /**
     * Constructor
     *
     * @param VariableCollectionFactory $variableCollectionFactory
     * @param Config                $config
     */
    public function __construct(
        VariableCollectionFactory $variableCollectionFactory,
        Config $config
    ) {
        $this->variableCollectionFactory = $variableCollectionFactory;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray($filesystem = false)
    {
        $result = [];

        if ($filesystem) {
            $path = $this->config->getDynamicVariablePath();
            if ($handle = opendir($path)) {
                while (false !== ($entry = readdir($handle))) {
                    if (substr($entry, 0, 1) != '.') {
                        $result[] = [
                            'label' => $entry,
                            'value' => $path . '/' . $entry,
                        ];
                    }
                }
                closedir($handle);
            }
        } else {
            $result = $this->variableCollectionFactory->create()->toOptionArray();
        }

        sort($result);

        return $result;
    }
}
