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

namespace AdvancedWebform;

/**
 * Advanced Webform
 * @package AdvancedWebform
 * @author  Carl-Fredrik Herö
 * @since   1.0.0
 */
class AdvancedWebform {
	
    /**
     * @const string
     */
    const VERSION = '1.0.0';

    /**
     *
     * @var PodioApp 
     */
    protected $app;

    /**
     *
     * @var PodioItem
     */

    protected $item;

    /**
     * Error message
     * @var String|false
     */
    protected $error = false;

    /**
     * An array of all AdvancedWebform\Elements\Form objects
     * @var array
     */
    protected $elements;

    /**
     * Is $this a sub form, used to indicate if <form>-tag should be displayed or not
     * @var bool
     */
    protected $is_sub_form = false;

    /**
     * Array of PodioFiles
     * @var array 
     */
    protected $files;

    // used to prefix form fields in sub forms
    // the field "name" in an app reference field "company"
    // would get the name attribute company[name]
    // prefix should not contain the last [] surrounding "name"
    // as the element will take care of that.
    protected $field_name_prefix = '';


    /**
     * Default decorators
     * field and parent_field
     *   1 name attribute
     *   2 label, also defaults as placeholder
     *   3 element, the actual input element
     *   4 description decorator, only if there is a description
     *   5 required decorator
     *   6 optional css classes (e.g. error or similar) (only field)
     * field_description
     *   1 description
     * sub_sub_field - used when a subform has contact sub elements
     * @var array 
     */

    protected $decorators = array(
        'field' => '<div class="control-group %6$s"><label class="control-label" for="%1$s">%2$s%5$s</label><div class="controls">%3$s%4$s</div></div>',
        'field_required' => ' <span class="required">*</span>',
        'field_description' => '<small class="help-block muted">%1$s</small>',
        'parent_field' => '<fieldset class="well"><legend>%2$s</legend>%4$s%3$s</fieldset>',
        'sub_field' => '<div class="control-group %6$s"><label class="control-label" for="%1$s">%2$s%5$s</label><div class="controls">%3$s%4$s</div></div>',
        'sub_parent_field' => '<div class="control-group %6$s"><label class="control-label" for="%1$s">%2$s%5$s<br>%4$s</label><div class="controls">%3$s</div><hr></div>',
        'sub_sub_field' => '<label for="%1$s">%2$s%5$s</label>%3$s%4$s',
    );
	
    /**
     * Method type constants
     */
    const METHOD_GET    = 'get';
    const METHOD_POST   = 'post';

    /**
     * Allowed methods
     * @var array 
     */
    protected $methods = array(
        self::METHOD_GET,
        self::METHOD_POST,
    );

    /**
     * Encoding type constants
     */
    const ENCTYPE_URLENCODED = 'application/x-www-form-urlencoded';
    const ENCTYPE_MULTIPART  = 'multipart/form-data';

    /**
     * form-tag method, GET or POST
     * @var string 
     */
    protected $method;
    
    /**
     *
     * @var string 
     */
    protected $action;
    
    /**
     *
     * @var string 
     */
    protected $enctype;

    /**
     * Default form attributes
     * @var type 
     */
    protected $attributes = array(
        'submit_value' => 'Submit',
        'class' => 'advancedwebform form-horizontal',
        'method' => self::METHOD_POST,
    );

