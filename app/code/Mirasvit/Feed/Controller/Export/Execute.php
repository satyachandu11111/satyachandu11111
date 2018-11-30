<?php
namespace Mirasvit\Feed\Controller\Export;

use Mirasvit\Feed\Controller\Export;
use Mirasvit\Feed\Export\Step\Exporting as ExportingStep;
use Mirasvit\Feed\Export\Step\Validation as ValidationStep;
use Mirasvit\Feed\Model\Config;

class Execute extends Export
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $mode = $this->getRequest()->getParam('mode');

        try {
            $feed = $this->getFeed();
            $handler = $this->exporter->getHandler($feed);

            $result = [
                'success' => true,
                'progress' => [],
            ];

            if (!$mode || $mode == 'new') {
                $handler->reset();
                $result['progress'] = $handler->toJson();
            } else {
                $status = $this->exporter->export($feed);

                $result['status'] = $status;

                if ($status == Config::STATUS_COMPLETED) {
                    $result['progress']['completed'] = [
                        'url' => $feed->getUrl(),
                        'time' => gmdate('H:i:s', $feed->getGeneratedTime()),
                        'count' => $handler->getStepData(ExportingStep::STEP, 'data/count'),
                        'valid' => $handler->getStepData(ExportingStep::STEP, 'data/count') - $handler->getStepData(
                            ValidationStep::STEP,
                            'data/'.ValidationStep::INVALID_ENTITY_COUNT
                        ),
                    ];
                } else {
                    $result['progress'] = $handler->toJson();
                }
            }
        } catch (\Exception $e) {
            $result['success'] = false;
            $result['progress']['error'] = $e->getMessage();
        }

        $callback = $this->getRequest()->getParam('callback');

        /** @var \Magento\Framework\App\Response\Http\Interceptor $response */
        $response = $this->getResponse();
        $response->setHeader('Content-Type', 'application/javascript', true);
        $response->setBody($callback . '(' . \Zend_Json::encode($result) . ')');
    }

    /**
     * {@inheritdoc}
     *
     * Disable keys (request without form key)
     */
    protected function _processUrlKeys()
    {
        return true;
    }
}
