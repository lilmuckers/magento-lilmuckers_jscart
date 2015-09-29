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
 * Adminhtml Consumer grid container
 *
 * @category Lilmuckers
 * @package  Lilmuckers_JsCart
 * @author   Patrick McKinley <contact@patrick-mckinley.com>
 * @license  MIT http://choosealicense.com/licenses/mit/
 * @link     https://github.com/lilmuckers/magento-lilmuckers_queue
 */
class Lilmuckers_JsCart_Block_Adminhtml_Consumer_Grid 
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set up the artist grid
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('consumerGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('alias');
        $this->setSaveParametersInSession(true);
    }
    
    /**
     * Set up the collection for the grid to use
     * 
     * @return WMG_Artist_Block_Adminhtml_Artist_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('liljscart/consumer_collection');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Define grid columns
     *
     * @return WMG_Artist_Block_Adminhtml_Artist_Grid
     */
    protected function _prepareColumns()
    {        
        //artist name
        $this->addColumn(
            'alias', 
            array(
                'header' => $this->__('Consumer Name'),
                'type'   => 'text',
                'index'  => 'alias',
                'width'  => '150'
            )
        );
        //updated date
        $this->addColumn(
            'email', 
            array(
                'header' => $this->__('Consumer Email'),
                'type'   => 'text',
                'index'  => 'email',
                'width'  => '150'
            )
        );
        
        //created date
        $this->addColumn(
            'created_at', 
            array(
                'header' => $this->__('Consumer Created At'),
                'type'   => 'datetime',
                'align'  => 'center',
                'index'  => 'created_at',
                'width'  => '150'
            )
        );
        //updated date
        $this->addColumn(
            'updated_at', 
            array(
                'header' => $this->__('Consumer Updated At'),
                'type'   => 'datetime',
                'align'  => 'center',
                'index'  => 'updated_at',
                'width'  => '150'
            )
        );
        
        //actions 
        $this->addColumn(
            'action',
            array(
                'header'    =>  Mage::helper('liljscart')->__('Action'),
                'width'     => '50',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('liljscart')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
            )
        );
        return parent::_prepareColumns();
    }

    /**
     * Get the onClick URL for the rows
     * 
     * @param WMG_Artist_Model_Artist $row The row in question
     * 
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id'=>$row->getId()));
    }
    
    /**
     * Get the filter grid
     * 
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid');
    }
}
