<?php

namespace Sabberworm\CSS\Value;

class CSSString extends PrimitiveValue {

	private $sString;
	private $bQuoted;

	public function __construct($sString, $iLineNo = 0, $bQuoted=true) {
		$this->sString = $sString;
		$this->bQuoted = $bQuoted;
		parent::__construct($iLineNo);
	}

	public function setString($sString) {
		$this->sString = $sString;
	}

	public function getString() {
		return $this->sString;
	}

	public function __toString() {
		return $this->render(new \Sabberworm\CSS\OutputFormat());
	}

	public function render(\Sabberworm\CSS\OutputFormat $oOutputFormat) {
		$sString = addslashes($this->sString);
		$sString = str_replace("\n", '\A', $sString);
		if($this->bQuoted)
			return $oOutputFormat->getStringQuotingType() . $sString . $oOutputFormat->getStringQuotingType();
		else
			return $sString;
	}

}