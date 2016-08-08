<?php

use Phinx\Migration\AbstractMigration;

class Token extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('token', array('id' => false, 'primary_key' => array('tokenKey')));
        $table
            ->addColumn('tokenKey', 'string', array('limit' => 32))
            ->addColumn('capacity', 'integer', array('default' => 0, 'comment' => '容量'))
            ->addColumn('tokens', 'integer', array('default' => 0, 'comment' => '当前容量'))
            ->addColumn('rates', 'integer', array('default' => 0, 'comment' => '速率'))
            ->addColumn('executedTime', 'integer', array('default' => 0, 'comment' => '生效时间'))
            ->create();
    }
}
