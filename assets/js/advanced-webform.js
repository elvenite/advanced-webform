
// Warns the user if the file is too large
// inspiration from html5rocks
//http://www.html5rocks.com/en/tutorials/file/dndfiles/
function handleFileSelect(evt) {
	var el = evt.target,
	files = el.files, // FileList object
	max = document.getElementsByName('MAX_FILE_SIZE');
	if (max){
		max = parseInt(max[0].value);
	}
	
	// remove all old error messages
	var errors = document.querySelectorAll('.awp-file-size-error');
	if (errors){
		for (var i = 0, err; err = errors[i]; i++) {
			el.parentNode.removeChild(err);
		}
	}

	// files is a FileList of File objects. List some properties.
	for (var i = 0, f; f = files[i]; i++) {
		if (f.size >= max){
			var	alert = document.createElement('p');
			alert.setAttribute('class', 'alert alert-danger awp-file-size-error');
			alert.appendChild(document.createTextNode(escape(f.name) + ' is too large. (' + (f.size / 1048576).toFixed(2) + 'MB, max allowed is ' + (max / 1048576).toFixed(2) + 'MB.)'));
			el.parentNode.insertBefore(alert, el.nextSibling);
		}
	}
}

if (window.File && window.FileList){
	elements = document.querySelectorAll('[type="file"]');
	if (elements){
		for (var i = 0, el; el = elements[i]; i++) {
			el.addEventListener('change', handleFileSelect, false);
		}
	}
}