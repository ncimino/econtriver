//<a href="javascript:submitForm(this)">Submit</a>
function submitForm(thisfield) {
	document.getElementById(thisfield).submit();
}

// onclick='hideElement(id)'
function hideElement(id, speed) {
	if (!speed)	speed = "fast";
	$("#" + id).slideUp(speed);
}

function showElement(id, speed) {
	if (!speed)	speed = "fast";
	$("#" + id).slideDown(speed);
}

// onclick='focus(id)'
function focus(id) {
	if(document.getElementById(id)) document.getElementById(id).focus();
	else return false;
}

var lastTimeout;
function timedHide(id, time) {
	clearTimeout(lastTimeout);
	lastTimeout = setTimeout("hideElement(" + id + ")", time);
}

// onkeypress="enterCall(event,function() {QaTxnAdd('new_txn_');})"
function enterCall(event, call) {
	if (event.keyCode == 13) {
		call();
		return event;
	} else {
		return event;
	}
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
function enterFocus(event, id) {
	return enterCall(event,function(){focus(id);});
	//if (event.keyCode == 13) document.getElementById(id).focus()
	//return event;
}

// onfocus='clearField(this,"Some Value")'
function clearField(id) {
	$('#' + id).one("focus", function() {
		$(this).val("");
	});
}

// onclick='return confirmSubmit("Are you sure?")'
function confirmSubmit(msg) {
	var agree = confirm(msg);
	if (agree)
		return true;
	else
		return false;
}

// AjaxIt('myfile.php','main_div','test=1&foo=3','acct_name',function(){});
function AjaxIt(file, content_id, post_data, focus_id, after_load) {
	if (post_data) {
		post_data = "content_id=" + content_id + "&" + post_data;
	} else {
		post_data = "content_id=" + content_id;
	}
	url = "include/ajax/" + file + ".php";
	sendPostRequest(url, content_id, post_data, focus_id, after_load);
}

// AjaxGetIt('myfile.php?test=2');
function AjaxGetIt(file, focus_id) {
	url = "include/ajax/" + file;
	return sendGetRequest(url, focus_id);
}

function sendGetRequest(url, focus_id) {
	var returnData;
	$.ajax( {
		type : "GET",
		url : url,
		async : false,
		success : function(data) {
			if (focus_id)
				focus(focus_id);
			returnData = data;
		}
	});
	return returnData;
}

function sendPostRequest(url, content_id, post_data, focus_id, after_load) {
	$.ajax( {
		type : "POST",
		url : url,
		data : post_data,
		beforeSend : function() {
			if (document.getElementById(content_id)) document.getElementById(content_id).innerHTML = "<span class=\"ui-icon ui-autocomplete-loading ui-float-left\"></span>&nbsp;Loading...";
		},
		success : function(msg) {
			if (document.getElementById(content_id)) document.getElementById(content_id).innerHTML = msg;
			QaHideMsgs();
			if (focus_id)
				focus(focus_id);
			if (after_load) {
				if (! isArray(after_load)) {
					$(after_load);
				} else {
					for (i=0;i<after_load.length;i++)
					{
						$(after_load[i]);
					}
				}
			}
		}
	});
}

function isArray(obj) {
	return (obj.constructor.toString().indexOf("Array") != -1);
}
