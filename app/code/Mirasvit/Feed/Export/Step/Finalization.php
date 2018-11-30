<?php
namespace Mirasvit\Feed\Export\Step;

use Mirasvit\Feed\Export\Context;
use Mirasvit\Feed\Helper\Io;
use Mirasvit\Feed\Model\Config;

class Finalization extends AbstractStep
{
    /**
     * @var Io
     */
    protected $io;

    /**
     * @var Config
     */
    protected $config;

    /**
     * {@inheritdoc}
     * @param Config  $config
     * @param Io      $io
     * @param Context $context
     */
    public function __construct(
        Config $config,
        Io $io,
        Context $context
    ) {
        $this->io = $io;
        $this->config = $config;

        parent::__construct($context);
    }

    /**
     * Copy temp feed file to regular place
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->isReady()) {
            $this->beforeExecute();
        }

        $feed = $this->context->getFeed();
        $tmpPath = $this->config->getTmpPath() . DIRECTORY_SEPARATOR . $feed->getId() . '.dat';
        $targetPath = $this->config->getBasePath() . DIRECTORY_SEPARATOR . $this->context->getFilename();

        $this->io->copy($tmpPath, $targetPath);

        $this->index++;
    }
}
