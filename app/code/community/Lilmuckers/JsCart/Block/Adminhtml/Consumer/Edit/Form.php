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
class Lilmuckers_JsCart_Block_Adminhtml_Consumer_Edit_Form
    extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * The prepare form handler
     * 
     * @return Lilmuckers_JsCart_Block_Adminhtml_Consumer_Edit_Form
     */
    protected function _prepareForm()
    {
        //instantiate the form
        $_form = new Varien_Data_Form(
            array(
                'id'      => 'edit_form',
                'action'  => $this->getData('action'),
                'method'  => 'post',
                'enctype' => 'multipart/form-data'
            )
        );
        
        //the artist
        $_consumer = Mage::registry('current_consumer');

        if ($_consumer->getId()) {
            $_form->addField(
                'consumer_id', 
                'hidden', 
                array(
                    'name' => 'consumer_id',
                )
            );
            $_form->setValues($_consumer->getData());
        }

        $_form->setUseContainer(true);
        $this->setForm($_form);
        return parent::_prepareForm();
    }
}
