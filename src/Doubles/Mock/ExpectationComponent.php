<?php
/* Copyright (c) 2011, Daniel Howlett. All rights reserved.
 * Released under a new BSD license.
 * https://github.com/dancras/Doubles/blob/master/LICENSE */

namespace Doubles\Mock;

/**
 * 
 */
class ExpectationComponent implements \Doubles\Core\IComponent {
	
	private $expecters = array();
	
	private $unexpectedMethodCallback;
	
	public function setUnexpectedMethodCallback(\Closure $callback) {
		$this->unexpectedMethodCallback = $callback;
	}
	
	public function whenMethodCalled($methodName, array $arguments) {

		foreach ($this->expecters as $component) {
			
			if ($component->isExpecting($methodName)) {
				return;
			}

		}

		$callback = $this->unexpectedMethodCallback;
		$callback($methodName, $arguments);
	}
	
	public function addExpecter(IExpecter $expecter) {
		$this->expecters = $expecter;
	}
	
	public function __construct() {
		
		$this->unexpectedMethodCallback = function () {
		};

	}

}