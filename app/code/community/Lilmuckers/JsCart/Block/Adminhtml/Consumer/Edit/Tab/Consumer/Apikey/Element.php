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
 * Adminhtml Consumer edit form API Key
 *
 * @category Lilmuckers
 * @package  Lilmuckers_JsCart
 * @author   Patrick McKinley <contact@patrick-mckinley.com>
 * @license  MIT http://choosealicense.com/licenses/mit/
 * @link     https://github.com/lilmuckers/magento-lilmuckers_queue
 */
class Lilmuckers_JsCart_Block_Adminhtml_Consumer_Edit_Tab_Consumer_Apikey_Element
    extends Varien_Data_Form_Element_Text
{
    /**
     * Override something
     * 
     * @return string
     */
    public function getElementHtml()
    {
        return "<strong>".$this->getConsumer()->getApiKey()."</strong> <br /> <input type=\"checkbox\" name=\"".$this->getName()."\" value=\"1\" /> ".Mage::helper('liljscart')->__("Regenerate Key");
    }
    
    /**
     * Get the current consumer
     * 
     * @return Lilmuckers_JsCart_Model_Consumer
     */
    public function getConsumer()
    {
        return Mage::registry('current_consumer');
    }
}
