<?php
namespace MonthlyBasis\SimpleEmailService\Model\Service\Send;

use Aws\Ses\SesClient;
use Aws\Exception\AwsException;
use DateTime;
use MonthlyBasis\SimpleEmailService\Model\Service as SimpleEmailServiceService;
use MonthlyBasis\SimpleEmailService\Model\Table as SimpleEmailServiceTable;

class Conditionally
{
    public function __construct(
        SimpleEmailServiceService\Send $sendService,
        SimpleEmailServiceTable\BounceLog $bounceLogTable,
        SimpleEmailServiceTable\ComplaintLog $complaintLogTable,
        SimpleEmailServiceTable\SendLog $sendLogTable
    ) {
        $this->sendService       = $sendService;
        $this->bounceLogTable    = $bounceLogTable;
        $this->complaintLogTable = $complaintLogTable;
        $this->sendLogTable      = $sendLogTable;
    }

    public function conditionallySend(
        string $toEmail,
        string $fromEmail,
        string $subject,
        string $messageText
    ): bool {
        $result = $this->bounceLogTable->selectWhereEmailAddressLimit1(
            $toEmail
        );
        if ($result->current()) {
            return false;
        }

        $result = $this->complaintLogTable->selectWhereEmailAddressLimit1(
            $toEmail
        );
        if ($result->current()) {
            return false;
        }

        $result = $this->sendLogTable->selectCountWhereEmailAddressAndCreatedGreaterThan(
            $toEmail,
            (new DateTime())->modify('-1 day')
        );
        if ($result->current()['COUNT(*)'] >= 3) {
            return false;
        }

        try {
            $this->sendLogTable->insert($toEmail);
            $this->sendService->send(
                $toEmail,
                $fromEmail,
                $subject,
                $messageText
            );
        } catch (AwsException $awsException) {
            return false;
        }

        return true;
    }
}
