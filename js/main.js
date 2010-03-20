//<a href="javascript:submitForm(this)">Submit</a>
function submitForm(thisfield) {
	document.getElementById(thisfield).submit();
}

// onclick='hideElement(id)'
function hideElement(id) {
	document.getElementById(id).style.display = 'none';
}

// onclick='focus(id)'
function focus(id) {
	document.getElementById(id).focus();
}

// <body onload='timedHide('info_messages_div',3500)'>
function timedHide(id, time) {
	//alert(id);
	setTimeout("hideElement(" + id + ")", time);
}
// function hide(id) { new Effect.BlindUp(id); }

// onkeypress='return enterSubmit(this,event);'
function enterSubmit(exec, thisevent) {
	var keycode;
	if (window.event)
		keycode = window.event.keyCode;
	else if (thisevent)
		keycode = thisevent.which;
	else
		return true;

	if (keycode == 13) {
		exec;
		return false;
	} else
		return true;
}

// onkeyup="enterFocus(event,2)"
function enterFocus(event,number)
{
    if( event.keyCode == 13 )
    {
        var wow = document.getElementById(number);
        wow.focus();
    	
    }        
}

// onfocus='clearField(this,"Some Value")'
function clearField(obj, initialvalue) {
	if (initialvalue == '')
		obj.value = "";
	else if (obj.value == initialvalue)
		obj.value = "";
}

// onclick='return confirmSubmit("Are you sure?")'
function confirmSubmit(msg) {
	var agree = confirm(msg);
	if (agree)
		return true;
	else
		return false;
}

// AjaxIt(myfile.php?test=1);
function AjaxIt(file) {
	url = "include/ajax/" + file;

	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	} else {// code for IE6, IE5
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	}

	xmlhttp.open("GET", url, false);
	xmlhttp.send(null);
	return xmlhttp.responseText;
}
