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
class Lilmuckers_JsCart_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Map the data appropriately from the fieldset
     *
     * @param Varien_Object|array $inputData - the input data
     * @param string              $fieldset  - the fieldset
     * @param string              $root      - the root
     * @param bool                $unsetNull - unset null arrays
     *
     * @return array
     */
    public function map($inputData, $fieldset, $root = 'global', $unsetNull = false)
    {
        //get the fieldset data
        $_mapData = Mage::app()->getConfig()->getFieldset($fieldset, $root)->asArray();
        $_map     = array();

        //translate to an associative mapping array
        foreach ($_mapData as $_from => $_info) {
            //the map first
            if (array_key_exists('to', $_info)) {
                $_map[$_from] = $_info['to'];
            }

            //now apply any formatters
            if (array_key_exists('format', $_info)) {
                //if we've got an array of varien object - get the data approriately
                if ($inputData instanceof Varien_Object) {
                    $_value = $inputData->getData($_from);
                } else {
                    $_value = $inputData[$_from];
                }

                $_data = call_user_func(
                    array($this, $_info['format']), 
                    $_value, 
                    $inputData,
                    $_from);

                //set it back appropriately.
                if ($inputData instanceof Varien_Object) {
                    $inputData->setData($_from, $_data);
                } else {
                    $inputData[$_from] = $_data;
                }
            }
        }

        //pass through varien mapper
        $_output = array();
        $_output = Varien_Object_Mapper::accumulateByMap($inputData, $_output, $_map);

        if ($unsetNull) {
            foreach ($_output as $_k => $_v) {
                if (empty($_v)) {
                    unset($_output[$_k]);
                }
            }
        }
        
        foreach ($_map as $_destMap) {
            if (!array_key_exists($_destMap, $_output)) {
                $_output[$_destMap] = null;
            }
        }

        //return the results
        return $_output;
    }
    
    /**
     * Round a number
     * 
     * @param numeric $input The input amount to round
     * 
     * @return int
     */
    protected function _round($input)
    {
        return (int) round($input);
    }
    
    /**
     * Round a currency to 2 DP
     * 
     * @param numeric $input Currency figure to round
     * 
     * @return double
     */
    protected function _roundCurrency($input, $object, $_field)
    {
        $_field = 'cur_'.$_field;
        $_value = Mage::helper('checkout')
            ->getQuote()
            ->getStore()
            ->formatPrice($input, false);
        $object->setData($_field, $_value);
        return (double) (round($input * 100) / 100);
    }
    
    /**
     * Make an explicit boolean
     * 
     * @param mixed $input The input to booleanise
     * 
     * @return bool
     */
    protected function _bool($input)
    {
        return (bool) $input;
    }
    
    /**
     * Return a javascript parsable datetime string with timezone
     * 
     * @param string $input The datetime string
     * 
     * @return string
     */
    protected function _datetime($input)
    {
        $_date = new DateTime($input);
        return $_date->format(DateTime::ISO8601);
    }
    
    /**
     * Convert an input to a double
     * 
     * @param mixed $input The input to doubleise
     * 
     * @return double
     */
    protected function _double($input)
    {
        return (double) $input;
    }
}
