/*
 * General
 */

function QaHideMsgs() {
	msg_class = AjaxIt('GetVar.php?var=getQaMsgsId');
	timedHide("'"+msg_class+"'",8000);
}

/*
 * Accounts
 */

function QaAccountGet(content_id) {
	document.getElementById(content_id).innerHTML = AjaxIt(arguments.callee.name
			+ ".php?content_id=" + content_id);
}

function QaAccountAdd(content_id, name_id) {
	document.getElementById(content_id).innerHTML = AjaxIt(arguments.callee.name
			+ ".php"
			+ "?name="
			+ escape(document.getElementById(name_id).value)
			+ "&content_id="
			+ content_id);
	focus(name_id);
	QaHideMsgs();
}

function QaAccountEdit(content_id, name_id, acct_id) {
	document.getElementById(content_id).innerHTML = AjaxIt(arguments.callee.name
			+ ".php"
			+ "?name="
			+ escape(document.getElementById(name_id).value)
			+ "&acct_id="
			+ acct_id + "&content_id=" + content_id);
	focus(name_id);
	QaHideMsgs();
}

function QaAccountDrop(content_id, acct_id) {
	document.getElementById(content_id).innerHTML = AjaxIt(arguments.callee.name
			+ ".php" + "?acct_id=" + acct_id + "&content_id=" + content_id);
	QaHideMsgs();
}

/*
 * Groups
 */

function QaGroupGet(content_id) {
	document.getElementById(content_id).innerHTML = AjaxIt(arguments.callee.name
			+ ".php?content_id=" + content_id);
}

function QaGroupAdd(content_id, name_id) {
	document.getElementById(content_id).innerHTML = AjaxIt(arguments.callee.name
			+ ".php"
			+ "?name="
			+ escape(document.getElementById(name_id).value)
			+ "&content_id="
			+ content_id);
	focus(name_id);
	QaHideMsgs();
}

function QaGroupEdit(content_id, name_id, group_id) {
	document.getElementById(content_id).innerHTML = AjaxIt(arguments.callee.name
			+ ".php"
			+ "?name="
			+ escape(document.getElementById(name_id).value)
			+ "&group_id="
			+ group_id + "&content_id=" + content_id);
	focus(name_id);
	QaHideMsgs();
}

function QaGroupDrop(content_id, group_id) {
	document.getElementById(content_id).innerHTML = AjaxIt(arguments.callee.name
			+ ".php" + "?group_id=" + group_id + "&content_id=" + content_id);
	QaHideMsgs();
}

function QaGroupRejoin(content_id, group_id) {
	document.getElementById(content_id).innerHTML = AjaxIt(arguments.callee.name
			+ ".php" + "?group_id=" + group_id + "&content_id=" + content_id);
	QaHideMsgs();
}

/*
 * Shared Accounts
 */

function QaSharedAccountsGet(content_id) {
	document.getElementById(content_id).innerHTML = AjaxIt(arguments.callee.name
			+ ".php?content_id=" + content_id);
}