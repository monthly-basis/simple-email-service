<?php
namespace MonthlyBasis\SimpleEmailService\Model\Table;

use MonthlyBasis\SimpleEmailService\Model\Table as SimpleEmailServiceTable;

class ComplaintLog extends SimpleEmailServiceTable\AbstractLog
{
    protected string $tableName = 'complaint_log';
}
