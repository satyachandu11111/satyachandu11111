<?php
namespace Mirasvit\Feed\Export\Step;

use Mirasvit\Feed\Export\Context;
use Mirasvit\Feed\Helper\Io;
use Mirasvit\Feed\Model\Config;

class Initialization extends AbstractStep
{
    /**
     * @var \Mirasvit\Feed\Helper\Io
     */
    protected $io;

    /**
     * @var \Mirasvit\Feed\Model\Config
     */
    protected $config;

    /**
     * {@inheritdoc}
     * @param Config   $config
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
     * Remove state and temp feed file
     * {@inheritdoc}
     */
    public function execute()
    {
        $tmpPath = $this->config->getTmpPath();

        $this->io->unlink($this->context->getStateFile());
        $this->io->unlink($tmpPath . DIRECTORY_SEPARATOR . $this->context->getFeed()->getId() . '.dat');

        $this->index = 1;
    }
}
