<?php

require_once 'PodioAdvancedFormError.php';

require_once 'elements/PodioAdvancedFormElement.php';
require_once 'elements/PodioAdvancedFormTextElement.php';
require_once 'elements/PodioAdvancedFormNumberElement.php';
require_once 'elements/PodioAdvancedFormProgressElement.php';
require_once 'elements/PodioAdvancedFormLocationElement.php';
require_once 'elements/PodioAdvancedFormDurationElement.php';
require_once 'elements/PodioAdvancedFormMoneyElement.php';
require_once 'elements/PodioAdvancedFormDateElement.php';
require_once 'elements/PodioAdvancedFormCategoryElement.php';
require_once 'elements/PodioAdvancedFormContactElement.php';
require_once 'elements/PodioAdvancedFormQuestionElement.php';
require_once 'elements/PodioAdvancedFormAppElement.php';
require_once 'elements/PodioAdvancedFormEmbedElement.php';
require_once 'elements/PodioAdvancedFormFileElement.php';
require_once 'elements/PodioAdvancedFormImageElement.php';

class PodioAdvancedForm {
	protected $app;
	protected $item;
	protected $error = false;
	
	protected $elements;
	
	protected $is_sub_form = false;
	
	protected $files;
	
	// used to prefix form fields in sub forms
	// the field "name" in an app reference field "company"
	// would get the name attribute company[name]
	// prefix should not contain the last [] surrounding "name"
	// as the element will take care of that.
	protected $field_name_prefix = '';
	
	
	/**
	 * field and parent_field
	 *   1 name attribute
	 *   2 label, also defaults as placeholder
	 *   3 element, the actual input element
	 *   4 description decorator, only if there is a description
	 *   5 required decorator
	 * field_description
	 *   1 description
	 * sub_sub_field - used when a subform has contact sub elements
	 * @var array 
	 */
	
	protected $decorators = array(
		'field' => '<div class="control-group"><label class="control-label" for="%1$s">%2$s%5$s</label><div class="controls">%3$s%4$s</div></div>',
		'field_required' => ' <span class="required">*</span>',
		'field_description' => '<small class="help-block muted">%1$s</small>',
		'parent_field' => '<fieldset class="well"><legend>%2$s</legend>%4$s%3$s</fieldset>',
		'sub_field' => '<div class="control-group"><label class="control-label" for="%1$s">%2$s%5$s</label><div class="controls">%3$s%4$s</div></div>',
		'sub_parent_field' => '<div class="control-group"><label class="control-label" for="%1$s">%2$s%5$s<br>%4$s</label><div class="controls">%3$s</div><hr></div>',
		'sub_sub_field' => '<label for="%1$s">%2$s%5$s</label>%3$s%4$s',
		
	);
	
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
	
