<?php

require 'PodioAdvancedFormError.php';

require 'Elements/PodioAdvancedFormElement.php';
require 'Elements/PodioAdvancedFormTextElement.php';
require 'Elements/PodioAdvancedFormNumberElement.php';
require 'Elements/PodioAdvancedFormProgressElement.php';
require 'Elements/PodioAdvancedFormLocationElement.php';
require 'Elements/PodioAdvancedFormDurationElement.php';
require 'Elements/PodioAdvancedFormMoneyElement.php';
require 'Elements/PodioAdvancedFormDateElement.php';
require 'Elements/PodioAdvancedFormCategoryElement.php';
require 'Elements/PodioAdvancedFormQuestionElement.php';

class PodioAdvancedForm {
	protected $app;
	protected $item;
	
	protected $elements;
	
	    /**#@+
     * Method type constants
     */
    const METHOD_DELETE = 'delete';
    const METHOD_GET    = 'get';
    const METHOD_POST   = 'post';
    const METHOD_PUT    = 'put';
	
	protected $methods = array(
		self::METHOD_DELETE,
		self::METHOD_GET,
		self::METHOD_POST,
		self::METHOD_PUT,
	);
    /**#@-*/

    /**#@+
     * Encoding type constants
     */
    const ENCTYPE_URLENCODED = 'application/x-www-form-urlencoded';
    const ENCTYPE_MULTIPART  = 'multipart/form-data';
	
	protected $method;
	protected $action;
	protected $enctype;
	
	protected $attributes = array();
		
	public function __construct($attributes = array()) {
		// setup app
		if(isset($attributes['app']) && $attributes['app'] instanceof PodioApp){
			$this->set_app($attributes['app']);
		} elseif (isset($attributes['app_id']) && $attributes['app_id']){
			$this->set_app( PodioApp::get($attributes['app_id']) );
		} else {
			throw new PodioFormError('App or app id must be set.');
		}
		
		unset($attributes['app']);
		unset($attributes['app_id']);
		
		// setup item
		if(isset($attributes['item']) && $attributes['item'] instanceof PodioItem){
			$this->set_item($attributes['item']);
		} elseif (isset($attributes['item_id']) && $attributes['item_id']){
			$item = PodioItem::get($attributes['item_id']);
			$item->app = $this->get_app();
			$this->set_item( $item );
		} else {
			$this->set_item( new PodioItem(array(
				'app' => $this->get_app(),
			)));
		}
		
		unset($attributes['item']);
		unset($attributes['item_id']);
		
		
		if ($attributes){
			$this->set_attributes($attributes);
		}
		
		// TODO set default attributes
		$this->set_attribute('class', 'form-horizontal');
		
		$this->set_elements();
	}

	public function get_app() {
		return $this->app;
	}

	public function set_app(PodioApp $app) {
		$this->app = $app;
	}

	public function get_item() {
		return $this->item;
	}

	public function set_item(PodioItem $item) {
		$this->item = $item;
	}
	
	protected function set_elements(){
		// get all fields
		foreach($this->get_app()->fields AS $field){
			$this->set_element($field);
		}
	}
	
	protected function set_element($field){
		$element = false;
		$class_name = 'PodioAdvancedForm' . ucfirst($field->type) . 'Element';
		if (class_exists($class_name)){
			$element = new $class_name($field, $this); // TODO third attribute, add item
		}
		
		if ($element){
			$this->elements[$field->external_id] = $element;
		}
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
	
	public function set_attributes(array $attributes){
		$this->clear_attributes();
		$this->add_attributes($attributes);
	}
	
	public function add_attributes(array $attributes){
		foreach($attributes AS $key => $attribute){
			$this->set_attribute($key, $attribute);
		}
	}
	
	public function clear_attributes(){
		$this->attributes = array();
	}
	
	public function remove_attribute($key){
		if (array_key_exists($key, $this->attributes)) {
            unset($this->attributes[$key]);
            return true;
        }

        return false;
	}
	
	public function get_method(){
		if (null === ($method = $this->get_attribute('method'))){
			$method = self::METHOD_POST;
		}
		
		return strtolower($method);
		
		
	}
	public function set_method($method){
		$method = strtolower($method);
		if (!in_array($method, $this->methods)){
			throw new PodioAdvancedFormError('"' . $method . '" is not a valid form method.');
		}
		
		$this->set_attribute('method', $method);
	}
	
	public function get_action(){
		$action = $this->get_attribute('action');
        if (null === $action) {
            $action = '';
            $this->set_attribute($action);
        }
        return $action;
	}
	public function set_action($action){
		$this->set_attribute('action', (string) $action);
	}
	
	public function get_enctype(){
		if (null === ($enctype = $this->get_attribute('enctype'))){
			$entype = self::ENCTYPE_URLENCODED;
			$this->set_attribute('enctype', $entype);
		}
		
		return $enctype;
		
	}
	public function set_enctype($enctype){
		$this->set_attribute('enctype', $enctype);
	}
	
	public function __toString() {
		return $this->render();
	}
	
	public function render(){
		$output = array();
		
		$head = '<form';
		foreach($this->get_attributes() AS $key => $value){
			$head .= ' ' . $key . '="' . (string) $value . '"';
		}
		$head .= '>';
		
		$output[] = $head;
		
		foreach($this->elements AS $field){
			
			$output[] = $field->render();
		}
		
		$output[] = '<div class="form-actions">
			<input type="submit" class="btn btn-primary" value="Save changes">
		</div>';
		
		$output[] = '</form>';
		
		return implode('', $output);
	}
	
	public function set_values($data){
		foreach($this->elements AS $key => $element){
			if (isset($data[$key])){
				$element->set_value($data[$key]);
				$this->item->add_field($element->get_item_field());
			}
		}
	}
	
	public function save(){
		$this->item->save();
	}


}

?>
