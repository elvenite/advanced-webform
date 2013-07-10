<?php


class PodioAdvancedFormElement {
	protected $app_field;
	protected $item_field;
	protected $form;
	protected $value;
	
	protected $attributes = array();


	public function __construct(PodioAppField $app_field, PodioAdvancedForm $form, $item_field = null) {
		$this->app_field = $app_field;
		$this->form = $form;
		
		if(!$item_field){
			$class_name = 'Podio' . ucfirst($app_field->type) . 'ItemField';
			$this->item_field = new $class_name(array(
				'field_id' => $app_field->field_id
			));
		}
		
		// set name
		$this->set_attribute('name', $app_field->external_id);
		// set placeholder
		$this->set_attribute('placeholder', $app_field->config['label']);
		// set required
		$this->set_attribute('required', (bool) $app_field->config['required']);
		// set description
		$this->set_attribute('description', $app_field->config['description']);
		
		// set type
		$this->set_attribute('type', $app_field->type);
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
	
	public function get_item_field(){
		return $this->item_field;
	}
	
	public function set_item_field($item_field){
		$this->item_field = $item_field;
	}
	
	public function set_value($values){
		$this->item_field->set_value($values);
	}

}

?>
