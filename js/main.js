//<a href="javascript:submitForm(this)">Submit</a>
function submitForm(thisfield) {
	document.getElementById(thisfield).submit();
}

// onclick='hideElement(id)'
function hideElement(id) {
	if (document.getElementById(id))
		document.getElementById(id).style.display = 'none';
}

// onclick='focus(id)'
function focus(id) {
	document.getElementById(id).focus();
}

// <body onload='timedHide('info_messages_div',3500)'>
var lastTimeout;
function timedHide(id, time) {
	clearTimeout(lastTimeout);
	lastTimeout = setTimeout("hide(" + id + ")", time);
	//lastTimeout = setTimeout("hideElement(" + id + ")", time);
}
function hide(id) {
	//new Effect.BlindUp(id);
	$("#"+id).slideUp("slow");
}

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
function enterFocus(event, number) {
	if (event.keyCode == 13) {
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

// AjaxIt('myfile.php','main_div','test=1&foo=3','acct_name');
function AjaxIt(file, content_id, post_data, focus_id) {
	if (post_data) {
		post_data = "content_id=" + content_id + "&" + post_data;
	} else {
		post_data = "content_id=" + content_id;
	}
	url = "include/ajax/" + file + ".php";
	sendPostRequest(url, content_id, post_data, focus_id);
}

// AjaxGetIt('myfile.php?test=2');
function AjaxGetIt(file, focus_id) {
	url = "include/ajax/" + file;
	// focus_id = (focus_id) ? focus_id : false;
	return sendGetRequest(url, focus_id);
}

function sendGetRequest(url, focus_id) {
	var req = createXMLHTTPObject();
	if (!req)
		return;
	req.open("GET", url, false);
	req.send(null);
	if (focus_id)
		focus(focus_id);
	return req.responseText;
}

function sendPostRequest(url, content_id, post_data, focus_id) {
	var req = createXMLHTTPObject();
	if (!req) {
		document.getElementById(content_id).innerHTML = "An error has occured.";
		return;
	}
	req.open("POST", url, true);
	req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	req.onreadystatechange = function() {
		if (req.status != 200 && req.status != 304) {
			document.getElementById(content_id).innerHTML = "An error has occured.";
			return;
		} /*else if (req.readyState == 0) {
			document.getElementById(content_id).innerHTML = "Prepairing to initialize request.";
			return;
		} else if (req.readyState == 1) {
			document.getElementById(content_id).innerHTML = "Initialization of request completed. Prepairing to send request.";
			return;
		} else if (req.readyState == 2) {
			document.getElementById(content_id).innerHTML = "Request was sent. Prepairing to process data.";
			return;
		} else if (req.readyState == 3) {
			document.getElementById(content_id).innerHTML = "Data is being processed.";
			return;
		}*/ else if (req.readyState == 4) {
			document.getElementById(content_id).innerHTML = req.responseText;
			QaHideMsgs();
			if (focus_id)
				focus(focus_id);
			
			$(function() {

				$("#active_grps_div").draggable({ revert: 'invalid' });

				$("input.acct_name").droppable({
					activeClass: 'ui-state-hover',
					hoverClass: 'ui-state-active',
					drop: function(event, ui) {
						$(this).addClass('ui-state-highlight').find('p').html('Dropped!');
					}
				});

			});
			
			return;
		}
	};
	document.getElementById(content_id).innerHTML = "Loading...";
	req.send(post_data);
}

function XMLHttpFactories() {
	return [ function() {
		return new XMLHttpRequest();
	}, function() {
		return new ActiveXObject("Msxml2.XMLHTTP");
	}, function() {
		return new ActiveXObject("Msxml3.XMLHTTP");
	}, function() {
		return new ActiveXObject("Microsoft.XMLHTTP");
	} ];
}

function createXMLHTTPObject() {
	var xmlhttp = false;
	var factories = XMLHttpFactories();
	for ( var i = 0; i < factories.length; i++) {
		try {
			xmlhttp = factories[i]();
		} catch (e) {
			continue;
		}
		break;
	}
	return xmlhttp;
}

