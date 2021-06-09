<?php
namespace MonthlyBasis\SimpleEmailTest\Model\Table;

use DateTime;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use MonthlyBasis\SimpleEmailService\Model\Table as SimpleEmailServiceTable;
use MonthlyBasis\LaminasTest\TableTestCase;

class BounceLogTest extends TableTestCase
{
    protected function setUp(): void
    {
        $this->dropAndCreateTable('bounce_log');

        $this->bounceLogTable = new SimpleEmailServiceTable\BounceLog(
            $this->getAdapter()
        );
    }

    public function test_insert()
    {
        $result = $this->bounceLogTable->insert(
            'test@example.com'
        );
        $this->assertSame(
            1,
            $result->getAffectedRows()
        );

        $result = $this->bounceLogTable->selectWhereEmailAddressLimit1(
            'test@example.com'
        );
        $array = $result->current();
        $this->assertSame(
            'test@example.com',
            $array['email_address']
        );
    }

    public function test_insertEmailAddressCreated()
    {
        $dateTime = new DateTime();
        $result = $this->bounceLogTable->insertEmailAddressCreated(
            'test@example.com',
            $dateTime
        );
        $this->assertSame(
            1,
            $result->getAffectedRows()
        );

        $result = $this->bounceLogTable->selectWhereEmailAddressLimit1(
            'test@example.com'
        );
        $array = $result->current();
        $this->assertSame(
            'test@example.com',
            $array['email_address']
        );
    }

    public function test_selectCountWhereEmailAddressAndCreatedGreaterThan()
    {
        $dateTimeTwoDaysAgo = (new DateTime())->modify('-2 days');
        $result = $this->bounceLogTable->insertEmailAddressCreated(
            'test@example.com',
            $dateTimeTwoDaysAgo
        );

        $dateTimeOneDayAgo = (new DateTime())->modify('-1 day');
        $result = $this->bounceLogTable->selectCountWhereEmailAddressAndCreatedGreaterThan(
            'test@example.com',
            $dateTimeOneDayAgo
        );
        $this->assertSame(
            '0',
            $result->current()['COUNT(*)']
        );

        $this->bounceLogTable->insert('test@example.com');
        $this->bounceLogTable->insert('test@example.com');
        $result = $this->bounceLogTable->selectCountWhereEmailAddressAndCreatedGreaterThan(
            'test@example.com',
            $dateTimeOneDayAgo
        );
        $this->assertSame(
            '2',
            $result->current()['COUNT(*)']
        );

        $dateTimeThreeDaysAgo = (new DateTime())->modify('-3 days');
        $result = $this->bounceLogTable->selectCountWhereEmailAddressAndCreatedGreaterThan(
            'test@example.com',
            $dateTimeThreeDaysAgo
        );
        $this->assertSame(
            '3',
            $result->current()['COUNT(*)']
        );
    }
}