	protected $attributes = array(
		'submit_value' => 'Submit',
		'class' => 'podio-advanced-form form-horizontal',
		'method' => self::METHOD_POST,
	);
		
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
			$this->set_item( $item );
		} else {
			
			$this->set_item( new PodioItem(array(
				'app' => $this->get_app(),
			)));
		}
		
		unset($attributes['item']);
		unset($attributes['item_id']);
		
		// set is_sub_form
		if (isset($attributes['is_sub_form'])){
			$this->set_is_sub_form($attributes['is_sub_form']);
			unset($attributes['is_sub_form']);
		}
		
		if ($attributes){
			$this->add_attributes($attributes);
		}
		
		$this->set_elements();
	}

	/**
	 * 
	 * @return PodioApp
	 */
	public function get_app() {
		return $this->app;
	}

	public function set_app(PodioApp $app) {
		$this->app = $app;
	}

	/**
	 * Returns the Podio item
	 * @return PodioItem
	 */
	public function get_item() {
		return $this->item;
	}

	public function set_item(PodioItem $item) {
		$this->item = $item;
	}
	
	/**
	 * 
	 * @return bool
	 */
	public function is_sub_form(){
		return $this->is_sub_form;
	}
	
	public function set_is_sub_form($sub_form){
		$this->is_sub_form = (bool) $sub_form;
	}
	
	/**
	 * Returns an instance of a form element
	 * @param string $external_id
	 * @return PodioAdvancedFormElement
	 */
	public function get_element($external_id){
		if (!array_key_exists($external_id, $this->elements)){
			return null;
		}
		
		return $this->elements[$external_id];
	}


	protected function set_elements(){
		// get all fields
		foreach($this->get_app()->fields AS $app_field){
			if ($app_field->status != "active")	continue;
			$key = $app_field->external_id;
			$item_field = null;
//			var_dump($this->item->fields);
//			echo '<hr>';
			if ($this->item->fields){
				$item_field = $this->item->field($key);
			}
			
			$attributes = $this->get_field_attributes($app_field);
			
			$this->set_element($app_field, $item_field, $attributes);
		}
		
		// is file uploads allowed?
		// Then add a file input element
		if ($this->get_app()->config['allow_attachments']){
			$app_field = new PodioAppField(array(
				'field_id' => PHP_INT_MAX,
				'status' => 'active',
				'type' => 'file',
				'external_id' => 'files', // external_id is used as input name
				'config' => array(
					'label' => 'Files',
					'required' => false,
					'description' => '',
				)
			));
			
			$this->set_element($app_field);
		}
	}
	
	protected function set_element($app_field, $item_field = null, $attributes = null){
		$element = false;
		$class_name = 'PodioAdvancedForm' . ucfirst($app_field->type) . 'Element';
		
		if (class_exists($class_name)){
			try {
				$element = new $class_name($app_field, $this, $item_field, $attributes);
			} catch (Exception $e){
				$element = false;
			}
			// App references are a special case
		}
		
		if ($element){
			$this->elements[$app_field->external_id] = $element;
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
	
	public function get_field_attributes($app_field){
		if (!isset($this->attributes['fields'])){
			return null;
		}
		
		if (array_key_exists($app_field->external_id, $this->attributes['fields'])){
			return $this->attributes['fields'][$app_field->external_id];
		}
		
		if (array_key_exists($app_field->app_id, $this->attributes['fields'])){
			return $this->attributes['fields'][$app_field->app_id];
		}
		
		return null;
	}
	
	public function get_files(){
		return $this->files;
	}
	
	public function add_file($file){
		if (!is_array($this->files)){
			$this->files = array();
		}
		
		$this->files[] = $file;
	}
	
	public function add_files($files){
		if (is_array($files)){
			foreach($files AS $file){
				$this->add_file($file);
			}
		}
	}
	
	public function set_files($files){
		$this->files = $files;
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
	
	public function get_field_name_prefix(){
		return $this->field_name_prefix;
	}
	
	public function set_field_name_prefix($prefix){
		$this->field_name_prefix = $prefix;
	}
	
	public function get_decorators(){
		return $this->decorators;
	}
	
	public function set_decorators($decorators){
		$this->decorators = $decorators;
	}
	
	public function get_decorator($key){
		if (!array_key_exists($key, $this->decorators)){
			return null;
		}
		
		return $this->decorators[$key];
	}
	
	public function set_decorator($key, $value){
		$this->decorators[$key] = $value;
	}
	
	public function __toString() {
		return $this->render();
	}
	
	public function render(){
		$output = array();
		
		if (!$this->is_sub_form()){
			$attributes = $this->get_attributes();
			unset($attributes['submit_value']);
			$head = '<form';
			foreach($attributes AS $key => $value){
				// if true, then attribute minimization is allowed
				if ($value === true){
					$head .= ' ' . $key;
				} elseif ($value){ // all falsy values won't be added
					$head .= ' ' . $key . '="' . (string) $value . '"';
				}	
			}
			$head .= '>';
			
			$output[] = $head;
		}

		foreach($this->elements AS $field){
			try {
				$output[] = $field->render();
			} catch (Exception $e){
				var_dump($field);
			}
		}
		
		if (!$this->is_sub_form()){
			$output[] = '<div class="form-actions">
				<input type="submit" class="btn btn-primary" value="' . $this->get_attribute('submit_value') . '">
			</div>';
			
			$output[] = '</form>';
		}
		
		return implode('', $output);
	}
	
	public function set_values($data, $files = array()){
		foreach($this->elements AS $key => $element){
			if (isset($data[$key])){
				$element->set_value($data[$key]);
				$this->item->add_field($element->get_item_field());
			} elseif (isset($files[$key])){
				$element->set_value($files[$key]);
				// if element is the attachment field, not an image or similar
				// add to the item files attribute
				// otherwise add item field to item
				if ($key == 'files'){
					if (!empty($files['files']['name'][0])){
						$this->add_files($element->get_files());
					}
				} else {
					$this->item->add_field($element->get_item_field());
				}
			}
		}
	}
	
	public function get_error(){
		return $this->error;
	}
	
	public function set_error($message){
		$this->error = (string) $message;
	}
	
	/**
	 * Save $this->item (PodioItem) to Podio
	 * @return int $item_id 
	 */
	public function save(){
		try {

			$result = $this->item->save();
			// if item is update, result will be an array with revision id
			// + title. We always want this function to result the item_id
			if (is_array($result)){
				$item_id = $this->item->item_id;
			} else {
				$item_id = $result;
			}

			// if $this->item->files is a none empty array
			// attach it to the newly created item.
			if ($item_id && $this->get_files())
			{
				foreach($this->get_files() AS $file){
					PodioFile::attach($file->file_id, array(
						'ref_type' => 'item',
						'ref_id' => $item_id,
					));
				}

			}
		} catch (PodioError $e){
			$this->set_error($e->body['error_description']);
		}
		catch (Exception $e){
			$this->set_error($e->getMessage());
		}
		
		if ($this->error){
			return false;
		}
		
		return $item_id;
	}


}

?>
