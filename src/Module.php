<?php
namespace MonthlyBasis\SimpleEmailService;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use MonthlyBasis\SimpleEmailService\Controller as SimpleEmailServiceController;
use MonthlyBasis\SimpleEmailService\Model\Command as SimpleEmailServiceCommand;
use MonthlyBasis\SimpleEmailService\Model\Entity as SimpleEmailServiceEntity;
use MonthlyBasis\SimpleEmailService\Model\Service as SimpleEmailServiceService;
use MonthlyBasis\SimpleEmailService\Model\Table as SimpleEmailServiceTable;
use MonthlyBasis\SimpleEmailService\View\Helper as SimpleEmailServiceHelper;
use MonthlyBasis\StopForumSpam\Service as StopForumSpamService;

class Module
{
    public function getConfig(): array
    {
        return [
            'controllers' => [
                'factories' => [
                    SimpleEmailServiceController\SimpleNotificationService\SimpleEmailService\Bounce::class => function ($sm) {
                        return new SimpleEmailServiceController\SimpleNotificationService\SimpleEmailService\Bounce(
                            $sm->get(\Aws\Sns\MessageValidator::class),
                            $sm->get(SimpleEmailServiceService\SimpleNotificationService\Bounce\SaveToMySql::class),
                            $sm->get('Config')['monthly-basis']['simple-email-service']['logs']['bounce'],
                        );
                    },
                    SimpleEmailServiceController\SimpleNotificationService\SimpleEmailService\Complaint::class => function ($sm) {
                        return new SimpleEmailServiceController\SimpleNotificationService\SimpleEmailService\Complaint(
                            $sm->get(\Aws\Sns\MessageValidator::class),
                            $sm->get(SimpleEmailServiceService\SimpleNotificationService\Complaint\SaveToMySql::class),
                            $sm->get('Config')['monthly-basis']['simple-email-service']['logs']['complaint'],
                        );
                    },
                    SimpleEmailServiceController\SimpleNotificationService\SimpleEmailService\Delivery::class => function ($sm) {
                        return new SimpleEmailServiceController\SimpleNotificationService\SimpleEmailService\Delivery(
                            $sm->get(\Aws\Sns\MessageValidator::class),
                            $sm->get(SimpleEmailServiceService\SimpleNotificationService\Delivery\SaveToMySql::class),
                            $sm->get('Config')['monthly-basis']['simple-email-service']['logs']['delivery'],
                        );
                    },
                ],
            ],
            'laminas-cli' => [
                'commands' => [
                    'send' => SimpleEmailServiceCommand\Send::class,
                ],
            ],
            'router' => [
                'routes' => [
                    'simple-notification-service' => [
                        'type'    => Literal::class,
                        'options' => [
                            'route'    => '/simple-notification-service',
                        ],
                        'may_terminate' => false,
                        'child_routes' => [
                            'simple-email-service' => [
                                'type'    => Literal::class,
                                'options' => [
                                    'route'    => '/simple-email-service',
                                ],
                                'may_terminate' => false,
                                'child_routes' => [
                                    'bounce' => [
                                        'type'    => Literal::class,
                                        'options' => [
                                            'route'    => '/bounce',
                                            'defaults' => [
                                                'controller' => SimpleEmailServiceController\SimpleNotificationService\SimpleEmailService\Bounce::class,
                                                'action'     => 'index',
                                            ],
                                        ],
                                    ],
                                    'complaint' => [
                                        'type'    => Literal::class,
                                        'options' => [
                                            'route'    => '/complaint',
                                            'defaults' => [
                                                'controller' => SimpleEmailServiceController\SimpleNotificationService\SimpleEmailService\Complaint::class,
                                                'action'     => 'index',
                                            ],
                                        ],
                                    ],
                                    'delivery' => [
                                        'type'    => Literal::class,
                                        'options' => [
                                            'route'    => '/delivery',
                                            'defaults' => [
                                                'controller' => SimpleEmailServiceController\SimpleNotificationService\SimpleEmailService\Delivery::class,
                                                'action'     => 'index',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
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
            'view_manager' => [
                'template_path_stack' => [
                    'monthly-basis/simple-email-service' => __DIR__ . '/../view',
                ],
            ],
        ];
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                \Aws\Ses\SesClient::class => function ($sm) {
                    $configEntity = $sm->get(SimpleEmailServiceEntity\Config::class);
                    $credentials = $sm->get('Config')['aws']['credentials'];
                    return new \Aws\Ses\SesClient([
                        'version' => '2010-12-01',
                        'region'  => $configEntity['region'],
                        'credentials' => [
                            'key' => $credentials['key'],
                            'secret' => $credentials['secret'],
                        ],
                    ]);
                },
                /*
                 * @TODO This factory should eventually be moved to the
                 * MonthlyBasis\SimpleNotificationService module whenever it is created
                 */
                \Aws\Sns\MessageValidator::class => function ($sm) {
                    return new \Aws\Sns\MessageValidator();
                },
                /*
                 * @TODO Config entity should be built using a factory such as
                 * MonthlyBasis\SimpleNotificationService\Factory\Config
                 * The factory can ensure that required key-value pairs exist.
                 */
                SimpleEmailServiceEntity\Config::class => function ($sm) {
                    return new SimpleEmailServiceEntity\Config(
                        $sm->get('Config')['monthly-basis']['simple-email-service'] ?? []
                    );
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
                        $sm->get(SimpleEmailServiceTable\SendLog::class),
                        $sm->get(StopForumSpamService\IpAddress\Toxic::class),
                    );
                },
                SimpleEmailServiceService\SimpleNotificationService\Bounce\SaveToMySql::class => function ($sm) {
                    return new SimpleEmailServiceService\SimpleNotificationService\Bounce\SaveToMySql(
                        $sm->get(SimpleEmailServiceTable\BounceLog::class),
                    );
                },
                SimpleEmailServiceService\SimpleNotificationService\Complaint\SaveToMySql::class => function ($sm) {
                    return new SimpleEmailServiceService\SimpleNotificationService\Complaint\SaveToMySql(
                        $sm->get(SimpleEmailServiceTable\ComplaintLog::class),
                    );
                },
                SimpleEmailServiceService\SimpleNotificationService\Delivery\SaveToMySql::class => function ($sm) {
                    return new SimpleEmailServiceService\SimpleNotificationService\Delivery\SaveToMySql(
                        $sm->get(SimpleEmailServiceTable\DeliveryLog::class),
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
