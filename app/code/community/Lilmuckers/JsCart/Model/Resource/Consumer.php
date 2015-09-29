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
 * Consumer Resource Model
 *
 * @category Lilmuckers
 * @package  Lilmuckers_JsCart
 * @author   Patrick McKinley <contact@patrick-mckinley.com>
 * @license  MIT http://choosealicense.com/licenses/mit/
 * @link     https://github.com/lilmuckers/magento-lilmuckers_queue
 */
class Lilmuckers_JsCart_Model_Resource_Consumer
    extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize main table and primary field
     * 
     * @return void
     */
    protected function _construct()
    {
        $this->_init('liljscart/consumer', 'consumer_id');
    }
    
    /**
     * After the load - add the apikey and origins to the thing
     *
     * @param Mage_Core_Model_Abstract $object - the consumer object
     *
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        //get the origin URLs
        $_origins = $this->getOriginUrls($object->getId());
        
        //set it on the object
        $object->setData('origin_urls', $_origins);

        return parent::_afterLoad($object);
    }
    
    /**
     * Get the origin URLs for a given consumer
     * 
     * @param int $consumerId The consumer to load for
     * 
     * @return array
     */
    public function getOriginUrls($consumerId = null)
    {
        //if there's no id, just an empty array
        if (is_null($consumerId)) {
            return array();
        }
        
        //build the select
        $_select = $this->_getReadAdapter()
            ->select()
            ->from($this->getTable('liljscart/consumer_origin'), new Zend_Db_Expr('*'))
            ->where('consumer_id = :consumer_id');
            
        //Query the DB
        $_origins = $this->_getReadAdapter()
            ->fetchAll(
                $_select, 
                array(
                    'consumer_id' => $consumerId
                )
            );
        
        //convert the array
        $_output = array();
        foreach ($_origins as $_data) {
            $_output[$_data['consumer_origin_id']] = $_data['origin'];
        }
        
        return $_output;
    }
    
    /**
     * Validate and save the urls
     * 
     * @param array $urls       The origin URLs to save
     * @param int   $consumerId The consumer ID to save against
     * 
     * @return Lilmuckers_JsCart_Model_Resource_Consumer
     */
    public function saveOriginUrls($urls, $consumerId)
    {
        $_extantUrls = $this->getOriginUrls($consumerId);
        $_newUrls    = array();
        $_delUrlIds  = array();
        
        //artrange the data for saving
        foreach ($urls as $_url) {
            if (!in_array($_url, $_extantUrls)) {
                $_newUrls[] = array(
                    'origin'      => $_url, 
                    'consumer_id' => $consumerId
                );
            }
        }
        foreach ($_extantUrls as $_id => $_extantUrl) {
            if (!in_array($_extantUrl, $urls)) {
                $_delUrlIds[] = $_id;
            }
        }
        
        //insert the new URLs
        if (!empty($_newUrls)) {
            $this->_getWriteAdapter()->insertMultiple(
                $this->getTable('liljscart/consumer_origin'),
                $_newUrls
            );
        }
        
        //delete broken ids
        if (!empty($_delUrlIds)) {
            $_cond = array(
                'consumer_origin_id IN(?)' => $_delUrlIds,
                'consumer_id=?'            => $consumerId
            );
            $this->_getWriteAdapter()->delete(
                $this->getTable('liljscart/consumer_origin'), 
                $_cond
            );
        }
        
        return $this;
    }
    
    /**
     * Get a consumer ID by api key
     * 
     * @param string $apiKey the api key to get the id for
     * 
     * @return int
     */
    public function getIdByApiKey($apiKey)
    {
        //build the select
        $_select = $this->_getReadAdapter()
            ->select()
            ->from($this->getMainTable(), new Zend_Db_Expr('consumer_id'))
            ->where('api_key = :api_key');
        
        //Query the DB
        $_consumer = $this->_getReadAdapter()
            ->fetchOne(
                $_select, 
                array(
                    'api_key' => $apiKey
                )
            );
        
        return $_consumer;
    }
    
    /**
     * Get a consumer ID by the origin URL
     * 
     * @param string $url The origin URl to load by
     * 
     * @return int
     */
    public function getIdByOrigin($url)
    {
        //build the select
        $_select = $this->_getReadAdapter()
            ->select()
            ->from($this->getTable('liljscart/consumer_origin'), new Zend_Db_Expr('consumer_id'))
            ->where('origin = :origin');
        
        //Query the DB
        $_consumer = $this->_getReadAdapter()
            ->fetchOne(
                $_select, 
                array(
                    'origin' => $url
                )
            );
        
        return $_consumer;
    }
}
