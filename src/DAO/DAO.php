<?php

namespace Modea\DAO;

abstract class DAO 
{
    /**
     * Database connection
     *
     * @var Connection
     */
    private $connection;

    /**
     * Constructor
     *
     * @param \Doctrine\DBAL\Connection The database connection object
     */

    public function __construct(\PDO $connection = null)
    {
//        require '../../data/db-config.inc.php';
 		
        $this->connection = $connection;
    }

    /**
     * Builds a domain object from a DB row.
     * Must be overridden by child classes.
     */
    protected abstract function buildDomainObject($row);
}