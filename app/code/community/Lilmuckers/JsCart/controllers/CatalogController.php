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
 * Catalog controller
 *
 * @category Lilmuckers
 * @package  Lilmuckers_JsCart
 * @author   Patrick McKinley <contact@patrick-mckinley.com>
 * @license  MIT http://choosealicense.com/licenses/mit/
 * @link     https://github.com/lilmuckers/magento-lilmuckers_queue
 */
class Lilmuckers_JsCart_CatalogController 
    extends Lilmuckers_JsCart_Controller_Abstract
{
    /**
     * Check if a given product is saleable or not
     * 
     * @return Lilmuckers_JsCart_CatalogController
     */
    public function saleableAction()
    {
        $_skus = $this->getRequest()->getParam('sku');
        $_singleOutput = false;
        
        //normalise the data
        if (!is_array($_skus)) {
            $_singleOutput = true;
            $_skus = array($_skus);
        }
        
        //get only the skus we're interested in
        $_products = Mage::getModel('catalog/product')->getCollection();
        $_products->addAttributeToFilter(
            'sku', 
            array(
                'in' => $_skus
            )
        );
        
        //output generation, scan through the products and add saleable info
        $_output = array();
        foreach ($_products as $_product) {
            $_output[$_product->getSku()] = $_product->isSaleable();
        }
        
        $this->_setOutput($_output);
        return $this->outputResponse();
    }
}