<?php
namespace Mirasvit\Feed\Console\Command;

use Magento\Framework\App\State;
use Mirasvit\Feed\Model\FeedFactory;
use Mirasvit\Feed\Model\Feed\Deliverer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DeliveryCommand extends AbstractCommand
{
    const INPUT_FEED_ID = 'id';

    /**
     * @var FeedFactory
     */
    protected $feedFactory;

    /**
     * @var Deliverer
     */
    protected $deliverer;

    /**
     * {@inheritdoc}
     * @param FeedFactory $feedFactory
     * @param Deliverer   $deliverer
     * @param State       $appState
     */
    public function __construct(
        FeedFactory $feedFactory,
        Deliverer $deliverer,
        State $appState
    ) {
        $this->feedFactory = $feedFactory;
        $this->deliverer = $deliverer;

        parent::__construct($appState);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $options = [
            new InputOption(
                self::INPUT_FEED_ID,
                null,
                InputOption::VALUE_REQUIRED,
                'Feed ID',
                false
            )
        ];
        $this->setName('mirasvit:feed:delivery')
            ->setDescription('Delivery Feed')
            ->setDefinition($options);

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->appState->setAreaCode('frontend');
        } catch (\Exception $e) {
        }

        $feedId = $input->getOption(self::INPUT_FEED_ID);
        $verbose = $output->getVerbosity() == 2 ? true : false;

        if ($feedId) {
            $feedsIds = [$feedId];
        } else {
            $feedsIds = $this->feedFactory->create()->getCollection()
                ->addFieldToFilter('is_active',array('eq' => 1))
                ->addFieldToFilter('ftp',array('eq' => 1))
                ->getAllIds();
        }

        foreach ($feedsIds as $feedId) {
            /** @var \Mirasvit\Feed\Model\Feed $feed */
            $feed = $this->feedFactory->create()->load($feedId);

            if (!$feed->getId()) {
                $output->writeln('<error>Invalid feed id for option "id".</error>');

                continue;
            }

            if ($verbose) {
                $output->writeln('<info>' . $feed->getName() . '</info>');
            }

            try {
                $this->deliverer->delivery($feed);
                $output->writeln('<info>' . __('Feed "%1" was successfully delivered to "%2"', $feed->getName(), $feed->getFtpHost()) . '</info>');
            } catch (\Exception $e) {
                $output->writeln('<error>' . __('Unable to delivery feed "%1".  %2', $feed->getName(), $e->getMessage()) . '</error>');
            }
        }
    }
}
