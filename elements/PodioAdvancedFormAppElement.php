<?php

/**
 * Podio Advanced Form - A form generator for Podio
 *
 * @author      Carl-Fredrik Herö <carl-fredrik.hero@elvenite.se>
 * @copyright   2014 Carl-Fredrik Herö
 * @link        https://github.com/elvenite/podio-advanced-form
 * @license     https://github.com/elvenite/podio-advanced-form
 * @version     1.0.0
 * @package     PodioAdvancedForm
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

/**
 * Element for App Reference Field
 * Since Podio Advanced Form can display nested app forms, this class contains
 * a $sub_form property with a PodioAdvancedForm class.
 * @package PodioAdvancedForm
 * @author  Carl-Fredrik Herö
 * @since   1.0.0
 */
class PodioAdvancedFormAppElement extends PodioAdvancedFormElement{
    
    /**
     * @var PodioAdvancedForm 
     */
    protected $sub_form;
	
    /**
     * Constructor
     * @param PodioAppField $app_field
     * @param PodioAdvancedForm $form
     * @param PodioItemField $item_field
     * @param array|null $attributes
     */
    public function __construct($app_field, $form, $item_field = null, $attributes = null) {
        parent::__construct($app_field, $form, $item_field, $attributes);

        // load a new PodioAdvancedForm in the sub_form attribute
        $this->set_attribute('reference_apps', $app_field->config['settings']['referenceable_types']);

        // for now, just get the first app
        $sub_app_id = $app_field->config['settings']['referenceable_types'][0];
        $this->set_attribute('app_id', $sub_app_id);

        // extract sub item id 
        $sub_item_id = null;
        if ($item_field){
                foreach($item_field->values AS $item){
                        $item = $item['value'];
                        if ($item['app']['app_id'] == $sub_app_id){
                                $sub_item_id = $item['item_id'];
                                break;

                        }
                }
        }

        
        // setup view settings
        // TODO shouldn't this only work if there is NO sub item?
        // either you show a sub form or a select box with items from the view
        $view = $this->get_attribute('view');
        if ($view){
            $collection = PodioItem::filter_by_view($sub_app_id, $view);
            // TODO read total, filtered do decide if autocomplete should be used.
            if ($collection){
                $this->set_attribute('items', $collection['items']);
            } else {
                // if no items, then hide the field
                $this->set_attribute('hidden', true);
            }
        }

        $sub_form_attributes = array(
            'app_id' => $sub_app_id,
            'is_sub_form' => true,
            'item_id' => $sub_item_id,
            'parent' => $this,
        );

        $sub_form = new PodioAdvancedForm($sub_form_attributes);

        $this->set_sub_form($sub_form);

        /**
         * TODO
         * check visibility equals true (config['visible']
         * add delta field (delta is the sort order)
         */
    }
	
    /**
     * Get sub form
     * @return PodioAdvancedForm
     */
    public function get_sub_form(){
        return $this->sub_form;
    }

    /**
     * Set sub form
     * @param PodioAdvancedForm|mixed $settings
     */
    public function set_sub_form($settings){
        if ($settings instanceof PodioAdvancedForm){
            $this->sub_form = $settings;
        } else {
            $this->sub_form = new PodioAdvancedForm($settings);
        }

        // just for extra safety
        $this->sub_form->set_is_sub_form(true);

        $this->sub_form->set_field_name_prefix($this->get_attribute('name'));
    }
    /**
     * Set value
     * @param array|int $values
     * @return void
     */
    public function set_value($values) {
        // if $values if an int, it means it's an item_id
        if (is_numeric($values)){
                $sub_form_item_id = (int) $values;
                $this->sub_form->get_item()->item_id = $sub_form_item_id;
        } elseif (is_array($values)) {
                $this->sub_form->set_values($values);
                // TODO, change this since we don't always want to save before
                // the actual save method has been called.
                $sub_form_item_id = $this->sub_form->save();
        } else {
                // no value, the select element is empty
                return;
        }

        $this->item_field->set_value($sub_form_item_id);
    }

    /**
     * Renders the sub form in locked mode
     * @return string
     */
    public function render_locked(){
        return $this->sub_form->render();
    }

    /**
     * Renders a select box with items to choose from
     * Only if attribute items is set
     * @return string
     */
    public function render_select(){
        $attributes = $this->get_attributes();
        $element = '<select';
        $element .= $this->attributes_concat($attributes);
        $element .= '>';

        if(!$this->get_attribute('required')){
                $app_field = $this->get_app_field();
                $label = $app_field->config['label'];
                $element .= '<option value="">' . $label . '</option>';
        }

        $items = $this->get_attribute('items');
        foreach($items AS $item){
                $element .= '<option value="' . $item->item_id . '">' . $item->title . '</option>';
        }

        $element .= '</select>';

        return $element;
    }

    /**
     * Renders a complete sub form
     * @param string $element
     * @param string $default_field_decorator
     * @return string
     */
    public function render($element = null, $default_field_decorator = 'field'){
        if ($this->get_attribute('items')){
                return parent::render($this->render_select());
        }

        return parent::render($this->sub_form->render(), 'parent_field');
    }
}
