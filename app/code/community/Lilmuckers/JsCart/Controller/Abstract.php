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
 * Abstract controller
 *
 * @category Lilmuckers
 * @package  Lilmuckers_JsCart
 * @author   Patrick McKinley <contact@patrick-mckinley.com>
 * @license  MIT http://choosealicense.com/licenses/mit/
 * @link     https://github.com/lilmuckers/magento-lilmuckers_queue
 */
abstract class Lilmuckers_JsCart_Controller_Abstract extends Mage_Core_Controller_Front_Action
{
    /**
     * The errors to send to the browser
     * 
     * @param string
     */
    protected $_errorResponse;
    
    /**
     * The HTML string to send to the browser
     * 
     * @param string
     */
    protected $_htmlResponse;
    
    /**
     * The data to send to the browser
     * 
     * @param mixed
     */
    protected $_dataResponse;
    /**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
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
     * Get the consumer for this request
     * 
     * @return Lilmuckers_JsCart_Model_Consumer
     */
    public function getConsumer()
    {
        return Mage::getSingleton('liljscart/consumer');
    }

    /**
     * Get current active quote instance
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }
    
    /**
     * Execute the validation on the predispatch
     * 
     * @return Lilmuckers_JsCart_Controller_Abstract
     */
    public function preDispatch()
    {
        $this->_verifyOrigin();
        return parent::preDispatch();
    }
    
    /**
     * Verify the origin of the ajax request and apikey, adding the Access-Control-Allow-Origin header
     * 
     * @return Lilmuckers_JsCart_Controller_Abstract
     */
    protected function _verifyOrigin()
    {
        $_response = $this->getResponse();
        $_request = $this->getRequest();

        //load the consumer by API key
        $_apiKey = $_request->getHeader('ApiKey');
        $this->getConsumer()->loadByApiKey($_apiKey);
        
        //verify origin
        if ($this->getConsumer()->getId()) {
            $_origin = $this->getRequest()->getHeader('ApiMaster');
            $_validOrigin = in_array($_origin, $this->getConsumer()->getOriginUrls());
        } else {
            $_origin = $this->_getOriginUrl(true, 'Origin');
            $_validOrigin = $_origin ? true : false;
        }
        
        if (($this->getConsumer()->getId() && $_validOrigin) || ($_validOrigin && $this->getRequest()->isOptions())) {
            $_headers = $_request->getHeader('Access-Control-Request-Headers');
            $_response->setHeader('Access-Control-Allow-Origin', $_origin, true);
            $_response->setHeader('Access-Control-Max-Age', 3600, true);
            $_response->setHeader('Access-Control-Allow-Method', 'POST');
            $_response->setHeader('Access-Control-Allow-Headers', $_headers);
            $_response->setHeader('Access-Control-Allow-Credentials', 'true', true);
        } else {
            //$this->_failCORS('Invalid API key');
        }
        
        return $this;
    }
    
    /**
     * Get the current origin url
     * 
     * @param bool $validated Validate the origin against the DB
     * 
     * @return string|bool
     */
    protected function _getOriginUrl($validated = false, $header = 'Referer')
    {
        //build the origin URL
        $_originUrl = $this->getRequest()->getHeader($header);
        $_urlData   = parse_url($_originUrl);
        
        if (count($_urlData) < 2) {
            return false;
        }
        
        $_origin    = sprintf('%s://%s', $_urlData['scheme'], $_urlData['host']);
        
        //add the port if required.
        if (array_key_exists('port', $_urlData) && !empty($_urlData['port'])) {
            $_origin .= ':'.$_urlData['port'];
        }
        
        if ($validated) {
            $this->getConsumer()->loadByOrigin($_origin);
            if (!$this->getConsumer()->getId()) {
                return false;
            }
        }
        
        return $_origin;
    }
    
    /**
     * Fail the CORS request
     * 
     * @param string $reason The reason it failed
     *
     * @return void
     */
    protected function _failCORS($reason = 'Request Blocked')
    {
        Mage::app()->getRequest()
            ->setControllerName('cms')
            ->setActionName('noRoute')
            ->setDispatched(false);
        Mage::app()->getResponse()->setHeader('Lilmuckers-JSCart', $reason);
    }
    
    /**
     * Output the JSON string
     * 
     * @param array $data the response data
     * 
     * @return string
     */
    protected function _json($data)
    {
        return Mage::helper('core')->jsonEncode($data);
    }
    
    /**
     * Set an error for the browser
     * 
     * @param string $error
     * 
     * @return Lilmuckers_JsCart_Controller_Abstract
     */
    protected function _setError($error)
    {
        $this->_errorResponse = $error;
        return $this;
    }
    
    /**
     * Set a html response for the browser
     * 
     * @param string $html
     * 
     * @return Lilmuckers_JsCart_Controller_Abstract
     */
    protected function _setHtml($html)
    {
        $this->_htmlResponse = $html;
        return $this;
    }
    
    /**
     * Set a data response for the browser
     * 
     * @param array $data
     * 
     * @return Lilmuckers_JsCart_Controller_Abstract
     */
    protected function _setOutput($data)
    {
        $this->_dataResponse = $data;
        return $this;
    }
    
    /**
     * Do a standard return type as JSON
     * 
     * @return string
     */
    protected function _returnData()
    {
        $_output = array(
            'error' => $this->_errorResponse,
            'html'  => $this->_htmlResponse,
            'data'  => $this->_dataResponse,
        );
        return $this->_json($_output);
    }
    
    /**
     * Output the response
     * 
     * @return Lilmuckers_JsCart_Controller_Abstract
     */
    public function outputResponse()
    {
        $this->getResponse()->setBody($this->_returnData());
        return $this;
    }
}
