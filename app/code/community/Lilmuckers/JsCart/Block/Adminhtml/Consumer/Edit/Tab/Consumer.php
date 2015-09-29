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
 * Adminhtml Consumer edit form 
 *
 * @category Lilmuckers
 * @package  Lilmuckers_JsCart
 * @author   Patrick McKinley <contact@patrick-mckinley.com>
 * @license  MIT http://choosealicense.com/licenses/mit/
 * @link     https://github.com/lilmuckers/magento-lilmuckers_queue
 */
class Lilmuckers_JsCart_Block_Adminhtml_Consumer_Edit_Tab_Consumer
    extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Initialise form
     *
     * @return Lilmuckers_JsCart_Block_Adminhtml_Consumer_Edit_Tab_Consumer
     */
    public function initForm()
    {
        //instantiate the form
        $_form = new Varien_Data_Form();
        $_form->setHtmlIdPrefix('_consumer');
        $_form->setFieldNameSuffix('consumer');
        
        //get the artist
        $_consumer = Mage::registry('current_consumer');
        
        //get a new fieldset
        $_fieldset = $_form->addFieldset(
            'base_fieldset', 
            array(
                'legend' => Mage::helper('liljscart')->__('Consumer Information')
            )
        );
        
        //artist name field
        $_fieldset->addField(
            'alias', 
            'text',
            array(
                'label'    => Mage::helper('liljscart')->__('Name'),
                'name'     => 'alias',
                'required' => true
            )
        );
        
        //artist name field
        $_fieldset->addField(
            'email', 
            'text',
            array(
                'label'    => Mage::helper('liljscart')->__('Email Address'),
                'name'     => 'email',
                'required' => true,
                'class' => 'input-text validate-email',
            )
        );
        
        if ($_consumer->getId()) {
            $_fieldset->addType(
                'api_key',
                'Lilmuckers_JsCart_Block_Adminhtml_Consumer_Edit_Tab_Consumer_Apikey_Element'
            );
            $_fieldset->addField(
                'api_key',
                'api_key',
                array(
                    'label'    => Mage::helper('liljscart')->__('API Key'),
                    'name'     => 'regen_apikey',
                    'required' => false
                )
            );
            
        }
        
        $_fieldset->addType(
            'origin_urls',
            'Lilmuckers_JsCart_Block_Adminhtml_Consumer_Edit_Tab_Consumer_Origins_Element'
        );
        $_fieldset->addField(
            'origin_urls',
            'origin_urls',
            array(
                'label'    => Mage::helper('liljscart')->__('Allowed Origin Urls'),
                'name'     => 'origin_urls',
                'required' => false
            )
        );
        
        //set the values and the form
        $_form->setValues($_consumer->getData());
        $this->setForm($_form);
        
        return $this;
    }
}
