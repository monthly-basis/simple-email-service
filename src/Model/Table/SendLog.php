<?php
namespace MonthlyBasis\SimpleEmailService\Model\Table;

use MonthlyBasis\SimpleEmailService\Model\Table as SimpleEmailServiceTable;

class SendLog extends SimpleEmailServiceTable\AbstractLog
{
    protected string $tableName = 'send_log';
}
