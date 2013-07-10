<?php

class PodioAdvancedFormNumberElement extends PodioAdvancedFormElement{
	
	public function __construct($app_field, $form, $item_field = null) {
		parent::__construct($app_field, $form, $item_field);
		
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
		
		$description = $this->get_attribute('description');
		unset($attributes['description']);
		$required = $this->get_attribute('required');
		unset($attributes['required']);
		
		$attributes_string = '';
		foreach($attributes AS $key => $attribute){
			$attributes_string .= ' ' . $key . '="' . (string) $attribute . '"';
		}
		
		$element = '<input';
		
		
		if ($required){
			$element .= ' required';
		}
		
		$element .= $attributes_string;
		
		$element .= '>';
		
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
