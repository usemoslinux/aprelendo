<?php
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Aprelendo;

use PDO;

class Connect
{
    private $driver    = '';
    private $host      = '';
    private $user      = '';
    private $password  = '';
    private $db        = '';
    private $charset   = '';
    
    /**
     * Constructor
     *
     * Sets basic variables for all database connections
     *
     */
    public function __construct(
        string $driver,
        string $host,
        string $user,
        string $password,
        string $db_name,
        string $charset
        )
    {
        $this->driver   = $driver;
        $this->host     = $host;
        $this->user     = $user;
        $this->password = $password;
        $this->db       = $db_name;
        $this->charset  = $charset;
    } // end __construct()

    /**
     * Connects to database using parameters passed to the constructor
     *
     * @return PDO
     */
    public function connect(): \PDO
    {
        try {
            $dsn = $this->driver
                . ':host=' . $this->host
                . ';dbname=' . $this->db
                . ';charset=' . $this->charset;

            $pdo = new \PDO(
                $dsn,
                $this->user,
                $this->password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (\PDOException $e) {
            throw new InternalException($e->getMessage());
        }

        return $pdo;
    }
}
