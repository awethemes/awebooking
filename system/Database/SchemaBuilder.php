<?php

namespace AweBooking\System\Database;

use AweBooking\Vendor\Illuminate\Database\Schema\Blueprint;
use AweBooking\Vendor\Illuminate\Database\Schema\MySqlBuilder;
use Closure;
use LogicException;

class SchemaBuilder extends MySqlBuilder
{
    /**
     * The default string length for migrations.
     *
     * @var int
     */
    public static $defaultStringLength = 199;

    /**
     * Constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;

        $this->connection->useDefaultSchemaGrammar();
        $this->grammar = $connection->getSchemaGrammar();
    }

    /**
     * {@inheritdoc}
     */
    public function dropAllTables()
    {
        throw new LogicException('Too dangerous, cannot dropping all tables.');
    }

    /**
     * {@inheritdoc}
     */
    public function drop($table)
    {
        global $wpdb;

        if (array_key_exists($table, $wpdb->tables())) {
            throw new LogicException('Cannot drop core tables.');
        }

        parent::drop($table);
    }

    /**
     * {@inheritdoc}
     */
    public function dropIfExists($table)
    {
        global $wpdb;

        if (array_key_exists($table, $wpdb->tables())) {
            throw new LogicException('Cannot drop core tables.');
        }

        parent::dropIfExists($table);
    }

    /**
     * {@inheritdoc}
     */
    protected function createBlueprint($table, Closure $callback = null)
    {
        return new Blueprint(
            $table,
            function ($blueprint) use ($callback) {
                global $wpdb;

                $blueprint->charset = $wpdb->charset;
                $blueprint->collation = $wpdb->collate;

                if ($callback) {
                    $callback($blueprint);
                }
            },
            $this->connection->getTablePrefix()
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function build(Blueprint $blueprint)
    {
        foreach ($blueprint->toSql($this->connection, $this->grammar) as $statement) {
            $this->connection->statement($statement);
        }
    }
}