    /**
     * Constructor
     * @param \PodioApp $attributes
     * @throws Error
     */
    public function __construct($attributes = array()) {
        /* Setup app
         * If app attribute is set, use as base, otherwise use app_id and
         * get the app config with \PodioApp::get()
         */
        if(isset($attributes['app']) && $attributes['app'] instanceof \PodioApp){
                $this->set_app($attributes['app']);
        } elseif (isset($attributes['app_id']) && $attributes['app_id']){
                $this->set_app( \PodioApp::get($attributes['app_id']) );
        } else {
                throw new Error('App or app id must be set.');
        }

        // we don't need these anymore
        // if kept, they will only clutter $this->attributes
        unset($attributes['app']);
        unset($attributes['app_id']);

        /* Setup item
         * If item attribute is set, use as base, otherwise use item_id and
         * get item config + data from \PodioItem::get()
         */
        if(isset($attributes['item']) && $attributes['item'] instanceof \PodioItem){
                $this->set_item($attributes['item']);
        } elseif (isset($attributes['item_id']) && $attributes['item_id']){
                $item = \PodioItem::get($attributes['item_id']);
                $this->set_item( $item );
        } else {

                $this->set_item( new \PodioItem(array(
                        'app' => $this->get_app(),
                )));
        }

        // we don't need these anymore
        // if kept, they will only clutter $this->attributes
        unset($attributes['item']);
        unset($attributes['item_id']);

        // set is_sub_form
        // a sub form will not create a form tag
        if (isset($attributes['is_sub_form'])){
                $this->set_is_sub_form($attributes['is_sub_form']);
                unset($attributes['is_sub_form']);
        }

        // add remaining attributes
        if ($attributes){
            $this->add_attributes($attributes);
        }

        // initiate element generation
        $this->set_elements();
    }

    /**
     * Get Podio App object
     * @return \PodioApp
     */
    public function get_app() {
        return $this->app;
    }

    /**
     * Set Podio App object
     * @param PodioApp $app
     */
    public function set_app(\PodioApp $app) {
        $this->app = $app;
    }

    /**
     * Get Podio item object
     * @return PodioItem
     */
    public function get_item() {
        return $this->item;
    }

    /**
     * Set Podio Item object
     * @param PodioItem $item
     */
    public function set_item(\PodioItem $item) {
        $this->item = $item;
    }

    /**
     * Is this form a sub form
     * @return bool
     */
    public function is_sub_form(){
        return $this->is_sub_form;
    }

    /**
     * Set is this form a sub form
     * @param type $sub_form
     */
    public function set_is_sub_form($sub_form){
        $this->is_sub_form = (bool) $sub_form;
    }

    /**
     * Returns an instance of a form element
     * @param string $external_id
     * @return PodioAdvancedFormElement
     */
    public function get_element($external_id){
        if (!array_key_exists($external_id, $this->elements)){
                return null;
        }

        return $this->elements[$external_id];
    }

    /**
     * Iterates through all app fields and created instances of the
     * corresponding element
     */
    protected function set_elements(){
        // get all fields
        foreach($this->get_app()->fields AS $app_field){
            if ($app_field->status != "active"){
                continue;
            }

            // get the item field
            $key = $app_field->external_id;
            $item_field = null;
            if ($this->item->fields){
                    $item_field = $this->item->field($key);
            }

            // TODO is this really working?
            // it should be possible to send attributes to specific elements
            // in the PodioAdvancedForm constructor through the "fields"
            // attribute
            $attributes = $this->get_field_attributes($app_field);
            // initiate the element
            $this->set_element($app_field, $item_field, $attributes);
        }

        // is file uploads allowed?
        // Then add a file input element
        if ($this->get_app()->config['allow_attachments']){
            $app_field = new \PodioAppField(array(
                'field_id' => PHP_INT_MAX,
                'status' => 'active',
                'type' => 'file',
                'external_id' => 'files', // external_id is used as input name
                'config' => array(
                    'label' => 'Files',
                    'required' => false,
                    'description' => '',
                )
            ));

            $this->set_element($app_field);
        }
    }

    /**
     * Initiates an element and adds it to the form
     * @param \PodioAppField $app_field
     * @param \PodioItemField $item_field
     * @param array $attributes
     */
    protected function set_element(\PodioAppField $app_field, \PodioItemField $item_field = null, $attributes = null){
        $element = false;
        $class_name = 'Elements\\' . ucfirst($app_field->type);

        // only create if the class exists
        if (class_exists($class_name)){
                try {
                        $element = new $class_name($app_field, $this, $item_field, $attributes);
                } catch (\Exception $e){
                    // TODO output to log that class does not exist
                    $element = false;
                }
        }

        // add element to elements list
        if ($element){
                $this->elements[$app_field->external_id] = $element;
        }
    }

    /**
     * Get form attribute
     * @param string $key
     * @return null|string|array
     */
    public function get_attribute($key){
        if (!array_key_exists($key, $this->attributes)){
                return null;
        }

        return $this->attributes[$key];
    }

