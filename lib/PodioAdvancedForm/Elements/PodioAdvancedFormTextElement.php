<?php

class PodioAdvancedFormTextElement extends PodioAdvancedFormElement{
	
	public function __construct($app_field, $form, $item_field = null) {
		parent::__construct($app_field, $form, $item_field);
		
		// set type
		if ($this->app_field->config['settings']['size'] == 'small'){
			$type = 'text';
			
		} else {
			$type = 'textarea';
			$this->set_attribute('rows', 6);
			if ($item_field){
				$value = $item_field->values[0]['value'];
				$value = str_replace('</p><p>', "\n\n", $value);
				$value = strip_tags($value);
				$this->set_attribute('value', $value);
			}
		}
		$this->set_attribute('type', $type);
		
		/**
		 * TODO
		 * check status is active
		 * check visibility equals true (config['visible']
		 * add delta field (delta is the sort order)
		 */
	
	}
	
	/**
	 * Renders the input field
	 * This method HAS to return parent::render() with an optional element
	 * string, this way parent::render() can decide whether to display the
	 * element or not. If it happened to have hidden=true
	 * @return type
	 */
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
		
		if ($type == 'text'){
			$element = '<input type="text"';
		} else {
			unset($attributes['value']);
			$element = '<textarea';
		}
		
		$element .= $this->attributes_concat($attributes);
		
		$element .= '>';
		
		if ($type == 'textarea'){
			$element .= $this->get_attribute('value');
			$element .= '</textarea>';
		}
		
		return parent::render($element);
	}
}

?>
