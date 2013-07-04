<?php

require 'PodioAdvancedFormError.php';

require 'Elements/PodioAdvancedFormElement.php';
require 'Elements/PodioAdvancedFormTextElement.php';
require 'Elements/PodioAdvancedFormNumberElement.php';
require 'Elements/PodioAdvancedFormCategoryElement.php';
require 'Elements/PodioAdvancedFormQuestionElement.php';

class PodioAdvancedForm {
	protected $app_id;
	protected $app;
	
	protected $item_id;
	protected $item;
	
	protected $fields;
	
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
		
	public function __construct($app_id, $attributes = array()) {
		if ($attributes){
			$this->set_attributes($attributes);
		}
		
		// TODO set default attributes
		$this->set_attribute('class', 'form-horizontal');
		
		$this->set_app_id($app_id);
		
		if (!$this->app_id){
			throw new PodioAdvancedFormError('App id not set');
		}
		
		$app = PodioApp::get($this->get_app_id());
		
		$this->set_app($app);
		
		$this->set_fields();
	}
	
	public function get_app_id() {
		return $this->app_id;
	}

	public function set_app_id($app_id) {
		$this->app_id = (int) $app_id;
	}

	public function get_app() {
		return $this->app;
	}

	public function set_app(PodioApp $app) {
		$this->app = $app;
	}

	public function get_item_id() {
		return $this->item_id;
	}

	public function set_item_id($item_id) {
		$this->item_id = (int) $item_id;
	}

	public function get_item() {
		return $this->item;
	}

	public function set_item(PodioItem $item) {
		$this->item = $item;
	}
	
	protected function set_fields(){
		// get all fields
		foreach($this->get_app()->fields AS $field){
			$this->add_field($field);
		}
	}
	
	protected function add_field($field){
		switch ($field->type) {
			case 'text':
				$this->fields[] = new PodioAdvancedFormTextElement($field, $this, ''); // TODO third attribute, add item values
				break;
			case 'category':
				$this->fields[] = new PodioAdvancedFormCategoryElement($field, $this, ''); // TODO third attribute, add item values
				break;
			case 'question':
				$this->fields[] = new PodioAdvancedFormQuestionElement($field, $this, ''); // TODO third attribute, add item values
				break;
			case 'number':
				$this->fields[] = new PodioAdvancedFormNumberElement($field, $this, ''); // TODO third attribute, add item values
				break;
			default:
				break;
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
		
		foreach($this->fields AS $field){
			
			$output[] = $field->render();
		}
		
		$output[] = '</form>';
		
		return implode('', $output);
	}


}

?>
