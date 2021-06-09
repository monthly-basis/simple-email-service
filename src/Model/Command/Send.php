<?php
namespace MonthlyBasis\SimpleEmailService\Model\Command;

use Laminas\Cli\Command\AbstractParamAwareCommand;
use Laminas\Cli\Input\StringParam;
use MonthlyBasis\SimpleEmailService\Model\Service as SimpleEmailServiceService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Send extends AbstractParamAwareCommand
{
    public function __construct(SimpleEmailServiceService\Send\Conditionally $conditionallySendService)
    {
        parent::__construct();

        $this->conditionallySendService = $conditionallySendService;
    }

	protected function configure(): void
    {
        $this->addParam(
            (new StringParam('to'))
                ->setDescription('to email address')
                ->setShortcut('t')
        );
        $this->addParam(
            (new StringParam('from'))
                ->setDescription('from email address')
                ->setShortcut('f')
        );
        $this->addParam(
            (new StringParam('subject'))
                ->setDescription('subject')
                ->setShortcut('s')
        );
        $this->addParam(
            (new StringParam('message'))
                ->setDescription('message')
                ->setShortcut('m')
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $toEmail = $input->getParam('to');
        $fromEmail = $input->getParam('from');
        $subject = $input->getParam('subject');
        $message = $input->getParam('message');
        var_dump($toEmail, $fromEmail, $subject, $message);

        $this->conditionallySendService->conditionallySend(
            $toEmail,
            $fromEmail,
            $subject,
            $message,
        );

        return Command::SUCCESS;
    }
}
