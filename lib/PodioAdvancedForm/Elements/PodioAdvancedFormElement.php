<?php


abstract class PodioAdvancedFormElement {
	protected $app_field;
	protected $item_field;
	protected $form;
	protected $value;
	protected $name;


	protected $attributes = array(
		'class' => 'span8',
	);
	
	/**
	 * Wrappers around elements
	 * Defaults to form decorators if not set
	 * @var type 
	 */
	protected $decorators = array();

	public function __construct(PodioAppField $app_field, PodioAdvancedForm $form, $item_field = null) {
		if ($app_field->status != "active"){
			throw new ErrorException('Field is not active');
		}
		
		$this->set_app_field($app_field);
		
		$this->form = $form;
		
		if(!$item_field){
			$class_name = 'Podio' . ucfirst($app_field->type) . 'ItemField';
			$this->set_item_field(new $class_name(array(
				'field_id' => $app_field->field_id
			)));
		} else {
			$this->set_item_field($item_field);
			$this->set_attribute('value', $item_field->humanized_value());
		}
		
		// set id
		$this->set_attribute('id', $app_field->field_id);
		// set name
		$this->set_name($app_field->external_id);
		// set placeholder
		$this->set_attribute('placeholder', $app_field->config['label']);
		// set required
		$this->set_attribute('required', (bool) $app_field->config['required']);
		
		// set "special" attributes from description
		// like if description contains [hidden], the field should be hidden
		$description = $app_field->config['description'];
		if ($description){
			preg_match_all('/\[([^[]+)\]/', $description, $matches);

			foreach($matches[0] AS $match){
				$description = str_replace($match, '', $description);
			}
			
			foreach($matches[1] AS $match){
				if (false !== strpos($match, '=')){
					$match_key = substr($match, 0, strpos($match,'='));
					$match_value = substr($match, strpos($match,'=')+1);
					
					$this->set_attribute($match_key, $match_value);
				} else {
					$this->set_attribute($match, true);
				}
			}

			$description = trim($description);
			// set description
			$this->set_attribute('description', $description);
		}
		
		// set type
		$this->set_attribute('type', $app_field->type);
		
		return true;
	}
	
	public function get_attribute($key){
		// backward compability
		if ('name' == $key){
			return $this->get_name();
		}
		
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
		$attributes = $this->attributes;
		$attributes['name'] = $this->get_name();
		return $attributes;
	}
	
	public function get_app_field(){
		return $this->app_field;
	}
	
	public function set_app_field($app_field){
		$this->app_field = $app_field;
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
	
	public function get_name(){
		$name = $this->name;
		if ($this->form->is_sub_form()){
			$name = $this->form->get_field_name_prefix() . '[' . $name . ']';
		}
		
		return $name;
		
	}
	
	public function set_name($name){
		$this->name = (string) $name;
	}
	
	public function is_hidden(){
		return (bool) $this->get_attribute('hidden');
	}
	
	public function is_locked(){
		$locked = $this->get_attribute('locked');
		if (!$locked){
			if ($this->form->is_sub_form()){
				if ($parent = $this->form->get_attribute('parent')) {
					$locked = $parent->get_attribute('locked');
				}
			}
		}
		
		return $locked;
	}
	
	public function get_decorators(){
		return $this->decorators;
	}
	
	public function set_decorators($decorators){
		$this->decorators = $decorators;
	}
	
	public function get_decorator($key){
		if (!array_key_exists($key, $this->decorators)){
			return ($this->form->get_decorator($key)) ?
				$this->form->get_decorator($key) :
				null;
		}
		
		return $this->decorators[$key];
	}
	
	public function set_decorator($key, $value){
		$this->decorators[$key] = $value;
	}
	
	protected function attributes_concat($attributes = null){
		if (!$attributes) {
			$attributes = $this->get_attributes();
		}
		
		$attributes_string = '';
		$ignore = array(
			'description',
		);
		
		foreach($attributes AS $key => $attribute){
			if (in_array($key, $ignore)) continue;
			// if true, then attribute minimization is allowed
			if ($attribute === true){
				$attributes_string .= ' ' . $key;
			} elseif ($attribute != ''){ // empty attributes won't be added
				$attributes_string .= ' ' . $key . '="' . (string) $attribute . '"';
			}
		}
		
		return $attributes_string;
	}
	
	protected function render_element(){
		$attributes = $this->get_attributes();
		$element = '<input';
		$element .= $this->attributes_concat($attributes);
		$element .= '>';
		
		return $element;
	}
	protected function render_locked(){
		$element = '<div class="locked" style="padding:5px 0">';
		if ($this->get_item_field()->values){
			$element .= $this->get_item_field()->humanized_value();
		}
		$element .= '</div>';
		
		return $element;
	}
	
	public function render($element = null, $default_field_decorator = 'field'){
		if ($this->form->is_sub_form()){
			$default_field_decorator = 'sub_field';
		}
		// hidden elements will not even show up as type="hidden", they are completely
		// invisible but can still contain prepopulate values
		if ($this->is_hidden()){
			return '';
		}
		
		if ($this->is_locked()){
			$element = $this->render_locked();
		} else {
			if (!$element){
				$element = $this->render_element();
			}
		}
		
		$description_decorator = '';
		$description = $this->get_attribute('description');
		if ($description){
			$description_decorator = sprintf($this->get_decorator('field_description'),
												$description
											);
		}
		
		$decorator = sprintf($this->get_decorator($default_field_decorator), 
						$this->get_attribute('name'),
						$this->get_attribute('placeholder'),
						$element,
						$description_decorator,
						($this->get_attribute('required')) ? $this->get_decorator('field_required') : ''
					);
		
		return $decorator;
	}

}