

var btns = document.querySelectorAll('.field-add-another'), i;

for(i=0;i<btns.length; i++){
  btns[i].addEventListener('click', addAnother, false);
}

function addAnother(e){
  var btn = e.target,
  btn_wrapper = btn.parentNode,
  last_form_group = btn_wrapper.previousSibling,
  form_group_clone = last_form_group.cloneNode(true);

  // find and empty input
  var input = form_group_clone.querySelector('input');
  input.value = '';

  // find and remove "selected" from select, if there is a select in the field
  var select = form_group_clone.querySelector('select');
  if (select){
    var option = select.querySelector('option:checked');
  option.selected = false;
  }

  // increase index counter (phone-number[0][type] => phone-number[1][type]);
  var i = input.name.substring(input.name.indexOf('[')+1, input.name.indexOf(']'));
  i++;
  [input, select].forEach(function(el){
    if (el){
      var prefix = el.name.substring(0, el.name.indexOf('[')+1);
      var suffix = el.name.substring(el.name.indexOf(']'));
      el.name = prefix + i + suffix;
    }
  });

  var remove_btn = form_group_clone.querySelector('.field-remove button');
  remove_btn.addEventListener('click', removeField, false);

  // insert clone into DOM after last form group
  btn_wrapper.parentNode.insertBefore(form_group_clone, btn_wrapper);

  // remove hidden from all "remove buttons"
  unhideRemoveButtons(btn_wrapper.parentNode);
}

function unhideRemoveButtons(parent){
  var hidden_elements = parent.querySelectorAll('.hidden');
  if (hidden_elements.length){
    var i = 0;
    for (i=0;i<hidden_elements.length;i++){
      hidden_elements[i].className = hidden_elements[i].className.replace( /(?:^|\s)hidden(?!\S)/g , '' );
    }
  }
}

var elements = document.querySelectorAll('.form-control'),
i,
name,
map = {};



for(i=0;i<elements.length;i++){
  name = elements[i].name.substring(0, elements[i].name.indexOf('['));
  if (name !== ''){
    if (map[name]){
      map[name]++;
    } else {
      map[name] = 1;
    }
  }
}

console.log(map);

for (var name in map){
  if (map[name]>1){
    var elements = document.querySelectorAll('[name^="'+name+'"]');
    for(i=0;i<elements.length;i++){
      var el = elements[i];
      console.log(el);
      unhideRemoveButtons(el.parentNode.parentNode);
    }
  }
}

// remove field if .field-remove is clicked
var remove_btns = document.querySelectorAll('.field-remove button');
for (i=0;i<remove_btns.length;i++){
  remove_btns[i].addEventListener('click', removeField, false);
}

function removeField(e){
  var btn = e.target,
  field = btn.parentNode.parentNode.parentNode,
  field_parent = field.parentNode;

  btn.removeEventListener('click', removeField);
  field_parent.removeChild(field);

  // check if there is only one fields left,
  // if so, hide the field-remove
  var elements = field_parent.querySelectorAll('.field-remove');

  if (elements.length === 1){
    elements[0].className += ' hidden';
  }
}