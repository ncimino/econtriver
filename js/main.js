//<a href="javascript:submitForm(this)">Submit</a>
function submitForm(thisfield) {
	document.getElementById(thisfield).submit();
}

// onclick='hideElement(id)'
function hideElement(id) {
	$("#" + id).slideUp("fast");
}

// onclick='focus(id)'
function focus(id) {
	document.getElementById(id).focus();
}

var lastTimeout;
function timedHide(id, time) {
	clearTimeout(lastTimeout);
	lastTimeout = setTimeout("hideElement(" + id + ")", time);
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
	var req = createXMLHTTPObject();
	if (!req)
		return;
	req.open("GET", url, false);
	req.send(null);
	if (focus_id)
		focus(focus_id);
	return req.responseText;
}

function sendPostRequest(url, content_id, post_data, focus_id, after_load) {
	$.ajax({
		   type: "POST",
		   url: url,
		   data: post_data,
		   beforeSend: function(){
		document.getElementById(content_id).innerHTML = "Loading...";
		},
		   success: function(msg){
			document.getElementById(content_id).innerHTML = msg;
			   QaHideMsgs();
				if (focus_id) focus(focus_id);
				if (after_load) $(after_load);
			   }
		 });
}
