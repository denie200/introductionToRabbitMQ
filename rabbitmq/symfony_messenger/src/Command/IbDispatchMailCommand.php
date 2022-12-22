<?php

namespace App\Command;

use App\Message\MailMessage;
use App\Message\MessageInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;


#[AsCommand(
    name: 'ib.dispatch-mail',
    description: 'Send a mail to the queue',
)]
class IbDispatchMailCommand extends Command
{
    public function __construct(
        private readonly MessageBusInterface $messageBus
    ){
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('message', InputArgument::OPTIONAL, 'Argument description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $msg = $input->getArgument('message') ?? "HELLO WORLD!";

        $mailMessage = new MailMessage(
            ['d.denie@ib.nl'],
            $msg,
            $msg
        );

        if($mailMessage->isValid()) {
            $this->messageBus->dispatch(
                $this->createEnvelope($mailMessage)
            );
            $io->success('Message send to the queue!');

            return Command::SUCCESS;
        }

        return Command::FAILURE;
    }

    private function createEnvelope(MessageInterface $message): Envelope
    {
         return new Envelope(
             $message,
             [
                 new DelayStamp(5000),
                 new AmqpStamp('ib.mail')
             ]
         );
    }
}
