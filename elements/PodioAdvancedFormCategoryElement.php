<?php

class PodioAdvancedFormCategoryElement extends PodioAdvancedFormElement{
	
	public function __construct($app_field, $form, $item_field = null) {
		parent::__construct($app_field, $form, $item_field);
		
		// set multiple
		$this->set_attribute('multiple', $app_field->config['settings']['multiple']);
		
		$this->set_attribute('options', $app_field->config['settings']['options']);
		
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
		 * check visibility equals true (config['visible']
		 * add delta field (delta is the sort order)
		 */
		
		if ($item_field){
			$this->set_attribute('value', $item_field->api_friendly_values());
		}
	
	}

	public function render($element = null, $default_field_decorator = 'field'){
		// if the method is invoked from PodioAdvancedFormQuestionElement
		// just pass it on to the parent.
		if ($element){
			return parent::render($element);
		}
		
		$elements = array();
		$element = '';
		
		$required = $this->get_attribute('required');
		
		foreach($this->get_attribute('options') AS $key => $option){
		$class = array();
		$class[] = $this->get_attribute('type');
		$class[] = $option['color'] ? 'color-' . $option['color'] : 'color-DCEBD8';
		// check the first option ($key === 0) if field is required and radio
		$checked = (($required && 
					!$this->get_attribute('multiple') &&
					$key === 0 &&
					(!$this->get_value())) ||
					in_array($option['id'], $this->get_value())) ? 'checked' : '';
		
			$element = sprintf(
						'<label class="%7$s inline">
							<input type="%1$s" value="%2$d" name="%3$s" %4$s %5$s> %6$s
						</label>', 
							$this->get_attribute('type'),
							$option['id'],
							$this->get_attribute('name'),
							($required && !$this->get_attribute('multiple'))
								? 'required' : '',
							$checked,
							$option['text'],
							implode(' ',$class)
					  );
			
			$elements[] = $element;
		}
		
		return parent::render(implode('', $elements));
	}
	
	public function get_value(){
		$value = parent::get_attribute('value');
		if (!$value){
			return array();
		}
		
		return $value;
	}
}

?>
