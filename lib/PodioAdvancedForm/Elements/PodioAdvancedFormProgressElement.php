<?php

class PodioAdvancedFormProgressElement extends PodioAdvancedFormElement{
	
	public function __construct($app_field, $form, $item_field = null) {
		parent::__construct($app_field, $form, $item_field);
		
		$this->set_attribute('type', 'range');
		$this->set_attribute('min', 0);
		$this->set_attribute('max', 100);
		$this->set_attribute('step', 5);
		
		// initial value, can be overridden
		if ($item_field){
			$this->set_attribute('value', $item_field->values[0]['value']);
		} else {
			$this->set_attribute('value', '0');
		}
		
		
		/**
		 * TODO
		 * check status is active
		 * check visibility equals true (config['visible']
		 * add delta field (delta is the sort order)
		 */
	
	}
}

?>
