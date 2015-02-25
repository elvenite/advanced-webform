# Advanced Webform for Podio
This is an extension library to the Podio PHP library, it generates a form based on a Podio App ID and optionally an Item ID.

Support for version 4

Known issues:
 - Submitting empty category field with values before, will not impact in the field

[x] Text
[x] Category

# Date
PodioBadRequestError Start and end must both be with time or both must be without time
Sätt datum och tid och sätt sen att tid inte ska visas, dessa fel ska visas i formulärets felmeddelande
Ta bort embed och category fungerar inte. De ändras inte i Podio.

# Installation
## Composer Install

Install composer in your project:

    curl -s https://getcomposer.org/installer | php

Create a `composer.json` file in your project root:

    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/elvenite/advanced-webform"
        }
    ],
    "require": {
        "elvenite/advanced-webform": "master"
    }

Install via composer:

    php composer.phar install

Add this line to your application's `index.php` file:

    <?php
    require 'vendor/autoload.php';

Advanced Webform requires the [Podio PHP library](https://github.com/podio/podio-php). Default CSS uses Bootstrap 2.3.2 but you can change the decorators to fit your own CSS framework.

# Initialize the form
To load the form elements from the desired Podio App, initialize the class:

    $form = new \AdvancedWebform\AdvancedWebform(array(
        'app_id'		=> APP_ID, // an int value with your desired Podio App
		// 'app'		=> $app, // Optional, If you already loaded the app from PodioApp::get you can reference it like this
        // 'item_id'	=> $item_id, // Optional, to have the form update an existing item, include the item id
		// 'item'		=> $item, // Like the $app attribute, the result from a PodioItem::get($item_id) 
    ));

## Other attributes

* lock_default (true|false)
* method ("POST"|"GET")
* action (url)
* submit_value (The text on the submit button, defaults to "Submit")

## Set element attributes in description
Each Podio app field has a description, you can use the description to easily add different attributes to single elements.

Example: "[hidden] This field will be hidden in the Advanced Webform"

More attributes in the description:

* hidden (no value, hides the field)
* locked (no value, locks the field, but still visible and any value will be submitted to Podio)
* required (true|false, override default setting)
* value (example: [value=This will be the predefined value in the element]
* maxsize (int, maximum allowed characters in the element
* practically any html attribute, autofocus etc

### Special attributes
The Relation field will show the first 30 items of the first referenced app as a default.
To filter the items, set a view attribute to the description. Example: [view=12345] where 12345 is the id of a saved view for the referenced app.
This makes it possible to show a subset of items instead of all like the regular Podio web form does.
To expand the first referenced app, add [expand] in the field description. Now the visitor can create a new item which will be referenced in the parent submittion.

# Save the form
	if ($_POST){
		$form->set_values($_POST, $_FILES);
		if (!$form->save()){
			$error_message = $form->get_error();
		}
	}

# Display the form
Just echo the the form object:
        echo $form;

# Known issues
* linebreaks are removed from podio, nl2br?

* If app element is locked, don't display Files placeholder

* If app element is locked, better display of values

# TODOs?
* remove placeholder in subcontact fields
* should an app reference field be an autocomplete or extended?
* Show progress-slider value to the right
* date and timepicker?
* Add calc field
* Better documentation inside code, PHPDoc style
* Describe item fields vs app fields
* Explain field name prefix
* Unit tests
* Support for Podio PHP Library version 4
* Documentation section gh-pages for more extended documentation
    * decorators
    * how to do validation, pre-save checks etc

# Author
Advanced Webform is created and maintained by Carl-Fredrik Herö. Carl-Fredrik
is a Web Architect at [Elvenite](http://elvenite.com/) where he help
organizations to setup and optimize their Podio work flow. Elvenite is a Podio
Preferred Partner located in Sweden.