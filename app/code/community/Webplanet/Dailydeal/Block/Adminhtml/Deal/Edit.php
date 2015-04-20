<?php

class Webplanet_Dailydeal_Block_Adminhtml_Deal_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'dailydeal';
        $this->_controller = 'adminhtml_deal';

        // Save:
        $this->_updateButton('save', 'label', Mage::helper('adminhtml')->__('Save'));

        // Delete:
        $this->_updateButton('delete', 'label', Mage::helper('adminhtml')->__('Delete'));
        $this->_updateButton('delete', 'onclick', 'deleteItem(\'' . Mage::helper('adminhtml')->__('Are you sure you want to delete?') . '\', \'' . $this->getDeleteUrl() . '\')');
        // Save and Continue Edit
        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);

        $this->_formScripts[] = "
        
            function saveAndContinueEdit()
            {
            	editForm.submit($('edit_form').action+'back/edit/');
            }

            ";
    }

}
