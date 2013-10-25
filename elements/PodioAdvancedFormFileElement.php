<?php

class PodioAdvancedFormFileElement extends PodioAdvancedFormElement{
	
	protected $files = array();
	
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
			if ($value['error'] === UPLOAD_ERR_OK){
				$file = PodioFile::upload($value['tmp_name'], $value['name']);
				if ($file instanceof PodioFile){
					$files[] = $file;
				}
			} else {
				switch ($value['error']) { 
					case UPLOAD_ERR_INI_SIZE: 
						$message = "The uploaded file exceeds the upload_max_filesize directive in php.ini"; 
						break; 
					case UPLOAD_ERR_FORM_SIZE: 
						$message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form"; 
						break; 
					case UPLOAD_ERR_PARTIAL: 
						$message = "The uploaded file was only partially uploaded"; 
						break; 
					case UPLOAD_ERR_NO_FILE: 
						$message = "No file was uploaded"; 
						break; 
					case UPLOAD_ERR_NO_TMP_DIR: 
						$message = "Missing a temporary folder"; 
						break; 
					case UPLOAD_ERR_CANT_WRITE: 
						$message = "Failed to write file to disk"; 
						break; 
					case UPLOAD_ERR_EXTENSION: 
						$message = "File upload stopped by extension"; 
						break; 

					default: 
						$message = "Unknown upload error"; 
						break; 
				}
				
				throw new Exception($message);
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
		
		$element = '<input type="hidden" name="MAX_FILE_SIZE" value="104857600">';
		
		$element .= '<input';
		
		$element .= $this->attributes_concat($attributes);
		
		$element .= '>';
		
		return $element;
	}
	
	public function render($element = null, $default_field_decorator = 'field'){
		return parent::render($this->render_element());
	}
}