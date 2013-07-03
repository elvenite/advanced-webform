<?php

class PodioAdvancedFormTextElement extends PodioAdvancedFormElement{
	public function __construct($field, $form, $value) {
		parent::__construct($field, $form, $value);
	}
	
	public function render(){
		$output = '<input type="text" name="sasd">';
		
		return $output;
	}
}

?>
