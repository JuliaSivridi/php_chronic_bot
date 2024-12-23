<?php
class Cchronic {
	public $ctime;
	public $msg_id;
	public $chronic;
	public $comment;
	function __construct($msg_id, $chronic) {
		$this->ctime = time();
		$this->msg_id = $msg_id;
		$this->chronic = $chronic;
	}
}
?>