<?php
class Webplanet_Dailydeal_Block_Adminhtml_Deals extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
        $this->_controller = 'adminhtml_deal';
		$this->_blockGroup = 'dailydeal';
		$this->_headerText = 'Daily Deal Management';
		$this->_addButtonLabel = 'Add Daily Deal';

		parent::__construct();


	}



	

}