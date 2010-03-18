function QaHideMsgs() {
	msg_class = AjaxIt('GetVar.php?var=getQaMsgsId');
	timedHide("'"+msg_class+"'",8000);
}

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
	QaHideMsgs();
}

function QaEditAccount(content_id, name_id, acct_id) {
	document.getElementById(content_id).innerHTML = AjaxIt(arguments.callee.name
			+ ".php"
			+ "?name="
			+ escape(document.getElementById(name_id).value)
			+ "&acct_id="
			+ acct_id + "&content_id=" + content_id);
	focus(name_id);
	QaHideMsgs();
}

function QaDropAccount(content_id, group_id) {
	document.getElementById(content_id).innerHTML = AjaxIt(arguments.callee.name
			+ ".php" + "?group_id=" + group_id + "&content_id=" + content_id);
	QaHideMsgs();
}

function QaGetGroups(content_id) {
	document.getElementById(content_id).innerHTML = AjaxIt(arguments.callee.name
			+ ".php?content_id=" + content_id);
}

function QaAddGroup(content_id, name_id) {
	document.getElementById(content_id).innerHTML = AjaxIt(arguments.callee.name
			+ ".php"
			+ "?name="
			+ escape(document.getElementById(name_id).value)
			+ "&content_id="
			+ content_id);
	focus(name_id);
	QaHideMsgs();
}

function QaEditGroup(content_id, name_id, group_id) {
	document.getElementById(content_id).innerHTML = AjaxIt(arguments.callee.name
			+ ".php"
			+ "?name="
			+ escape(document.getElementById(name_id).value)
			+ "&group_id="
			+ group_id + "&content_id=" + content_id);
	focus(name_id);
	QaHideMsgs();
}

function QaDropGroup(content_id, group_id) {
	document.getElementById(content_id).innerHTML = AjaxIt(arguments.callee.name
			+ ".php" + "?group_id=" + group_id + "&content_id=" + content_id);
	QaHideMsgs();
}
