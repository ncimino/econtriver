<?php
class Table {
	public $table;
	public $cells = array();
	public $rows = array();
	private $num_rows;
	private $num_cols;
	function __construct($parentElement,$num_rows,$num_cols,$id=NULL,$class=NULL) {
		$this->num_cols = $num_cols;
		$this->num_rows = $num_rows;
		$this->table = new HTML_Table($parentElement,$id,$class);
		for ($i=0;$i<$num_rows;$i++) {
			$row = new HTML_Tr($this->table);
			$this->rows[$i] = $row;
			if (!empty($class)) { $row->setClass($class.'_row'); }
			if (!empty($id)) { $row->setId($id.'_'.$i); }
			for ($j=0;$j<$num_cols;$j++) {
				$this->cells[$i][$j] = new HTML_Td($row);
				if (!empty($class)) { $this->cells[$i][$j]->setClass($class.'_col'); }
				if (!empty($id)) { $this->cells[$i][$j]->setId($id.'_'.$i.'_'.$j); }
			}
		}
	}

	function makeSingleCellRow($row) {
		if ($row > $this->num_rows) {
			return false;
		} else {
			$this->cells[$row][0]->setAttribute('colspan',$this->num_cols);
			for ($i=1;$i<$this->num_cols;$i++) {
				$this->cells[$row][$i]->remove();
			}
		}
	}

	function removeRowClass($row) {
		$this->rows[$row]->removeAttribute('class');
	}

	function removeRowId($row) {
		$this->rows[$row]->removeAttribute('id');
	}

	function removeRowAttribs($row) {
		$this->removeRowClass($row);
		$this->removeRowId($row);
	}

	function removeCellClass($row,$col) {
		$this->cells[$row][$col]->removeAttribute('class');
	}

	function removeCellId($row,$col) {
		$this->cells[$row][$col]->removeAttribute('id');
	}

	function removeCellAttribs($row,$col) {
		$this->removeCellClass($row,$col);
		$this->removeCellId($row,$col);
	}
}
?>