<?php

class Create_products_table
{
    private $_lava;

    public function __construct()
    {
        $this->_lava = lava_instance();
        $this->_lava->call->dbforge();
    }

    public function up()
    {
        if ($this->_lava->dbforge->table_exists('products')) {
            return;
        }

        $this->_lava->dbforge
            ->add_field([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => TRUE,
                    'auto_increment' => TRUE,
                    'null'           => FALSE,
                ],
                'product_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                    'null'       => FALSE,
                ],
                'description' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => FALSE,
                ],
                'price' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,2',
                    'null'       => FALSE,
                ],
                'quantity' => [
                    'type'       => 'INT',
                    'unsigned'   => TRUE,
                    'null'       => FALSE,
                    'default'    => 0,
                ],
            ])
            ->add_key('id', primary: TRUE)
            ->create_table('products');
    }

    public function down()
    {
        $this->_lava->dbforge->drop_table('products');
    }
}