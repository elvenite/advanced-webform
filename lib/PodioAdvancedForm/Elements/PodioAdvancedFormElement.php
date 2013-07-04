<?php


class PodioAdvancedFormElement {
	protected $field;
	protected $form;
	protected $value;
	
	protected $attributes = array();


	public function __construct(PodioAppField $field, PodioAdvancedForm $form, $value = null) {
		$this->field = $field;
		$this->form = $form;
		$this->value = $value;
		
		// set name
		$this->set_attribute('name', $field->external_id);
		// set placeholder
		$this->set_attribute('placeholder', $field->config['label']);
		// set required
		$this->set_attribute('required', (bool) $field->config['required']);
		// set description
		$this->set_attribute('description', $field->config['description']);
		
		// set type
		$this->set_attribute('type', $field->type);
	}
	
	public function get_attribute($key){
		if (!array_key_exists($key, $this->attributes)){
			return null;
		}
		
		return $this->attributes[$key];
	}
	
	public function set_attribute($key, $value){
		$key = (string) $key;
		$this->attributes[$key] = $value;
	}
	
	public function get_attributes(){
		return $this->attributes;
	}
	
	
}

?>
