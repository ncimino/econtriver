function QaGetAccounts(content_id) {
	document.getElementById(content_id).innerHTML = AjaxIt(arguments.callee.name
			+ ".php?content_id=" + content_id);
}

function QaAddAccount(content_id, name_id) {
	document.getElementById(content_id).innerHTML = AjaxIt(arguments.callee.name
			+ ".php?name="
			+ document.getElementById(name_id).value
			+ "&content_id=" + content_id);
}

function AjaxIt(file) {
	url = "include/ajax/" + file;
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	} else {// code for IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.open("GET", url, false);
	xmlhttp.send(null);
	return xmlhttp.responseText;
}