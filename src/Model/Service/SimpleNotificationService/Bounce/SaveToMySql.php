<?php
namespace MonthlyBasis\SimpleEmailService\Model\Service\SimpleNotificationService\Bounce;

use Aws\Sns\Message;
use MonthlyBasis\SimpleEmailService\Model\Table as SimpleEmailServiceTable;

class SaveToMySql
{
    public function __construct(
        SimpleEmailServiceTable\BounceLog $bounceLogTable
    ) {
        $this->bounceLogTable = $bounceLogTable;
    }

    public function saveToMySql(Message $message)
    {
        $messageJson = $message['Message'];
        $messageObj  = json_decode($messageJson);

        if (!isset($messageObj->{'bounce'}->{'bouncedRecipients'})) {
            return;
        }

        $recipients = $messageObj->{'bounce'}->{'bouncedRecipients'};
        foreach ($recipients as $recipient) {
            $email = $recipient->{'emailAddress'};
            $this->bounceLogTable->insert($email);
        }
    }
}
