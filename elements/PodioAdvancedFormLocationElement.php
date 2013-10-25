<?php

class PodioAdvancedFormLocationElement extends PodioAdvancedFormElement{
	
	public function __construct($app_field, $form, $item_field = null) {
		parent::__construct($app_field, $form, $item_field);
		
		$this->set_attribute('type', 'text');
		
		/**
		 * TODO
		 * check status is active
		 * check visibility equals true (config['visible']
		 * add delta field (delta is the sort order)
		 */
	
	}
}

?>
