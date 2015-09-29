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
class Lilmuckers_JsCart_Block_Adminhtml_Consumer_Edit_Tab_Consumer_Origins_Element
    extends Varien_Data_Form_Element_Text
{
    /**
     * Constant to define the origin input renderer
     */
    const INPUT_RENDERER_CLASS = 'liljscart/adminhtml_consumer_edit_tab_consumer_origins';

    /**
     * Apply a custom renderer
     *
     * @param array $attributes attributes list
     *
     * @return void
     */
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        $this->_renderer = Mage::app()->getLayout()->createBlock(self::INPUT_RENDERER_CLASS);
    }

    /**
     * We don't want it to override our special renderer
     *
     * @param Varien_Data_Form_Element_Renderer_Interface $renderer renderer object
     *
     * @return Lilmuckers_JsCart_Block_Adminhtml_Consumer_Edit_Tab_Consumer_Origins_Element
     * @SuppressWarnings(PHPMD)
     */
    public function setRenderer(Varien_Data_Form_Element_Renderer_Interface $renderer)
    {
        return $this;
    }
}
