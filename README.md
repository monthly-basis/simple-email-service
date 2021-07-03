# Monthly Basis > Simple Email Service

This monthly-basis/simple-email-service package is a wrapper for using AWS's Simple Email Service from within the Laminas PHP framework.

## Config

Add and configure the following lines in your local config file, usually located at `config/autoload/local.php`

```
return [
    // ...
    'monthly-basis' => [
        // ...
        'simple-email-service' => [
            'logs' => [
                'bounce'    => '/path/to/bounce.log',
                'complaint' => '/path/to/complaint.log',
                'delivery'  => '/path/to/delivery.log',
            ],  
        ],  
        // ...
    ],
    // ...
];
```
