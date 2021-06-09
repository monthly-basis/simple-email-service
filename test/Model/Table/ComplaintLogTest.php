<?php
namespace MonthlyBasis\SimpleEmailTest\Model\Table;

use Laminas\Db\Adapter\Exception\InvalidQueryException;
use MonthlyBasis\SimpleEmailService\Model\Table as SimpleEmailServiceTable;
use MonthlyBasis\LaminasTest\TableTestCase;

class ComplaintLogTest extends TableTestCase
{
    protected function setUp(): void
    {
        $this->dropAndCreateTable('complaint_log');

        $this->complaintLogTable = new SimpleEmailServiceTable\ComplaintLog(
            $this->getAdapter()
        );
    }

    public function test_insert()
    {
        $result = $this->complaintLogTable->insert(
            'test@example.com'
        );
        $this->assertSame(
            1,
            $result->getAffectedRows()
        );

        $result = $this->complaintLogTable->selectWhereEmailAddressLimit1(
            'test@example.com'
        );
        $array = $result->current();
        $this->assertSame(
            'test@example.com',
            $array['email_address']
        );
    }
}
