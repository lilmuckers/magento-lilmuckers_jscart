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
 * Consumer Model
 *
 * @category Lilmuckers
 * @package  Lilmuckers_JsCart
 * @author   Patrick McKinley <contact@patrick-mckinley.com>
 * @license  MIT http://choosealicense.com/licenses/mit/
 * @link     https://github.com/lilmuckers/magento-lilmuckers_queue
 */
class Lilmuckers_JsCart_Model_Consumer
    extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource model
     * 
     * @return void
     */
    protected function _construct()
    {
        $this->_init('liljscart/consumer');
    }
    
    /**
     * Before save - create an API key if required
     * 
     * @return Lilmuckers_JsCart_Model_Consumer
     */
    protected function _beforeSave()
    {
        //set the log dates - for shits and giggled
        $_now = Mage::getSingleton('core/date')->gmtDate();
        $this->setData('updated_at', $_now);
        if (!$this->getId()) {
            $this->setData('created_at', $_now);
        }
    
        if (!$this->getApiKey()) {
            $_key = $this->generateNewApiKey();
            $this->setApiKey($_key);
        }
        
        return parent::_beforeSave();
    }
    
    /**
     * Before save - create an API key if required
     * 
     * @return Lilmuckers_JsCart_Model_Consumer
     */
    protected function _afterSave()
    {
        if ($this->hasOriginUrls()) {
            $this->getResource()
                ->saveOriginUrls(
                    $this->getOriginUrls(),
                    $this->getId()
                );
        }
        
        return parent::_afterSave();
    }
    
    /**
     * Generate an API key
     * 
     * @return string
     */
    public function generateNewApiKey()
    {
        // Generates a random string of ten digits
        $_salt      = mt_rand();
        $_secretKey = mt_rand();
        
        // Computes the signature by hashing the salt with the secret key as the key
        $_signature = hash_hmac('sha256', $_salt, $_secretKey, true);
        
        // base64 encode...
        $_apiKey = base64_encode($_signature);

        //ensure it's unique
        if ($this->getResource()->getIdByApiKey($_apiKey)) {
            $_apiKey = $this->generateNewApiKey();
        }
        
        return $_apiKey;
    }
    
    /**
     * Load a consumer by the API key
     * 
     * @param string $apiKey The api key to load by
     * 
     * @return Lilmuckers_JsCart_Model_Consumer
     */
    public function loadByApiKey($apiKey)
    {
        return $this->load($apiKey, 'api_key');
    }
    
    /**
     * Load a consumer by the origin URL they are set up to use
     * 
     * @param string $url The url to load by
     * 
     * @return Lilmuckers_JsCart_Model_Consumer
     */
    public function loadByOrigin($url)
    {
        $_consumerId = $this->getResource()->getIdByOrigin($url);
        if ($_consumerId) {
            $this->load($_consumerId);
        }
        
        return $this;
    }
}
