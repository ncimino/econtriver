<?php
class TabIndex {
	private $currentIndex = 0;
	
	function __construct($startIndex = 0) {
		$this->currentIndex = $startIndex;
	}
	
	function getIndex() {
		return $this->currentIndex;
	}

	function setIndex($value) {
		return $this->currentIndex = $value;
	}
	
	function incrIndex() {
		return ++$this->currentIndex;
	}
	
	function decrIndex() {
		return --$this->currentIndex;
	}
	
	function applyIndex($parentElement) {
		$parentElement->setAttribute('tabindex',$this->currentIndex);
	}
	
	function add($parentElement) {
		$this->applyIndex($parentElement);
		$this->incrIndex();
	}
}