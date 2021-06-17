<?php
namespace MonthlyBasis\SimpleEmailService;

use MonthlyBasis\SimpleEmailService\Model\Command as SimpleEmailServiceCommand;
use MonthlyBasis\SimpleEmailService\Model\Service as SimpleEmailServiceService;
use MonthlyBasis\SimpleEmailService\Model\Table as SimpleEmailServiceTable;
use MonthlyBasis\SimpleEmailService\View\Helper as SimpleEmailServiceHelper;

class Module
{
    public function getConfig(): array
    {
        return [
            'laminas-cli' => [
                'commands' => [
                    'send' => SimpleEmailServiceCommand\Send::class,
                ],
            ],
            'service_manager' => [
                'factories' => [
                    SimpleEmailServiceCommand\Send::class => function ($sm) {
                        return new SimpleEmailServiceCommand\Send(
                            $sm->get(SimpleEmailServiceService\Send\Conditionally::class)
                        );
                    },
                ],
            ],
        ];
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                \Aws\Ses\SesClient::class => function ($sm) {
                    $credentials = $sm->get('Config')['aws']['credentials'];
                    return new \Aws\Ses\SesClient([
                        'version' => '2010-12-01',
                        'region'  => 'us-east-2',
                        'credentials' => [
                            'key' => $credentials['key'],
                            'secret' => $credentials['secret'],
                        ],
                    ]);
                },
                SimpleEmailServiceService\Send::class => function ($sm) {
                    return new SimpleEmailServiceService\Send(
                        $sm->get(\Aws\Ses\SesClient::class)
                    );
                },
                SimpleEmailServiceService\Send\Conditionally::class => function ($sm) {
                    return new SimpleEmailServiceService\Send\Conditionally(
                        $sm->get(SimpleEmailServiceService\Send::class),
                        $sm->get(SimpleEmailServiceTable\BounceLog::class),
                        $sm->get(SimpleEmailServiceTable\ComplaintLog::class),
                        $sm->get(SimpleEmailServiceTable\SendLog::class)
                    );
                },
                SimpleEmailServiceService\SimpleNotificationService\Bounce\SaveToMySql::class => function ($sm) {
                    return new SimpleEmailServiceService\SimpleNotificationService\Bounce\SaveToMySql(
                        $sm->get(SimpleEmailServiceTable\BounceLog::class),
                    );
                },
                SimpleEmailServiceTable\BounceLog::class => function ($sm) {
                    return new SimpleEmailServiceTable\BounceLog(
                        $sm->get('simple-email-service')
                    );
                },
                SimpleEmailServiceTable\ComplaintLog::class => function ($sm) {
                    return new SimpleEmailServiceTable\ComplaintLog(
                        $sm->get('simple-email-service')
                    );
                },
                SimpleEmailServiceTable\DeliveryLog::class => function ($sm) {
                    return new SimpleEmailServiceTable\DeliveryLog(
                        $sm->get('simple-email-service')
                    );
                },
                SimpleEmailServiceTable\SendLog::class => function ($sm) {
                    return new SimpleEmailServiceTable\SendLog(
                        $sm->get('simple-email-service')
                    );
                },
            ],
        ];
    }
}
