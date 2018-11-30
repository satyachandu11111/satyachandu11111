<?php
namespace Mirasvit\Feed\Export\Step;

use Magento\Framework\ObjectManagerInterface;

class StepFactory
{
    /**
     * Object Manager
     *
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Construct
     *
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Gets product of particular type
     *
     * @param string $className
     * @param array  $data
     * @return \Mirasvit\Feed\Export\Step\AbstractStep
     */
    public function create($className, array $data = [])
    {
        if (strpos($className, 'Mirasvit') === false) {
            $className = 'Mirasvit\Feed\Export\Step\\' . $className;
        }

        $step = $this->objectManager->create($className, $data);

        return $step;
    }
}
