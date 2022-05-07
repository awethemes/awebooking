<?php

namespace AweBooking\System\Database;

use AweBooking\Vendor\Illuminate\Database\Connection as BaseConnection;
use AweBooking\Vendor\Illuminate\Database\Schema\Grammars\MySqlGrammar;
use DateTimeInterface;
use Closure;
use RuntimeException;
use Throwable;
use wpdb;

class Connection extends BaseConnection
{
    /**
     * @var wpdb
     */
    protected $db;

    /**
     * @var static
     */
    protected static $instance;

    /**
     * Get the connection instance.
     *
     * @return static
     */
    public static function getInstance()
    {
        if (!static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        global $wpdb;

        $this->db = $wpdb;
        $this->database = $wpdb->dbname;
        $this->tablePrefix = $wpdb->prefix;

        $this->postProcessor = new Processor();
        $this->queryGrammar = (new QueryGrammar())
            ->setTablePrefix($this->tablePrefix);
    }

    /**
     * {@inheritdoc}
     */
    public function getSchemaBuilder()
    {
        return new SchemaBuilder($this);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultSchemaGrammar()
    {
        return (new MySqlGrammar())
            ->setTablePrefix($this->getTablePrefix());
    }

    /**
     * {@inheritdoc}
     */
    public function select($query, $bindings = [], $useReadPdo = true)
    {
        return $this->prepareResults(
            $this->db->get_results(
                $this->prepareQuery($query, $bindings)
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function selectOne($query, $bindings = [], $useReadPdo = true)
    {
        return $this->prepareResults(
            $this->db->get_row(
                $this->prepareQuery($query, $bindings)
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function cursor($query, $bindings = [], $useReadPdo = true)
    {
        throw new RuntimeException('Cursor query is not support at moment.');
    }

    /**
     * {@inheritdoc}
     */
    public function statement($query, $bindings = [])
    {
        return $this->prepareResults(
            (bool) $this->db->query(
                $this->prepareQuery($query, $bindings)
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function affectingStatement($query, $bindings = [])
    {
        return $this->prepareResults(
            (int) $this->db->query(
                $this->prepareQuery($query, $bindings)
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function unprepared($query)
    {
        return $this->prepareResults(
            $this->db->query($query)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function prepareBindings(array $bindings)
    {
        foreach ($bindings as $key => $value) {
            if ($value instanceof DateTimeInterface) {
                $bindings[$key] = $value->format('Y-m-d H:i:s');
            } elseif (is_bool($value)) {
                $bindings[$key] = (int) $value;
            }
        }

        return $bindings;
    }

    /**
     * Prepares a SQL query for safe execution.
     *
     * @param string $query
     * @param array $bindings
     * @return string
     */
    protected function prepareQuery(string $query, array $bindings)
    {
        if (count($bindings) === 0) {
            return $query;
        }

        // Remove null bindings.
        $bindings = array_filter($bindings, function ($binding) {
            return $binding !== null;
        });

        $bindings = $this->prepareBindings($bindings);

        if (count($bindings) > 0 && false !== strpos($query, '?')) {
            trigger_error(">>> CHECK THIS QUERY:" . $query, E_USER_WARNING);

            $query = preg_replace_callback(
                '/\?/',
                function () use (&$bindings) {
                    static $i = 0;

                    $value = $bindings[$i] ?? null;
                    $i++;

                    return $this->getQueryGrammar()->parameter($value);
                },
                $query,
                count($bindings)
            );
        }

        if (false !== strpos($query, '%')) {
            $query = $this->db->prepare($query, $bindings);
        }

        return $query;
    }

    /**
     * @param mixed $results
     * @return mixed
     */
    protected function prepareResults($results)
    {
        if ($this->transactionLevel() > 0 && $this->db->last_error) {
            throw new QueryException($this->db->last_error);
        }

        return $results;
    }

    /**
     * {@inheritdoc}
     */
    public function transaction(Closure $callback, $attempts = 1)
    {
        $this->beginTransaction();

        try {
            $data = $callback($this);

            $this->commit();

            return $data;
        } catch (Throwable $e) {
            $this->rollBack();

            throw $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function beginTransaction()
    {
        if ($this->transactionLevel() > 0) {
            throw new RuntimeException('Nested transaction is not allowed!');
        }

        $this->statement("START TRANSACTION;");

        $this->transactions++;
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {
        if ($this->transactionLevel() === 0) {
            return;
        }

        $this->statement("COMMIT;");

        $this->transactions--;
    }

    /**
     * {@inheritdoc}
     */
    public function rollBack($toLevel = null)
    {
        if ($this->transactionLevel() === 0) {
            return;
        }

        $this->statement("ROLLBACK;");

        $this->transactions--;
    }

    /**
     * {@inheritdoc}
     */
    public function pretend(Closure $callback)
    {
        throw new RuntimeException('Pretend queries is not supported.');
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect()
    {
        throw new RuntimeException('Disconnect connection is not supported.');
    }

    /**
     * {@inheritdoc}
     */
    public function getPdo()
    {
        throw new RuntimeException('This connection does not use PDO to connect.');
    }

    /**
     * {@inheritdoc}
     */
    public function getReadPdo()
    {
        throw new RuntimeException('This connection does not use PDO to connect.');
    }

    /**
     * {@inheritdoc}
     */
    public function isDoctrineAvailable()
    {
        return false;
    }

    /**
     * @return wpdb
     */
    public function wpdb()
    {
        return $this->db;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'wpdb';
    }
}
