<?php

use Doubles\Spy;
use Doubles\Spy\CallCounter;

class SpyTest extends PHPUnit_Framework_TestCase {
	
	public function testSpyFromClassIsAnInstanceOfSubjectClass() {

		$spy = Spy::fromClass('SomeClass');
		$this->assertInstanceOf('SomeClass', $spy);
	}
	
	public function testSpyFromInterfaceIsAnInstanceOfSubjectInterface() {

		$spy = Spy::fromInterface('SomeInterface');
		$this->assertInstanceOf('SomeInterface', $spy);
	}
	
	public function testSpyCountsCallsToAnyOfItsMethods() {

		$spy = Spy::fromClass('SomeClass');
		$spy->firstMethod();
		$spy->firstMethod();
		$spy->secondMethod();
		
		$this->assertSame(3, $spy->callCount());
		
		$this->assertSame(2, $spy->spy('firstMethod')->callCount());
		$this->assertSame(1, $spy->spy('secondMethod')->callCount());
	}
	
	public function testSpyTracksMethodCallOrder() {

		$spy = Spy::fromClass('SomeClass');
		$spy->firstMethod();
		$spy->secondMethod();
		$spy->firstMethod();

		$this->assertSame(1, $spy->spy('firstMethod')->callOrder(0));
		$this->assertSame(3, $spy->spy('firstMethod')->callOrder(1));
		
		$this->assertSame(2, $spy->spy('secondMethod')->callOrder(0));
	}
	
	public function testSpyTracksCallOrderAcrossObjects() {
		
		$spy = Spy::fromClass('SomeClass');
		$otherSpy = Spy::fromClass('SomeOtherClass');
		$anotherSpy = Spy::fromClass('SomeOtherClass');
		
		CallCounter::shareNew($spy, $otherSpy, $anotherSpy);
		
		$spy->method();
		$anotherSpy->anotherMethod();
		$otherSpy->otherMethod();
		
		$this->assertSame(1, $spy->spy('method')->sharedCallOrder(0));
		$this->assertSame(3, $otherSpy->spy('otherMethod')->sharedCallOrder(0));
		$this->assertSame(2, $anotherSpy->spy('anotherMethod')->sharedCallOrder(0));
	}
	
	public function testSpyTracksArguments() {
		
		$spy = Spy::fromClass('SomeClass');
		
		$dummyObject = new stdClass;
		$spy->method($dummyObject, 'dummyValue');
		
		$this->assertSame($dummyObject, $spy->spy('method')->arg(0, 0));
		$this->assertSame('dummyValue', $spy->spy('method')->arg(0, 1));
		
		$this->assertEquals(array($dummyObject, 'dummyValue'), $spy->spy('method')->args(0));
	}
	
	public function testOneCallArgThrowsExceptionWhenCalledMoreThanOnce() {
		$this->setExpectedException('\Doubles\Spy\OneCallException');
		
		$spy = Spy::fromClass('SomeClass');
		$spy->method();
		$spy->method();
		
		$spy->spy('method')->oneCallArg(0);
	}
	
	public function testOneCallArgsThrowsExceptionWhenCalledMoreThanOnce() {
		$this->setExpectedException('\Doubles\Spy\OneCallException');
		
		$spy = Spy::fromClass('SomeClass');
		$spy->method();
		$spy->method();
		
		$spy->spy('method')->oneCallArgs();
	}
	
	public function testOneCallOrderThrowsExceptionWhenCalledMoreThanOnce() {
		$this->setExpectedException('\Doubles\Spy\OneCallException');
		
		$spy = Spy::fromClass('SomeClass');
		$spy->method();
		$spy->method();
		
		$spy->spy('method')->oneCallOrder();
	}
	
	public function testOneSharedCallOrderThrowsExceptionWhenCalledMoreThanOnce() {
		$this->setExpectedException('\Doubles\Spy\OneCallException');
		
		$spy = Spy::fromClass('SomeClass');
		$spy->method();
		$spy->method();
		
		$spy->spy('method')->oneSharedCallOrder();
	}

}