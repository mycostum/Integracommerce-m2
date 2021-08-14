<?php

namespace Mycostum\IntegraCommerce\Console\Queue\Catalog\Category;

use Mycostum\IntegraCommerce\Console\AbstractConsole;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends AbstractConsole
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('integracommerce:queue_create:category')
            ->setDescription('Create queue for categories.');

        parent::configure();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Magento\Cron\Model\Schedule $schedule */
        $schedule = $this->context->objectManager()->create(\Magento\Cron\Model\Schedule::class);

        /** @var \Mycostum\IntegraCommerce\Cron\Queue\Catalog\Category $cron */
        $cron = $this->context
            ->objectManager()
            ->create(\Mycostum\IntegraCommerce\Cron\Queue\Catalog\Category::class);

        $cron->create($schedule);

        $output->writeln((string) $schedule->getMessages());
    }
}
