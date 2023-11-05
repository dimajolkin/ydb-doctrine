<?php

namespace Dimajolkin\YdbDoctrine\Tests\Fuctional;

use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Types;

class SchemaManagerTestCase extends AbstractTestCase
{
    private function createTable(string $name): Table
    {
        $table2 = new Table($name);
        $table2->addColumn('id', Types::STRING);
        $table2->addColumn('name', Types::STRING, ['notnull' => false]);
        $table2->setPrimaryKey(['id']);

        return $table2;
    }

    public function testCreateTable(): void
    {
        $this->connection->beginTransaction();
        try {
            $table2 = $this->createTable('tmp_event');
            $sm = $this->connection->createSchemaManager();
            $sm->createTable($table2);
            $this->assertTrue($sm->tablesExist('tmp_event'));

            $sm->dropTable('tmp_event');
            $this->assertFalse($sm->tablesExist('tmp_event'));
        } finally {
            $this->connection->rollBack();
        }
    }

    public function testInsetInNewTableStringValue(): void
    {
        $table = $this->createTable('tmp_event');
        $sm = $this->connection->createSchemaManager();
        $sm->createTable($table);

        $this->connection->transactional(function () {
            $this->connection->insert('tmp_event', ['id' => '1', 'name' => 'test']);
        });

        $this->assertEquals('test', $this->connection->fetchOne("SELECT name FROM tmp_event WHERE id = ?", [1], [Types::STRING]));
        $sm->dropTable('tmp_event');
    }

    public function testListTable(): void
    {
        $sm = $this->connection->createSchemaManager();

        $sm->createTable($this->createTable('tmp_event'));

        $this->assertNotEmpty($sm->listTables());
        $tableInfo = $sm->listTables()[0];

        $this->assertEquals('tmp_event', $tableInfo->getName());
    }
}