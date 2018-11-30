<?php
namespace Mirasvit\Feed\Console\Command;

use Magento\Framework\App\State;
use Mirasvit\Feed\Cron\Export as CronExport;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CronCommand extends AbstractCommand
{
    /**
     * @var CronExport
     */
    protected $cronExport;

    /**
     * Constructor
     *
     * @param CronExport $cronExport
     * @param State      $appState
     */
    public function __construct(
        CronExport $cronExport,
        State $appState
    ) {
        $this->cronExport = $cronExport;parent::__construct($appState);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('mirasvit:feed:cron')
            ->setDescription('Run cron jobs for extension')
            ->setDefinition([]);

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->appState->setAreaCode('frontend');

        $this->cronExport->execute();
    }
}
