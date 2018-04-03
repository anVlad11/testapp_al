<?php declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180331164621 extends AbstractMigration
{
    public function getDescription()
    {
        $description = "This is the initial migration";
        return $description;
    }

    public function preUp(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', "Migration can only be executed on PostgreSQL");

        $schema->dropTable('users');
        $schema->dropTable('tasks');
        $schema->dropTable('permissions');
        $schema->dropTable('tokens');

        parent::preUp($schema);
    }

    public function up(Schema $schema)
    {
        $this->addSql("CREATE EXTENSION IF NOT EXISTS pgcrypto;");

        //creating 'user' table
        $table = $schema->createTable('users');
        $table->addColumn('id', 'guid', ['notnull' => true]);
        $table->addColumn('login', 'string', ['notnull' => true, 'customSchemaOptions' => ['unique' => true]]);
        $table->setPrimaryKey(['id']);

        //creating 'tasks' table
        $table = $schema->createTable('tasks');
        $table->addColumn('id', 'guid', ['notnull' => true]);
        $table->addColumn('title', 'text', ['notnull' => true]);
        $table->addColumn('description', 'text', ['notnull' => true]);
        $table->addColumn('created_at', 'datetime', ['notnull' => true]);
        $table->addColumn('user_id', 'guid', ['notnull' => true]);
        $table->setPrimaryKey(['id']);

        //creating 'permission' table
        $table = $schema->createTable('permissions');
        $table->addColumn('id', 'guid', ['notnull' => true]);
        $table->addColumn('permission', 'integer', ['notnull' => true]);
        $table->addColumn('token_id', 'guid', ['notnull' => true]);
        $table->setPrimaryKey(['id']);

        //creating 'token' table
        $table = $schema->createTable('tokens');
        $table->addColumn('id', 'guid', ['notnull' => true]);
        $table->addColumn('token', 'string',  ['notnull' => true, 'customSchemaOptions' => ['unique' => true]]);
        $table->addColumn('user_id', 'guid', ['notnull' => true]);
        $table->setPrimaryKey(['id']);

    }

    public function postUp(Schema $schema)
    {
        $this->addSql('ALTER TABLE users ALTER COLUMN id SET DEFAULT gen_random_uuid()');
        $this->addSql('ALTER TABLE tasks ALTER COLUMN id SET DEFAULT gen_random_uuid()');
        $this->addSql('ALTER TABLE permissions ALTER COLUMN id SET DEFAULT gen_random_uuid()');
        $this->addSql('ALTER TABLE tokens ALTER COLUMN id SET DEFAULT gen_random_uuid()');

        parent::postUp($schema);
    }

    public function down(Schema $schema)
    {
        $schema->dropTable('users');
        $schema->dropTable('tasks');
        $schema->dropTable('permissions');
        $schema->dropTable('tokens');
    }
}
