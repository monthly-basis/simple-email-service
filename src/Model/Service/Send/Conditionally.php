<?php
namespace MonthlyBasis\SimpleEmailService\Model\Service\Send;

use Aws\Exception\AwsException;
use Aws\Ses\SesClient;
use DateTime;
use MonthlyBasis\SimpleEmailService\Model\Service as SimpleEmailServiceService;
use MonthlyBasis\SimpleEmailService\Model\Table as SimpleEmailServiceTable;
use MonthlyBasis\StopForumSpam\Service as StopForumSpamService;

class Conditionally
{
    public function __construct(
        SimpleEmailServiceService\Send $sendService,
        SimpleEmailServiceTable\BounceLog $bounceLogTable,
        SimpleEmailServiceTable\ComplaintLog $complaintLogTable,
        SimpleEmailServiceTable\SendLog $sendLogTable,
        StopForumSpamService\IpAddress\Toxic $toxicService
    ) {
        $this->sendService       = $sendService;
        $this->bounceLogTable    = $bounceLogTable;
        $this->complaintLogTable = $complaintLogTable;
        $this->sendLogTable      = $sendLogTable;
        $this->toxicService      = $toxicService;
    }

    public function conditionallySend(
        string $toEmail,
        string $fromEmail,
        string $subject,
        string $messageText
    ): bool {
        if (!empty($_SERVER['REMOTE_ADDR'])
            && $this->toxicService->isIpAddressToxic($_SERVER['REMOTE_ADDR'])
        ) {
            return false;
        }

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

        $this->sendLogTable->insert($toEmail);

        try {
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
