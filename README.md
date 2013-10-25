# About
This is an extension library to the Podio PHP library, it generates a form based on a Podio App ID and optionally an Item ID.

# Installation
Podio Advanced Form requires the [Podio PHP library](https://github.com/podio/podio-php). Default CSS uses Bootstrap 2.3.2 but you can change the decorators to fit your own CSS framework.

Include the Podio PHP library as you normally would, then add:

    require_once '/path/to/PodioAdvancedForm.php';

# Initialize the form
To load the form elements from the desired Podio App, initialize the class:

    $podioform = new PodioAdvancedForm(array(
        'app_id'		=> APP_ID, // an int value with your desired Podio App
		// 'app'		=> $app, // Optional, If you already loaded the app from PodioApp::get you can reference it like this
        // 'item_id'	=> $item_id, // Optional, to have the form update an existing item, include the item id
		// 'item'		=> $item, // Like the $app attribute, the result from a PodioItem::get($item_id) 
    ));

## Other attributes

* lock_default (true|false)
* method ("POST|"GET")
* action (url)
* submit_value (The text on the submit button, defaults to "Submit")

## Set element attributes in description
Each Podio app field has a description, you can use the description to easily add different attributes to single elements.

Example: "[hidden] This field will be hidden in the Podio Advanced Form"

More attributes the description:

* hidden (no value, hides the field)
* locked (no value, locks the field, but still visible and any value will be submitted to Podio)
* required (true|false, override default setting)
* value (example: [value=This will be the predefined value in the element]
* maxsize (int, maximum allowed characters in the element
* practically any html attribute, pattern, autofocus etc

### Special attributes
The App reference field will expand to display all elements of the first referenced app making it possible to insert values in several apps using a single form.
If you rather what to select an existing item, set a view attribute to the description. Example: [view=12345] where 12345 is the id of a saved view for the referenced app.
This makes it possible to show a subset of items instead of all like the regular Podio web form does.

# Save the form
	if ($_POST){
		$podioform->set_values($_POST, $_FILES);
		if (!$podioform->save()){
			$error_message = $podioform->get_error();
		}
	}

# Display the form
Just echo the the form object:
    echo $podioform;

# Known issues
* linebreaks are removed from podio, nl2br?
* If app element is locked, don't display Files placeholder
* If app element is locked, better display of values

Just nu sparas sub_forms på set_value vilket inte är bra, detta ska göras när
PodioAdvancedForm->save() körs. Den funktionen måste alltså iterera alla
subforms och köra save() innan $this->item->save() körs.

Nu har alla render-funktioner i /elements/ extra attribute som $element som inte används.

Hantera fel i file-uppladdningar

# TODOs?
* should an app reference field be an autocomplete or extended?
* Show progress-slider value to the right
* date and timepicker?
* Add calc field
* Better documentation inside code, PHPDoc style
* Documentation section /doc/ for more extended documentation
    * decorators
    * how to do validation, pre-save checks etc

