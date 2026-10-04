<?php

class Update_products_schema
{
    private $_lava;

    public function __construct()
    {
        $this->_lava = lava_instance();
        $this->_lava->call->database();
    }

    public function up()
    {
        $columns = $this->_lava->db->raw('SHOW COLUMNS FROM `products`')->fetchAll(PDO::FETCH_ASSOC);
        $column_types = [];

        foreach ($columns as $column) {
            $column_types[$column['Field']] = strtolower($column['Type']);
        }

        $changes = [];

        if (($column_types['product_name'] ?? '') !== 'varchar(100)') {
            $long_name = $this->_lava->db->raw(
                'SELECT 1 FROM `products` WHERE CHAR_LENGTH(`product_name`) > 100 LIMIT 1'
            )->fetch(PDO::FETCH_ASSOC);

            if ($long_name) {
                throw new RuntimeException('Product names longer than 100 characters must be shortened before this migration.');
            }

            $changes[] = 'MODIFY `product_name` VARCHAR(100) NOT NULL';
        }

        if (($column_types['description'] ?? '') !== 'text') {
            $changes[] = 'MODIFY `description` TEXT NOT NULL';
        }

        if (!isset($column_types['created_at'])) {
            $changes[] = 'ADD COLUMN `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP';
        }

        if ($changes) {
            $this->_lava->db->raw('ALTER TABLE `products` ' . implode(', ', $changes));
        }
    }

    public function down()
    {
        $columns = $this->_lava->db->raw('SHOW COLUMNS FROM `products`')->fetchAll(PDO::FETCH_ASSOC);
        $column_types = [];

        foreach ($columns as $column) {
            $column_types[$column['Field']] = strtolower($column['Type']);
        }

        if (($column_types['product_name'] ?? '') === 'varchar(100)') {
            $this->_lava->db->raw('ALTER TABLE `products` MODIFY `product_name` VARCHAR(150) NOT NULL');
        }
    }
}