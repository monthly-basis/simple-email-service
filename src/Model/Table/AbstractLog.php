<?php
namespace MonthlyBasis\SimpleEmailService\Model\Table;

use DateTime;
use Exception;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\Pdo\Result;

abstract class AbstractLog
{
    protected Adapter $adapter;

    protected string $tableName;

    final public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;

        if (empty($this->tableName)
            || preg_match('/\W/', $this->tableName)
        ) {
            throw new Exception('Invalid table name.');
        }
    }

    public function insert(
        string $emailAddress
    ): Result {
        $sql = "
            INSERT
              INTO {$this->tableName}
                   (
                       `email_address`
                   )
            VALUES (?)
                 ;
        ";
        $parameters = [
            $emailAddress,
        ];
        return $this->adapter->query($sql)->execute($parameters);
    }

    public function insertEmailAddressCreated(
        string $emailAddress,
        DateTime $created
    ): Result {
        $sql = "
            INSERT
              INTO {$this->tableName}
                   (
                       `email_address`,
                       `created`
                   )
            VALUES (?, ?)
                 ;
        ";
        $parameters = [
            $emailAddress,
            $created->format('Y-m-d H:i:s'),
        ];
        return $this->adapter->query($sql)->execute($parameters);
    }

    public function selectCountWhereEmailAddressAndCreatedGreaterThan(
        string $emailAddress,
        DateTime $created
    ): Result {
        $sql = "
            SELECT COUNT(*)
              FROM {$this->tableName}
             WHERE `email_address` = ?
               AND `created` > ?
                 ;
        ";
        $parameters = [
            $emailAddress,
            $created->format('Y-m-d H:i:s'),
        ];
        return $this->adapter->query($sql)->execute($parameters);
    }

    public function selectWhereEmailAddressLimit1(string $emailAddress): Result
    {
        $sql = "
            SELECT `email_address`, `created`
              FROM {$this->tableName}
             WHERE `email_address` = ?
             LIMIT 1
                 ;
        ";
        $parameters = [
            $emailAddress,
        ];
        return $this->adapter->query($sql)->execute($parameters);
    }
}
