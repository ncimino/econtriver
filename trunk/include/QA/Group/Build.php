<?php 
class QA_Group_Build {
	const A_CREATE = 'create_grp';
	const A_DELETE = 'del_grp';
	const A_EDIT = 'edit_grp';
	const A_GET = 'get_grp';
	const A_PERM_DELETE = 'perm_del_grp';
	const A_RESTORE = 'restore_grp';

	const C_AXN = 'QA_Group_AJAX';
	const C_GRP = 'group';
	
	const I_CREATE = 'add_grp_text';
	const I_EDIT = 'group_text';

	const N_GRP_ID = 'grp_id';
	const N_CREATE = 'new_grp_name';
	const N_NAME = 'grp_name';
	
	function activeTable($parentElement,$activeGroups,$db) {
		if ($db->num($activeGroups)>0) {
			$divGroups = new HTML_Div($parentElement,'',QA_Module::C_FRAME);
			self::table($divGroups,'Active Groups:',$activeGroups,'',$db);
		}
	}

	function inactiveTable($parentElement,$inactiveGroups,$db) {
		if ($db->num($inactiveGroups)>0) {
			$divGroups = new HTML_Div($parentElement,'',QA_Module::C_FRAME);
			self::table($divGroups,'Inactive Groups:',$inactiveGroups,'',$db,false);
		}
	}

	function table($parentElement,$title,$queryResult,$tableName,$db,$editable=true) {
		new HTML_Heading($parentElement,5,$title);
		$cols = ($editable) ? 3 : 3;
		$table = new Table($parentElement,$db->num($queryResult),$cols,$tableName);
		$i = 0;
		while ($group = $db->fetch($queryResult)) {
			$inputName = new HTML_InputText($table->cells[$i][0],self::N_NAME,$group['name'],'',self::C_GRP);
			$inputId = new HTML_InputHidden($table->cells[$i][0],self::N_GRP_ID,$group['group_id']);
			if ($editable) {
				$editAxn = new Axn($table->cells[$i][1], 'Edit', self::A_EDIT, $i, self::C_AXN);
				$deleteAxn = new Axn($table->cells[$i][2], 'Disable', self::A_DELETE, $i, self::C_AXN);
				$editAxn->uses(array($inputName,$inputId));
				$deleteAxn->uses(array($inputId));
			} else {
				$restoreAxn = new Axn($table->cells[$i][1], 'Join', self::A_RESTORE, $i, self::C_AXN);
				$permDeleteAxn = new Axn($table->cells[$i][2], 'Leave', self::A_PERM_DELETE, $i, self::C_AXN);
				$permDeleteAxn->verifyDelete($group['name']);
				$restoreAxn->uses(array($inputId));
				$permDeleteAxn->uses(array($inputId));
				$inputName->setAttribute('disabled',"disabled");
			}
			$i++;
		}
	}

	function newForm($parentElement,$name) {
		$divAddGroup = new HTML_Div($parentElement, '', QA_Module::C_FRAME);
		new HTML_Heading($divAddGroup,5,'Add Group:');
		$inputAddGroup = new HTML_InputText($divAddGroup,self::N_CREATE,$name,self::I_CREATE,self::C_GRP);
		$createAxn = new Axn($divAddGroup, 'Add Group', self::A_CREATE, '0', self::C_AXN);
		$createAxn->uses(array($inputAddGroup));
	}
}
?>