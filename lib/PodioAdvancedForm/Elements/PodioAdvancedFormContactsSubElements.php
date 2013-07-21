<?php

abstract class PodioAdvancedFormContactsSubElement{
	protected $parent;
	
	public function __construct(array $attributes) {
		
		$this->set_name($attributes['name']);
		unset($attributes['name']);
		
		$this->set_parent($attributes['parent']);
		unset($attributes['parent']);
		
		foreach($attributes AS $key => $attribute){
			$this->set_attribute($key, $attribute);
		}
		
		if ($parent_class = $this->parent->get_attribute('class')){
			$this->set_attribute('class', $parent_class);
		}
		
		// special handling of required attribute
		// if parent field is not required, set required to false
		if ($this->get_parent()->get_attribute('required') === false){
			$this->set_attribute('required', false);
		}
	}
	
	protected function get_parent(){
		return $this->parent;
	}
	
	protected function set_parent(PodioAdvancedFormContactElement $parent){
		$this->parent = $parent;
	}
	
	
	public function get_name(){
		$parent = $this->get_parent();
		$name = $parent->get_name();
		$name = $name . '[' . $this->name . ']';
		
		if ($this->get_attribute('multi')){
			$name .= '[]';
		}
		
		return $name;
	}
	
	public function set_name($name){
		$this->name = (string) $name;
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
	
	public function render(){
		// output is:
		// decorator
		// element
		
		$attributes = $this->get_attributes();
		
		$attributes_string = '';
		foreach($attributes AS $key => $attribute){
			// if true, then attribute minimization is allowed
			if ($attribute === true){
				$attributes_string .= ' ' . $key;
			} elseif ($attribute){ // all falsy values won't be added
				$attributes_string .= ' ' . $key . '="' . (string) $attribute . '"';
			}
		}
		
		$element = '<input';
		
		$element .= $attributes_string;
		
		$element .= '>';
		
		$decorator = sprintf($this->parent->get_decorator('sub_field'), 
						$this->get_attribute('name'),
						$this->get_attribute('placeholder'),
						$element,
						'' // description is always empty in these fields
					);
		
		return $decorator;
	}
}

class PodioAdvancedFormContactsSubElementText extends PodioAdvancedFormContactsSubElement{
	protected $attributes = array(
		'type' => 'text',
	);
}

class PodioAdvancedFormContactsSubElementEmail extends PodioAdvancedFormContactsSubElementText{
	protected $attributes = array(
		'type' => 'email',
		'multi' => true,
	);
}
