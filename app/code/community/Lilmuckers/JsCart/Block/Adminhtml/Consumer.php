<?php
/**
 * Magento javascript cart management module
 *
 * @category  Lilmuckers
 * @package   Lilmuckers_JsCart
 * @copyright Copyright (c) 2014 Patrick McKinley (http://www.patrick-mckinley.com)
 * @license   http://choosealicense.com/licenses/mit/
 */

/**
 * Adminhtml Consumer grid container
 *
 * @category Lilmuckers
 * @package  Lilmuckers_JsCart
 * @author   Patrick McKinley <contact@patrick-mckinley.com>
 * @license  MIT http://choosealicense.com/licenses/mit/
 * @link     https://github.com/lilmuckers/magento-lilmuckers_queue
 */
class Lilmuckers_JsCart_Block_Adminhtml_Consumer 
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * The grid container constructor
     * 
     * @return void
     */
    public function __construct()
    {
        $this->_controller     = 'adminhtml_consumer';
        $this->_blockGroup     = 'liljscart';
        $this->_headerText     = Mage::helper('liljscart')->__('Manage Consumers');
        $this->_addButtonLabel = Mage::helper('liljscart')->__('Add New Consumer');
        parent::__construct();
    }

}
