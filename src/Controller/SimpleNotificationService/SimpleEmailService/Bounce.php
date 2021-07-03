<?php
namespace MonthlyBasis\SimpleEmailService\Controller\SimpleNotificationService\SimpleEmailService;

use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use Laminas\Mvc\Controller\AbstractActionController;
use MonthlyBasis\SimpleEmailService\Model\Service as SimpleEmailServiceService;
use RuntimeException;

class Bounce extends AbstractActionController
{
    public function __construct(
        MessageValidator $messageValidator,
        SimpleEmailServiceService\SimpleNotificationService\Bounce\SaveToMySql $saveToMySqlService,
        string $logPath
    ) {
        $this->messageValidator   = $messageValidator;
        $this->saveToMySqlService = $saveToMySqlService;
        $this->logPath            = $logPath;
    }

    public function indexAction()
    {
        try {
            $message = Message::fromRawPostData();
        } catch (RuntimeException $runtimeException) {
            $this->appendLine('FAIL: ');
            $this->appendLine($runtimeException->getMessage());
            $this->appendLine();
            return;
        }

        if ($this->messageValidator->isValid($message)) {
            $this->appendLine('SUCCESS: ');
            $this->appendLine(json_encode($message->toArray()));
            $this->appendLine();

            $this->saveToMySqlService->saveToMySql($message);
        } else {
            $this->appendLine('FAIL: ');
            $this->appendLine(json_encode($message->toArray()));
            $this->appendLine();
        }
    }

    protected function appendLine(string $line = '')
    {
        file_put_contents(
            $this->logPath,
            $line . "\n",
            FILE_APPEND,
        );
    }
}
