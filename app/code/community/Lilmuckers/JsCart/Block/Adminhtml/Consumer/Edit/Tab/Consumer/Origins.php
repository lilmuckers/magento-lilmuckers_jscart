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
 * Adminhtml Consumer edit form origin renderer
 *
 * @category Lilmuckers
 * @package  Lilmuckers_JsCart
 * @author   Patrick McKinley <contact@patrick-mckinley.com>
 * @license  MIT http://choosealicense.com/licenses/mit/
 * @link     https://github.com/lilmuckers/magento-lilmuckers_queue
 */
class Lilmuckers_JsCart_Block_Adminhtml_Consumer_Edit_Tab_Consumer_Origins
    extends Mage_Adminhtml_Block_Widget
    implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Form element instance
     *
     * @var Varien_Data_Form_Element_Abstract
     */
    protected $_element;

    /**
     * Define sashes management template file
     *
     * @return void
     */
    public function __construct()
    {
        $this->setTemplate('lilmuckers/jscart/form/origins.phtml');
    }

    /**
     * Render HTML
     *
     * @param Varien_Data_Form_Element_Abstract $element form element
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    /**
     * Set form element instance
     *
     * @param Varien_Data_Form_Element_Abstract $element form element
     *
     * @return Lilmuckers_JsCart_Block_Adminhtml_Consumer_Edit_Tab_Consumer_Origins
     */
    public function setElement(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        return $this;
    }

    /**
     * Retrieve form element instance
     *
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * Prepare global layout
     * Add "Add Origin" button to layout
     *
     * @return Lilmuckers_JsCart_Block_Adminhtml_Consumer_Edit_Tab_Consumer_Origins
     */
    protected function _prepareLayout()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(
                array(
                     'label'   => Mage::helper('liljscart')->__('Add Origin'),
                     'onclick' => 'return originControl.addItem()',
                     'class'   => 'add'
                )
            );
        $button->setName('add_origin_item_button');

        $this->setChild('add_button', $button);
        return parent::_prepareLayout();
    }

    /**
     * Retrieve Add Item button HTML
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
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
    
    /**
     * Prepare sash values
     *
     * @return array
     */
    public function getOriginUrls()
    {
        $data = $this->getConsumer()->getData('origin_urls');

        return $data;
    }

}