    /**
     * Set form attribute
     * @param string $key
     * @param string|array $value
     */
    public function set_attribute($key, $value){
            $key = (string) $key;
            $this->attributes[$key] = $value;
    }

    /**
     * Get all form attributes
     * @return array $this->attributes
     */
    public function get_attributes(){
        return $this->attributes;
    }

    /**
     * Override all form attributes
     * @param array $attributes
     */
    public function set_attributes(array $attributes){
        $this->clear_attributes();
        $this->add_attributes($attributes);
    }

    /**
     * Add all form attributes
     * @param array $attributes
     */
    public function add_attributes(array $attributes){
        foreach($attributes AS $key => $attribute){
                $this->set_attribute($key, $attribute);
        }
    }

    /**
     * Clears all form attributes
     */
    public function clear_attributes(){
        $this->attributes = array();
    }

    /**
     * Remove a single attribute
     * Returns true if the key existed, otherwise false
     * @param string $key
     * @return boolean
     */
    public function remove_attribute($key){
        if (array_key_exists($key, $this->attributes)) {
            unset($this->attributes[$key]);
            return true;
        }

        return false;
    }
	
    /**
     * Get all form fields attributes
     * This is a way to specify a specific field's attributes during form
     * initiation
     * @param type $app_field
     * @return array|null
     */
    public function get_field_attributes($app_field){
        if (!isset($this->attributes['fields'])){
                return null;
        }

        if (array_key_exists($app_field->external_id, $this->attributes['fields'])){
                return $this->attributes['fields'][$app_field->external_id];
        }

        // TODO shouldn't this block test for field_id instead if app_id
        // attributes to a field would be done using either the external_id
        // or the field_id, not app_id
        // discussed in issue #9
        // https://github.com/elvenite/podio-advanced-form/issues/9
        if (array_key_exists($app_field->app_id, $this->attributes['fields'])){
                return $this->attributes['fields'][$app_field->app_id];
        }

        return null;
    }

    /**
     * Get array of PodioFile objects
     * @return array[PodioFile]
     */
    public function get_files(){
        return $this->files;
    }

    /**
     * Add a PodioFile object
     * @param type $file
     */
    public function add_file(\PodioFile $file){
        if (!is_array($this->files)){
                $this->files = array();
        }

        $this->files[] = $file;
    }

    /**
     * Adds an array of PodioFile objects
     * @param array[PodioFile] $files
     */
    public function add_files(array $files){
        if (is_array($files)){
            foreach($files AS $file){
                    $this->add_file($file);
            }
        }
    }

    /**
     * Set array of \PodioFiles to $this->files
     * @param array $files
     */
    public function set_files(array $files){
        $this->files = $files;
    }

    /**
     * Get form method
     * @return string
     */
    public function get_method(){
        // POST is default
        if (null === ($method = $this->get_attribute('method'))){
                $method = self::METHOD_POST;
        }

        return strtolower($method);
    }
    
    /**
     * Set form method
     * @param string $method
     * @throws PodioAdvancedFormError
     */
    public function set_method($method){
        // Verify method is allowed 
        $method = strtolower($method);
        if (!in_array($method, $this->methods)){
                throw new Error('"' . $method . '" is not a valid form method.');
        }

        $this->set_attribute('method', $method);
    }

    /**
     * Get form action
     * @return string
     */
    public function get_action(){
        $action = $this->get_attribute('action');
        if (null === $action) {
            $action = '';
            $this->set_attribute($action);
        }
        return $action;
    }
    
    /**
     * Set form action
     * action should be a url to the form validation av saving script
     * can be an empty string and if so, the script will use the current url
     * @param string $action
     */
    public function set_action($action){
        $this->set_attribute('action', (string) $action);
    }

    /**
     * Get form encoding type
     * @return string
     */
    public function get_enctype(){
        if (null === ($enctype = $this->get_attribute('enctype'))){
            $entype = self::ENCTYPE_URLENCODED;
            $this->set_attribute('enctype', $entype);
        }

        return $enctype;
    }
    
    /**
     * Set form encoding type
     * @param type $enctype
     */
    public function set_enctype($enctype){
        $this->set_attribute('enctype', $enctype);
    }

