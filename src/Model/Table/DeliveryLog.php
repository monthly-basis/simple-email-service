<?php
namespace MonthlyBasis\SimpleEmailService\Model\Table;

use MonthlyBasis\SimpleEmailService\Model\Table as SimpleEmailServiceTable;

class DeliveryLog extends SimpleEmailServiceTable\AbstractLog
{
    protected string $tableName = 'delivery_log';
}
