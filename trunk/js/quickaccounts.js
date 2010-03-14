function QaGetAccounts(content_id) {
	document.getElementById(content_id).innerHTML = AjaxIt(arguments.callee.name
			+ ".php?content_id=" + content_id);
}

function QaAddAccount(content_id, name_id) {
	document.getElementById(content_id).innerHTML = AjaxIt(arguments.callee.name
			+ ".php"
			+ "?name="
			+ escape(document.getElementById(name_id).value)
			+ "&content_id="
			+ content_id);
	focus(name_id);
	timedHide('qa_messages_div',8000);
}

function QaEditAccount(content_id, name_id, acct_id) {
	document.getElementById(content_id).innerHTML = AjaxIt(arguments.callee.name
			+ ".php"
			+ "?name="
			+ escape(document.getElementById(name_id).value)
			+ "&acct_id="
			+ acct_id + "&content_id=" + content_id);
	focus(name_id);
	timedHide('qa_messages_div',8000);
}

function QaDropAccount(content_id, acct_id) {
	document.getElementById(content_id).innerHTML = AjaxIt(arguments.callee.name
			+ ".php" + "?acct_id=" + acct_id + "&content_id=" + content_id);
	timedHide('qa_messages_div',8000);
}