    /**
     * Get field name prefix
     * @return string
     */
    public function get_field_name_prefix(){
        return $this->field_name_prefix;
    }

    /**
     * Set field name prefix
     * @param string $prefix
     */
    public function set_field_name_prefix($prefix){
        $this->field_name_prefix = $prefix;
    }

    /**
     * Get all decorators
     * @return array
     */
    public function get_decorators(){
        return $this->decorators;
    }

    /**
     * Set all decorators
     * @param array $decorators
     */
    public function set_decorators($decorators){
            $this->decorators = $decorators;
    }

    /**
     * Get decorator
     * @param string $key
     * @return null|string
     */
    public function get_decorator($key){
        if (!array_key_exists($key, $this->decorators)){
            return null;
        }

        return $this->decorators[$key];
    }

    /**
     * Set decorator
     * @param string $key
     * @param string $value
     */
    public function set_decorator($key, $value){
        $this->decorators[$key] = $value;
    }

    /**
     * Echo the form
     * @return string
     */
    public function __toString() {
        return $this->render();
    }

    /**
     * Render the form
     * @return string
     */
    public function render(){
        $output = array();

        // create the form tag if not a sub form
        if (!$this->is_sub_form()){
            $attributes = $this->get_attributes();
            unset($attributes['submit_value']);
            $head = '<form';
            foreach($attributes AS $key => $value){
                // if true, then attribute minimization is allowed
                if ($value === true){
                    $head .= ' ' . $key;
                } elseif ($value){ // all falsy values won't be added
                    $head .= ' ' . $key . '="' . (string) $value . '"';
                }	
            }
            $head .= '>';

            $output[] = $head;
        }

        foreach($this->elements AS $field){
            try {
                $output[] = $field->render();
            } catch (\Exception $e){
                // TODO stupid error handling, get to work
                echo 'Exception';
                var_dump($field);
            }
        }

        // add submit button and close form tag if not sub form
        // TODO add decorator for the submit button
        if (!$this->is_sub_form()){
            $output[] = '<div class="form-actions">
                    <input type="submit" class="btn btn-primary" value="' . $this->get_attribute('submit_value') . '">
            </div>';

            $output[] = '</form>';
        }

        return implode('', $output);
    }
    /**
     * Set values from form submission. 
     * $data = $_POST
     * $files = $_FILES
     * @param array $data
     * @param array $files
     */
    public function set_values($data, $files = array()){
        foreach($this->elements AS $key => $element){
            if (isset($data[$key])){
                $element->set_value($data[$key]);
                $this->item->add_field($element->get_item_field());
            } elseif (isset($files[$key])){
                $element->set_value($files[$key]);
                // if element is the attachment field, not an image or similar
                // add to the item files attribute
                // otherwise add item field to item
                if ($key == 'files'){
                    if (!empty($files['files']['name'][0])){
                        $this->add_files($element->get_files());
                    }
                } else {
                    $this->item->add_field($element->get_item_field());
                }
            }
        }
    }
	
    /**
     * Get error
     * @return string
     */
    public function get_error(){
        return $this->error;
    }
    
    /**
     * Set error
     * @param string $message
     */
    public function set_error($message){
        $this->error = (string) $message;
    }
	
    /**
     * Save $this->item (PodioItem) to Podio
     * @return false|int $item_id 
     */
    public function save(){
        try {
            $result = $this->item->save();
            // if item is update, result will be an array with revision id
            // + title. We always want this function to result the item_id
            if (is_array($result)){
                $item_id = $this->item->item_id;
            } else {
                $item_id = $result;
            }

            // if $this->item->files is a none empty array
            // attach it to the newly created item.
            // TODO refactor this block to a new method attach files
            if ($item_id && $this->get_files())
            {
                foreach($this->get_files() AS $file){
                    PodioFile::attach($file->file_id, array(
                            'ref_type' => 'item',
                            'ref_id' => $item_id,
                    ));
                }

            }
        } catch (PodioError $e){
                $this->set_error($e->body['error_description']);
        }
        catch (Exception $e){
                $this->set_error($e->getMessage());
        }

        if ($this->error){
                return false;
        }

        return $item_id;
    }
}
