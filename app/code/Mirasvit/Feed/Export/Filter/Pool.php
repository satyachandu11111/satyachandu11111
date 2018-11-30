<?php
namespace Mirasvit\Feed\Export\Filter;

class Pool
{
    /**
     * @var object[]
     */
    protected $scopes;

    /**
     * Constructor
     *
     * @param array $scopes
     */
    public function __construct(
        array $scopes
    ) {
        $this->scopes = $scopes;
    }

    /**
     * List of scopes
     *
     * @return object[]
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * Return full list of possible filters
     *
     * @return array
     */
    public function getFilters()
    {
        $filters = [];
        foreach ($this->scopes as $scope) {
            $class = new \Zend_Reflection_Class($scope);

            /** @var \Zend_Reflection_Method $method */
            foreach ($class->getMethods() as $method) {
                try {
                    $doc = $method->getDocblock();
                } catch (\Exception $e) {
                    continue;
                }

                $filter = [
                    'label' => __($doc->getShortDescription())->__toString(),
                    'value' => $method->getName(),
                    'args' => [],
                ];

                /** @var \Zend_Reflection_Parameter $param */
                foreach ($method->getParameters() as $param) {
                    if ($param->getName() == 'input') {
                        continue;
                    }

                    $default = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : '';

                    $filter['args'][] = [
                        'value' => $param->getName(),
                        'label' => ucfirst($param->getName()),
                        'default' => $default
                    ];
                }

                $filters[] = $filter;
            }
        }

        return $filters;
    }
}
