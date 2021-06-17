<?php
namespace MonthlyBasis\SimpleEmailTest\Model\Service\SimpleNotificationService\Bounce;

use Aws\Sns\Message;
use MonthlyBasis\SimpleEmailService\Model\Service as SimpleEmailServiceService;
use MonthlyBasis\SimpleEmailService\Model\Table as SimpleEmailServiceTable;
use PHPUnit\Framework\TestCase;

class SaveToMySqlTest extends TestCase
{
    protected function setUp(): void
    {
        $this->bounceLogTableMock = $this->createMock(
            SimpleEmailServiceTable\BounceLog::class
        );

        $this->saveToMySqlService = new SimpleEmailServiceService\SimpleNotificationService\Bounce\SaveToMySql(
            $this->bounceLogTableMock
        );
    }

    public function test_saveToMySql_zeroEmailAddresses_zeroInserts()
    {
        $this->bounceLogTableMock
            ->expects($this->exactly(0))
            ->method('insert')
            ;

        $message = new Message([
            'Message' => '',
            'MessageId' => '',
            'Signature' => '',
            'SignatureVersion' => '',
            'SigningCertURL' => '',
            'Timestamp' => '',
            'TopicArn' => '',
            'Type' => '',
        ]);
        $message['Message'] = '{"Type":"SubscriptionConfirmation","Message":"You have chosen to subscribe to the topic.\nTo confirm the subscription, visit the SubscribeURL included in this message."}';

        $this->saveToMySqlService->saveToMySql($message);
    }

    public function test_saveToMySql_oneEmailAddress_oneInsert()
    {
        $this->bounceLogTableMock
            ->expects($this->once())
            ->method('insert')
            ->with('bounce@simulator.amazonses.com')
            ;

        $message = new Message([
            'Message' => '',
            'MessageId' => '',
            'Signature' => '',
            'SignatureVersion' => '',
            'SigningCertURL' => '',
            'Timestamp' => '',
            'TopicArn' => '',
            'Type' => '',
        ]);
        $message['Message'] = '{"bounce":{"bouncedRecipients":[{"emailAddress":"bounce@simulator.amazonses.com","action":"failed"}]}}';

        $this->saveToMySqlService->saveToMySql($message);
    }

    /**
     * @todo We were not able to receive a message from AWS SES with multiple
     * bounced email addresses. So, if we can receive this message while
     * sending actual messages, we can test the message in the unit test at
     * that time.
     */
    public function test_saveToMySql_multipleEmailAddresses_multipleInserts()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
