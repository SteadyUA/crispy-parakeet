<?php

namespace Console\Communication;

use Communication\Messages\MessagesProjection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MessagesCommand extends Command
{
    /**
     * @var MessagesProjection
     */
    private $projection;

    public function __construct(MessagesProjection $messagesProjection)
    {
        $this->projection = $messagesProjection;
        parent::__construct('chat:messages');
    }

    protected function configure()
    {
        $this->setDescription('Show latest chat messages.')
            ->addArgument('limit', InputArgument::OPTIONAL, 'Messages limit', 10);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $limit = $input->getArgument('limit');

        $messages = $this->projection->latestMessages($limit);
        foreach ($messages as $message) {
            $output->writeln(date("H:i:s", $message['createdAt']) . " {$message['loginName']}: {$message['messageText']}");
        }

        return 0;
    }
}
