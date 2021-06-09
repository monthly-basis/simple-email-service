<?php
namespace MonthlyBasis\SimpleEmailTest\Model\Table;

use Laminas\Db\Adapter\Exception\InvalidQueryException;
use MonthlyBasis\SimpleEmailService\Model\Table as SimpleEmailServiceTable;
use MonthlyBasis\LaminasTest\TableTestCase;

class DeliveryLogTest extends TableTestCase
{
    protected function setUp(): void
    {
        $this->dropAndCreateTable('delivery_log');

        $this->deliveryLogTable = new SimpleEmailServiceTable\DeliveryLog(
            $this->getAdapter()
        );
    }

    public function test_insert()
    {
        $result = $this->deliveryLogTable->insert(
            'test@example.com'
        );
        $this->assertSame(
            1,
            $result->getAffectedRows()
        );

        $result = $this->deliveryLogTable->selectWhereEmailAddressLimit1(
            'test@example.com'
        );
        $array = $result->current();
        $this->assertSame(
            'test@example.com',
            $array['email_address']
        );
    }
}
