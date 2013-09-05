<?php

class PodioAdvancedFormQuestionElement extends PodioAdvancedFormCategoryElement{
	
	public function render(){

		// required cannot be used with multiple checkboxes
		$required = $this->get_attribute('required');


		$elements = array();
		$element = '';
		
		
		foreach($this->get_attribute('options') AS $key => $option){
		// check the first option ($key === 0) if field is required and radio
		$checked = (($required && 
					!$this->get_attribute('multiple') &&
					$key === 0 &&
					(!$this->get_value())) ||
					in_array($option['id'], $this->get_value())) ? 'checked' : '';
		
			$element = sprintf(
						'<label class="%1$s">
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
		
		return parent::render(implode('',$elements));
	}
}

?>
