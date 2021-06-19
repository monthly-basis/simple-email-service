<?php
namespace MonthlyBasis\SimpleEmailService\Model\Service\SimpleNotificationService\Delivery;

use Aws\Sns\Message;
use MonthlyBasis\SimpleEmailService\Model\Table as SimpleEmailServiceTable;

class SaveToMySql
{
    public function __construct(
        SimpleEmailServiceTable\DeliveryLog $deliveryLogTable
    ) {
        $this->deliveryLogTable = $deliveryLogTable;
    }

    public function saveToMySql(Message $message)
    {
        $messageJson = $message['Message'];
        $messageObj  = json_decode($messageJson);

        if (!isset($messageObj->{'delivery'}->{'recipients'})) {
            return;
        }

        $recipients = $messageObj->{'delivery'}->{'recipients'};
        foreach ($recipients as $email) {
            $this->deliveryLogTable->insert($email);
        }
    }
}
