<?php

/**
 * Advanced Webform for Podio - A form generator for Podio
 *
 * @author      Carl-Fredrik Herö <carl-fredrik.hero@elvenite.se>
 * @copyright   2014 Carl-Fredrik Herö
 * @link        https://github.com/elvenite/advanced-webform
 * @license     https://github.com/elvenite/advanced-webform
 * @version     1.0.0
 * @package     AdvancedWebform
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace AdvancedWebform\Elements;

require realpath( __DIR__ . '/../../utils') . '/helpers.php'; 

/**
 * File Element Field
 * @package AdvancedWebform
 * @author  Carl-Fredrik Herö
 * @since   1.0.0
 */

class File extends Element{
	
	protected $files = array();
	
	public function __construct($app_field, $form, $item_field = null) {
		parent::__construct($app_field, $form, $item_field);
		
		$this->set_attribute('type', 'file');
		$this->set_attribute('multiple', true);
		$this->form->set_enctype(\AdvancedWebform\AdvancedWebform::ENCTYPE_MULTIPART);
                
                $this->set_max_file_size();
		
		/**
		 * TODO
		 * check visibility equals true (config['visible']
		 * add delta field (delta is the sort order)
		 */
	
	}
        
        public function set_max_file_size(){
            // find max file size
            $max = 104857600; // podio allows 100MB
            $upload_max_filesize = \return_bytes(ini_get('upload_max_filesize'));
            $post_max_size = \return_bytes(ini_get('post_max_size'));

            $max_file_size = min(
                    $max,
                    $upload_max_filesize,
                    $post_max_size
            );
            
            $this->set_attribute('MAX_FILE_SIZE', $max_file_size);
        }
	
	public function get_files(){
		return $this->files;
	}
	
	public function set_value($values) {
            // make sure each file is in its own array
            // not an array for all names, tmp_names etc.
            $new_values = array();
   

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
            
            if ($new_values){
                $this->set_attribute('value', $new_values);
            }
	}
        
        public function save(){
            $new_values = $this->get_attribute('value');
            $files = array();
            
            if (is_array($new_values)){
                foreach($new_values AS $value){
                    if ($value['error'] === UPLOAD_ERR_OK){
                        $file = \PodioFile::upload($value['tmp_name'], $value['name']);
                        if ($file instanceof \PodioFile){
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
                                //$message = "No file was uploaded"; 
                                // if no file, continue to next
                                continue 2;
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

                        $this->throw_error($message);
                    }
                }
            }

            if ($files){
                $this->files = $files;
            }
        }
	
	protected function render_element(){
		$attributes = $this->get_attributes();
                unset($attributes['MAX_FILE_SIZE']);
		
		// make sure PHP can understand multiple files
		if ($this->get_attribute('multiple')){
			$name = $this->get_attribute('name');
			$name .= '[]';
			$attributes['name'] = $name;
		}
                
                $max_file_size = $this->get_attribute('MAX_FILE_SIZE');
		
		$element = '<input type="hidden" name="MAX_FILE_SIZE" value="' . $max_file_size . '">';
		
		$element .= '<input';
		
		$element .= $this->attributes_concat($attributes);
		
		$element .= '>';
		
		return $element;
	}
	
	public function render($element = null, $default_field_decorator = 'field'){
		return parent::render($this->render_element());
	}
}