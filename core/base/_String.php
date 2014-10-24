<?
class _String {
	var $text;
	public function __call($name, array $params){
		$p=func_get_args();
		$t[]=$this->text;
		foreach($p[1] as $r) array_push($t,$r);
		$this->text=call_user_func_array($name,$t);
		return $this;
	}
	function setLink(&$text) {
		$this->text = &$text;
		return $this;
	}
	function set($text) {
		$this->text = $text;
		return $this;
	}
	public function replace($from, $to) {
		$this->text=str_replace($from, $to, $this->text);
		return $this;
	}
	public function split($separator, $limit = PHP_INT_MAX) {
		$ar=new my_array();
		$ar->set(explode($separator, $this->text, $limit));
		return $ar;
	}
	public function toUpper() {
		$this->text=mb_strtoupper($this->text);
		return $this;
	}
	public function toLower() {
		$this->text=mb_strtolower($this->text);
		return $this;
	}
	public function remove($what){
		$this->text=str_replace($what, '', $this->text);
		return $this;
	}
	public function substr($start, $length=null){
		$this->text=mb_substr($this->text, $start, $length);
		return $this;
	}
	public function length(){
		return mb_strlen($this->text);
	}
	function __toString() {
		return $this->text;
	} 
}