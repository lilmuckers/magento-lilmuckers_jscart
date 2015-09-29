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
class Lilmuckers_JsCart_Block_Adminhtml_Consumer_Edit 
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * The constructor to set up the form
     * 
     * @return void
     */
    public function __construct()
    {
        $this->_objectId   = 'id';
        $this->_controller = 'adminhtml_consumer';
        $this->_blockGroup = 'liljscart';

        parent::__construct();
        
        //set the form action
        $this->setFormActionUrl($this->getUrl('*/*/save'));

        $this->_updateButton('save', 'label', Mage::helper('liljscart')->__('Save Consumer'));
        $this->_updateButton('delete', 'label', Mage::helper('liljscart')->__('Delete Consumer'));
    }
    
    /**
     * Get the header text for the page
     * 
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('current_consumer')->getId()) {
            return $this->escapeHtml(Mage::registry('current_consumer')->getAlias());
        } else {
            return Mage::helper('liljscart')->__('New Consumer');
        }
    }
}
