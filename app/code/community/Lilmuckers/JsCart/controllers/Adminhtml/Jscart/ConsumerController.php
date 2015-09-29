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
 * Adminhtml Consumer Controller
 *
 * @category Lilmuckers
 * @package  Lilmuckers_JsCart
 * @author   Patrick McKinley <contact@patrick-mckinley.com>
 * @license  MIT http://choosealicense.com/licenses/mit/
 * @link     https://github.com/lilmuckers/magento-lilmuckers_queue
 */
class Lilmuckers_JsCart_Adminhtml_Jscart_ConsumerController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Load the consumer and register it
     * 
     * @param string $idFieldName The field to load from
     * 
     * @return Lilmuckers_JsCart_Model_Consumer
     */
    protected function _initConsumer($idFieldName = 'id')
    {
        //set the title
        $this->_title($this->__('JS Cart Consumers'))
            ->_title($this->__('Manage Consumers'));
        
        //instantiate the consumer
        $_consumerId = (int) $this->getRequest()->getParam($idFieldName);
        $_consumer   = Mage::getModel('liljscart/consumer');
        
        //load it explicitly
        if ($_consumerId) {
            $_consumer->load($_consumerId);
        }
        
        //register the artist
        Mage::register('current_consumer', $_consumer);
        
        //send it back
        return $_consumer;
    }
    
    /**
     * The manage consumer grid
     * 
     * @return Lilmuckers_JsCart_Adminhtml_Jscart_ConsumerController
     */
    public function indexAction()
    {
        $this->_title($this->__('JS Cart Consumers'));

        $this->loadLayout();
        $this->_setActiveMenu('sales/liljscart/consumers');
        $this->renderLayout();
        return $this;
    }
    
    /**
     * Product grid for AJAX request
     * 
     * @return Lilmuckers_JsCart_Adminhtml_Jscart_ConsumerController
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
        return $this;
    }
    
    /**
     * Edit the consumer
     * 
     * @return Lilmuckers_JsCart_Adminhtml_Jscart_ConsumerController
     */
    public function editAction()
    {
        //initialise the consumer and load the layout
        $this->_initConsumer();
        $this->loadLayout();
        
        //get the registered consumer
        $_consumer = Mage::registry('current_consumer');
        
        //set the artist to the title
        $this->_title($_consumer->getId() ? $_consumer->getAlias() : $this->__('New Consumer'));

        //set the active menu item
        $this->_setActiveMenu('sales/liljscart/consumers/new');
        
        //render the layout and finish
        $this->renderLayout();
        return $this;
    }

    /**
     * Create new consumer action
     * 
     * @return Lilmuckers_JsCart_Adminhtml_Jscart_ConsumerController
     */
    public function newAction()
    {
        $this->_forward('edit');
        return $this;
    }
    
    /**
     * Save the consumer data to the database
     * 
     * @return Lilmuckers_JsCart_Adminhtml_Jscart_ConsumerController
     */
    public function saveAction()
    {
        //the data for the thing
        $_consumerId = $this->getRequest()->getParam('consumer_id');
        
        //the post data
        $_data = $this->getRequest()->getPost();
        if ($_data) {
            $_artist = $this->_initConsumerSave();
            
            try {
                $_artist->save();
                
                $_redirectBack = false;
                $this->_getSession()->addSuccess($this->__('The consumer has been saved.'));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage())
                    ->setArtistData($_data);
                $_redirectBack = true;
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage());
                $_redirectBack = true;
            }
        }
        
        if ($_redirectBack) {
            $this->_redirect(
                '*/*/edit', array(
                    'id'       => $_consumerId,
                    '_current' => true
                )
            );
        } else {
            $this->_redirect('*/*/');
        }
    }
    
    /**
     * Delete the given consumer
     * 
     * @return Lilmuckers_JsCart_Adminhtml_Jscart_ConsumerController
     */
    public function deleteAction()
    {
        //get the consumer we want to delete
        $_consumer = $this->_initConsumer();
        
        //get the name of the consimer to delete
        $_name = $_consumer->getAlias();
        
        try {
            //try to delete the artist
            $_consumer->delete();
            //success message, then delete
            Mage::getSingleton('adminhtml/session')
                ->addSuccess($this->__('Consumer \'%s\' has been deleted', $_name));
            // go to grid
            $this->_redirect('*/*/');
        } catch(Exception $e) {
            // display error message
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            // go back to edit form
            $this->_redirect('*/*/edit', array('id' => $_consumer->getId()));
        
        }
        
        return $this;
    }
    
    /**
     * Set up the consumer to be saved
     * 
     * @return Lilmuckers_JsCart_Model_Consumer
     */
    protected function _initConsumerSave()
    {
        //load the consumer
        $_consumer = $this->_initConsumer('consumer_id');
        
        //get the loaded data
        $_data = $this->getRequest()->getPost();
        
        //get the main consumer data
        $_consumerData = $_data['consumer'];
        
        //format the origins
        foreach ($_consumerData['origin_urls'] as $_key => $_origin) {
            if (array_key_exists('origin', $_origin) && !$_origin['delete']) {
                $_consumerData['origin_urls'][$_key] = $_origin['origin'];
            } else {
                unset($_consumerData['origin_urls'][$_key]);
            }
        }
        
        //regen the apikey
        if (isset($_consumerData['regen_apikey']) && $_consumerData['regen_apikey'] == 1) {
            $_consumer->setApiKey(false);
        }
        
        //assign the data to the consumer object
        $_consumer->addData($_consumerData);
        
        return $_consumer;
    }
}
