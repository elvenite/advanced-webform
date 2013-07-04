<?php

class PodioAdvancedFormTextElement extends PodioAdvancedFormElement{
	
	public function __construct($field, $form, $value) {
		parent::__construct($field, $form, $value);
		
		// set type
		if ($this->field->config['settings']['size'] == 'small'){
			$type = 'text';
			
		} else {
			$type = 'textarea';
		}
		$this->set_attribute('type', $type);
		
		/**
		 * TODO
		 * check status is active
		 * check visibility equals true (config['visible']
		 * add delta field (delta is the sort order)
		 */
	
	}
	
	public function render(){
		// output is:
		// decorator
		// element
		
		$attributes = $this->get_attributes();
		// some attributes should not go into the element, like type,
		// description
		// required is a special case as well
		// handle them first
		
		$type = $this->get_attribute('type');
		unset($attributes['type']);
		$description = $this->get_attribute('description');
		unset($attributes['description']);
		$required = $this->get_attribute('required');
		unset($attributes['required']);
		
		$attributes_string = '';
		foreach($attributes AS $key => $attribute){
			$attributes_string .= ' ' . $key . '="' . (string) $attribute . '"';
		}
		
		if ($type == 'text'){
			$element = '<input type="text"';
		} else {
			$element = '<textarea';
		}
		
		if ($required){
			$element .= ' required';
		}
		
		$element .= $attributes_string;
		
		$element .= '>';
		
		if ($type == 'textarea'){
			$element .= '</textarea>';
		}
		
		$description_decorator = '';
		if ($description){
			$description_decorator = sprintf('<span class="help-block">%1$s</span>',
												$description
											);
		}
		
		$decorator = sprintf('<div class="control-group"><label class="control-label" for="%1$s">%2$s</label><div class="controls">%3$s%4$s</div></div>', 
						$this->get_attribute('name'),
						$this->get_attribute('placeholder'),
						$element,
						$description_decorator
					);
		
		return $decorator;
	}
}

?>