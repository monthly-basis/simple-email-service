<?php
namespace MonthlyBasis\SimpleEmailService\Model\Table;

use MonthlyBasis\SimpleEmailService\Model\Table as SimpleEmailServiceTable;

class BounceLog extends SimpleEmailServiceTable\AbstractLog
{
    protected string $tableName = 'bounce_log';
}
