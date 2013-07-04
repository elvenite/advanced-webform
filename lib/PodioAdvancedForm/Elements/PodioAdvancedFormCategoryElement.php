<?php

class PodioAdvancedFormCategoryElement extends PodioAdvancedFormElement{
	
	public function __construct($field, $form, $value) {
		parent::__construct($field, $form, $value);
		
		// set multiple
		$this->set_attribute('multiple', $field->config['settings']['multiple']);
		
		$this->set_attribute('options', $field->config['settings']['options']);
		
		// set type to checkbox or radio depending on if category allows multiple
		// values, also change the name is multiple = true, ad [] to indicate
		// array values
		
		$name = $this->get_attribute('name');
		
		if ($this->get_attribute('multiple')){
			$type = 'checkbox';
			$name .= '[]';
		} else {
			$type = 'radio';
		}
		
		$this->set_attribute('name', $name);
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
		// some attributes should not go into the element, like
		// description
		// required is a special case as well
		// handle them first
		
		$description = $this->get_attribute('description');
		unset($attributes['description']);
		
		// required cannot be used with multiple checkboxes
		$required = $this->get_attribute('required');
		unset($attributes['required']);
		
		$placeholder = $this->get_attribute('placeholder');
		unset($attributes['placeholder']);
		
		$attributes_string = '';
		
		foreach($attributes AS $key => $attribute){
			$attributes_string .= ' ' . $key . '="' . (string) $attribute . '"';
		}

		$elements = array();
		$element = '';
		
		
		foreach($this->get_attribute('options') AS $key => $option){
		// check the first option ($key === 0) if field is required and radio
		$checked = ($required && 
					!$this->get_attribute('multiple') &&
					$key === 0) ? 'checked' : '';
		
			$element = sprintf(
						'<label class="%1$s inline">
							<input type="%1$s" value="%2$d" name="%3$s" %4$s %5$s> %6$s
						</label>', 
							$this->get_attribute('type'),
							$option['id'],
							$this->get_attribute('name'),
							($required && !$this->get_attribute('multiple'))
								? 'required' : '',
							$checked,
							$option['text']
					  );
			
			$elements[] = $element;
		}
		
		$description_decorator = '';
		if ($description){
			$description_decorator = sprintf(
										'<span class="help-block">%1$s</span>',
										$description
									);
		}
		
		$decorator = sprintf(
						'<div class="control-group">
							<label class="control-label">%3$s</label>
							<div class="controls">
							%1$s %2$s
							</div>
						</div>', 
						implode('', $elements),
						$description_decorator,
						$placeholder
					);
		
		return $decorator;
	}
}

?>
