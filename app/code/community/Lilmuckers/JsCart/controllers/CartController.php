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
 * Cart controller
 *
 * @category Lilmuckers
 * @package  Lilmuckers_JsCart
 * @author   Patrick McKinley <contact@patrick-mckinley.com>
 * @license  MIT http://choosealicense.com/licenses/mit/
 * @link     https://github.com/lilmuckers/magento-lilmuckers_queue
 */
class Lilmuckers_JsCart_CartController 
    extends Lilmuckers_JsCart_Controller_Abstract
{
    /**
     * The fieldset to map the quote for output
     */
    const QUOTE_FIELDSET_MAP      = 'liljscart_quote_output';
    const QUOTE_ITEM_FIELDSET_MAP = 'liljscart_quote_item_output';
    
    /**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        $_cart = Mage::getSingleton('checkout/cart');
        $_cart->setJsCartConsumer($this->getConsumer());
        return $_cart;
    }

    /**
     * Get checkout session model instance
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }
    
    /**
     * Initialize product instance from request data
     *
     * @param string $sku The sku of the product to load
     * 
     * @return Mage_Catalog_Model_Product || false
     */
    protected function _initProduct($sku)
    {
        $_product = Mage::getModel('catalog/product');
        $_productId = (int) $_product->getIdBySku($sku);
        if ($_productId) {
            $_product->setStoreId(Mage::app()->getStore()->getId())
                ->load($_productId);
            if ($_product->getId()) {
                return $_product;
            }
        }
        return false;
    }
    
    /**
     * Add the requested item to cart
     * 
     * @return Lilmuckers_JsCart_CartController
     */
    public function addAction()
    {
        //get the passed details and the cart to deal with
        $_params = $this->getRequest()->getParams();
        $_cart   = $this->_getCart();
        
        //format the number for locale, but default to 1 if nothing is set.
        if (array_key_exists('qty', $_params) && !empty($_params['qty'])) {
            $filter = new Zend_Filter_LocalizedToNormalized(
                array('locale' => Mage::app()->getLocale()->getLocaleCode())
            );
            $_params['qty'] = $filter->filter($_params['qty']);
        } else {
            $_params['qty'] = 1;
        }
        
        //load the product
        $_product = $this->_initProduct($_params['product']);
        
        if (!$_product) {
            $this->_setError($this->__('Product \'%s\' does not exist on this store.', $_params['product']));
            return $this->outputResponse();
        }
        
        //add the product to the cart
        try {
            $_cart->addProduct($_product, $_params);
            $_cart->save();
            $this->_getSession()->setCartWasUpdated(true);
            $this->_setCartDataResponse();
        } catch(Exception $e) {
            $this->_setError($e->getMessage());
        }
        return $this->outputResponse();
    }
    
    /**
     * Remove the requested item from cart
     * 
     * @return Lilmuckers_JsCart_CartController
     */
    public function delAction()
    {
        $id = (int) $this->getRequest()->getParam('itemId');
        if ($id) {
            try {
                $this->_getCart()->removeItem($id)
                  ->save();
                $this->_setCartDataResponse();
            } catch (Exception $e) {
                $this->_setError($this->__('Cannot remove the item.'));
                Mage::logException($e);
            }
        }
        return $this->outputResponse();
    }
    
    /**
     * Clear the entire cart
     * 
     * @return Lilmuckers_JsCart_CartController
     */
    public function clearAction()
    {
        try {
            $this->_getCart()->truncate()->save();
            $this->_setCartDataResponse();
        } catch (Exception $e) {
            $this->_setError($this->__('Cannot clear the cart.'));
            Mage::logException($e);
        }
        return $this->outputResponse();
    }
    
    /**
     * Test action
     * 
     * @return derp
     */
    public function testAction(){
        $this->_setCartDataResponse();
        return $this->outputResponse();
    }
    
    /**
     * Set the cart data to the response
     * 
     * @return Lilmuckers_JsCart_CartController
     */
    protected function _setCartDataResponse()
    {
        $_cart  = $this->_getCart();
        $_quote = $_cart->getQuote();
        
        //throw in the base quote information
        $_data = Mage::helper('liljscart')
            ->map($_quote, self::QUOTE_FIELDSET_MAP);
        
        //now we do the items
        $_items = array();
        foreach ($_cart->getItems() as $_item) {
            if ($_item->isDeleted()) {
                continue;
            }
            $_itemData = Mage::helper('liljscart')
                ->map($_item, self::QUOTE_ITEM_FIELDSET_MAP);
            $_itemData['thumbnail'] = (string) Mage::helper('catalog/image')
                ->init($_item->getProduct(), 'thumbnail')
                ->resize(75);
            $_items[] = $_itemData;
        }
        $_data['items'] = $_items;
        
        $_data['checkout_link'] = Mage::getUrl(
            'checkout/onepage', array('_secure'=>true)
        );
        
        $this->_setOutput($_data);
        return $this;
    }
}
