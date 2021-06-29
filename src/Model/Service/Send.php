<?php
namespace MonthlyBasis\SimpleEmailService\Model\Service;

use Aws\Exception\AwsException;
use Aws\Ses\SesClient;

class Send
{
    public function __construct(
        SesClient $sesClient
    ) {
        $this->sesClient = $sesClient;
    }

    /**
     * @throws AwsException
     */
    public function send(
        $toEmail,
        $fromEmail,
        $subject,
        $messageText
    ) {
        $recipient_emails = [
            $toEmail,
        ];

        $char_set = 'UTF-8';

        $result = $this->sesClient->sendEmail([
            'Destination' => [
                'ToAddresses' => $recipient_emails,
            ],
            'Source' => $fromEmail,
            'Message' => [
              'Body' => [
                  'Text' => [
                      'Charset' => $char_set,
                      'Data' => $messageText,
                  ],
              ],
              'Subject' => [
                  'Charset' => $char_set,
                  'Data' => $subject,
              ],
            ],
        ]);
    }
}
