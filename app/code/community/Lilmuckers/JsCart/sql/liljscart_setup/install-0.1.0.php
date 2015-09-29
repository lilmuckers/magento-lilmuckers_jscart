<?php
/**
 * Magento javascript cart management module
 *
 * @category  Lilmuckers
 * @package   Lilmuckers_JsCart
 * @copyright Copyright (c) 2014 Patrick McKinley (http://www.patrick-mckinley.com)
 * @license   http://choosealicense.com/licenses/mit/
 */

//container array for the table schemas
$tables = array();

//table names
$userTable       = $this->getTable('liljscart/consumer');
$userStoreTable  = $this->getTable('liljscart/consumer_store');
$userOriginTable = $this->getTable('liljscart/consumer_origin');

//setup the user table
$tables[] = $this->getConnection()
    ->newTable($userTable)
    ->addColumn(
        'consumer_id', 
        Varien_Db_Ddl_Table::TYPE_INTEGER, 
        null, 
        array(
            'identity' => true,
            'nullable' => false,
            'primary' => true
        ), 
        'Consumer Id'
    )
    ->addColumn(
        'alias', 
        Varien_Db_Ddl_Table::TYPE_VARCHAR, 
        255, 
        array('nullable' => false), 
        'Consumer Alias'
    )
    ->addColumn(
        'email', 
        Varien_Db_Ddl_Table::TYPE_VARCHAR, 
        255, 
        array('nullable' => false), 
        'Consumer Contact Email'
    )
    ->addColumn(
        'created_at', 
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP, 
        null, 
        array('nullable' => false), 
        'Created At'
    )
    ->addColumn(
        'updated_at', 
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP, 
        null, 
        array('nullable' => false), 
        'Updated At'
    )
    ->addColumn(
        'api_key', 
        Varien_Db_Ddl_Table::TYPE_VARCHAR, 
        255, 
        array('nullable' => false), 
        'Order Increment ID'
    )
    ->setComment('JSCart Consumers');

//setup the user store table
$tables[] = $this->getConnection()
    ->newTable($userStoreTable)
    ->addColumn(
        'consumer_store_id', 
        Varien_Db_Ddl_Table::TYPE_INTEGER, 
        null, 
        array(
            'identity' => true,
            'nullable' => false,
            'primary' => true
        ), 
        'Consumer Id'
    )
    ->addColumn(
        'consumer_id', 
        Varien_Db_Ddl_Table::TYPE_INTEGER, 
        null, 
        array('nullable' => false), 
        'Consumer ID'
    )
    ->addColumn(
        'store_id', 
        Varien_Db_Ddl_Table::TYPE_INTEGER, 
        null, 
        array('nullable' => false), 
        'Store ID'
    )
    ->addForeignKey(
        $this->getFkName(
            'liljscart/consumer_store', 
            'consumer_id', 
            'liljscart/consumer', 
            'consumer_id'
        ),
        'consumer_id',
        $userTable,
        'consumer_id',
        'CASCADE',
        'CASCADE'
    )
    ->addForeignKey(
        $this->getFkName(
            'liljscart/consumer_store', 
            'store_id', 
            'core/store', 
            'store_id'
        ),
        'store_id',
        $this->getTable('core/store'),
        'store_id',
        'CASCADE',
        'CASCADE'
    )
    ->setComment('JSCart Consumer Store Links');

//setup the user origin table
$tables[] = $this->getConnection()
    ->newTable($userOriginTable)
    ->addColumn(
        'consumer_origin_id', 
        Varien_Db_Ddl_Table::TYPE_INTEGER, 
        null, 
        array(
            'identity' => true,
            'nullable' => false,
            'primary' => true
        ), 
        'Consumer Id'
    )
    ->addColumn(
        'consumer_id', 
        Varien_Db_Ddl_Table::TYPE_INTEGER, 
        null, 
        array('nullable' => false), 
        'Consumer ID'
    )
    ->addColumn(
        'origin', 
        Varien_Db_Ddl_Table::TYPE_VARCHAR, 
        255, 
        array('nullable' => false), 
        'Consumer Origin'
    )
    ->addForeignKey(
        $this->getFkName(
            'liljscart/consumer_origin', 
            'consumer_id', 
            'liljscart/consumer', 
            'consumer_id'
        ),
        'consumer_id',
        $userTable,
        'consumer_id',
        'CASCADE',
        'CASCADE'
    )
    ->setComment('JSCart Consumer Origin URLs');

foreach ($tables as $table) {
    $this->getConnection()->createTable($table);
}

//create an index against the consumer origins column
$this->getConnection()->addIndex(
    $userOriginTable,
    $this->getIdxName(
        'liljscart/consumer_origin', 
        'origin', 
        Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
    ),
    'origin'
);

//create an index against the api keys column
$this->getConnection()->addIndex(
    $userTable,
    $this->getIdxName(
        'liljscart/consumer', 
        'api_key', 
        Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
    ),
    'api_key'
);