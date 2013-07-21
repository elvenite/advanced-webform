<?php

class PodioAdvancedFormFileElement extends PodioAdvancedFormElement{
	
	protected $files;
	
	public function __construct($app_field, $form, $item_field = null) {
		parent::__construct($app_field, $form, $item_field);
		
		$this->set_attribute('type', 'file');
		$this->set_attribute('multiple', true);
		$this->form->set_enctype(PodioAdvancedForm::ENCTYPE_MULTIPART);
		
		/**
		 * TODO
		 * check visibility equals true (config['visible']
		 * add delta field (delta is the sort order)
		 */
	
	}
	
	public function get_files(){
		return $this->files;
	}
	
	public function set_value($values) {
		// make sure each file is in its own array
		// not an array for all names, tmp_names etc.
		$new_values = array();
		$files = array();

		if (is_array($values['name'])){
			$count = count($values['name']);
			for($i=0;$i<$count;$i++){
				$new_values[] = array(
					'name' => $values['name'][$i],
					'tmp_name' => $values['tmp_name'][$i],
					'error' => $values['error'][$i],
				);
			}
		} else {
			$new_values = $values;
		}
		
		foreach($new_values AS $value){
			if ($value['error'] === 0){
				$file = PodioFile::upload($value['tmp_name'], $value['name']);
				if ($file instanceof PodioFile){
					$files[] = $file;
				}
			}
		}
		
		if ($files){
			$this->files = $files;
		}
	}
	
	protected function render_element(){
		$attributes = $this->get_attributes();
		
		// make sure PHP can understand multiple files
		if ($this->get_attribute('multiple')){
			$name = $this->get_attribute('name');
			$name .= '[]';
			$attributes['name'] = $name;
		}
		
		$element = '<input type="hidden" name="MAX_FILE_SIZE" value="102400">';
		
		$element .= '<input';
		
		$element .= $this->attributes_concat($attributes);
		
		$element .= '>';
		
		return $element;
	}
	
	public function render(){
		return parent::render($this->render_element());
	}
}

?>
