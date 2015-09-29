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
 * Adminhtml Consumer edit form tabs container
 *
 * @category Lilmuckers
 * @package  Lilmuckers_JsCart
 * @author   Patrick McKinley <contact@patrick-mckinley.com>
 * @license  MIT http://choosealicense.com/licenses/mit/
 * @link     https://github.com/lilmuckers/magento-lilmuckers_queue
 */
class Lilmuckers_JsCart_Block_Adminhtml_Consumer_Edit_Tabs
    extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Set up the tabs
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('consumer_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('liljscart')->__('Consumer Information'));
    }
    
    /**
     * Build the tabs
     * 
     * @return Lilmuckers_JsCart_Block_Adminhtml_Consumer_Edit_Tabs
     */
    protected function _beforeToHtml()
    {
        //the general edit tab
        $this->addTab(
            'consumer', 
            array(
                'label'   => Mage::helper('liljscart')->__('Consumer Information'),
                'content' => $this->getLayout()
                    ->createBlock('liljscart/adminhtml_consumer_edit_tab_consumer')
                    ->initForm()
                    ->toHtml(),
                'active'  => Mage::registry('current_consumer')->getId() ? false : true
            )
        );

        $this->_updateActiveTab();
        return parent::_beforeToHtml();
    }
    
    /**
     * Set the active tab thing
     * 
     * @return Lilmuckers_JsCart_Block_Adminhtml_Consumer_Edit_Tabs
     */
    protected function _updateActiveTab()
    {
        $_tabId = $this->getRequest()->getParam('tab');
        if ($_tabId) {
            $_tabId = preg_replace("#{$this->getId()}_#", '', $_tabId);
            if ($_tabId) {
                $this->setActiveTab($_tabId);
            }
        }
        return $this;
    }
}
