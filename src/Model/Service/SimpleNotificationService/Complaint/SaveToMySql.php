<?php
namespace MonthlyBasis\SimpleEmailService\Model\Service\SimpleNotificationService\Complaint;

use Aws\Sns\Message;
use MonthlyBasis\SimpleEmailService\Model\Table as SimpleEmailServiceTable;

class SaveToMySql
{
    public function __construct(
        SimpleEmailServiceTable\ComplaintLog $complaintLogTable
    ) {
        $this->complaintLogTable = $complaintLogTable;
    }

    public function saveToMySql(Message $message)
    {
        $messageJson = $message['Message'];
        $messageObj  = json_decode($messageJson);

        if (!isset($messageObj->{'complaint'}->{'complainedRecipients'})) {
            return;
        }

        $recipients = $messageObj->{'complaint'}->{'complainedRecipients'};
        foreach ($recipients as $recipient) {
            $email = $recipient->{'emailAddress'};
            $this->complaintLogTable->insert($email);
        }
    }
}
