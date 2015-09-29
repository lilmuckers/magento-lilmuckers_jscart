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
 * Connection controller
 *
 * @category Lilmuckers
 * @package  Lilmuckers_JsCart
 * @author   Patrick McKinley <contact@patrick-mckinley.com>
 * @license  MIT http://choosealicense.com/licenses/mit/
 * @link     https://github.com/lilmuckers/magento-lilmuckers_queue
 */
class Lilmuckers_JsCart_ConnectionController extends Lilmuckers_JsCart_Controller_Abstract
{

    /**
     * Execute the validation on the predispatch
     * 
     * @return Lilmuckers_JsCart_Controller_Abstract
     */
    public function preDispatch()
    {
        return Mage_Core_Controller_Front_Action::preDispatch();
    }
    
    /**
     * Execute the validation on the predispatch
     * 
     * @return Lilmuckers_JsCart_Controller_Abstract
     */
    public function connectAction() {
        $_origin = $this->_getOriginUrl(true);
        $_output = '<!DOCTYPE HTML>
        <script src="//cdn.rawgit.com/jpillora/xdomain/0.7.3/dist/xdomain.min.js" master="'.$_origin.'"></script>';
        $this->getResponse()->setBody($_output);
        return $this;
    }
    
    /**
     * Return session information
     * 
     * @return Lilmuckers_JsCart_Controller_Abstract
     */
    public function sessionAction() {
        $this->_setOutput(
            array(
                'sid' => Mage::getSingleton('core/session')->getSessionId(),
                'lt'   => ini_get("session.gc_maxlifetime")
            )    
        );
        return $this->outputResponse();
    }
}
