<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Test\Form;

use Acme\Form\Form;

/**
 * Test for Form
 *
 * @author k.holy74@gmail.com
 */
class FormTest extends \PHPUnit_Framework_TestCase
{

	public function testConstructor()
	{
		$form = new Form('myForm');
		$this->assertEquals('myForm', $form->getName());
	}

	public function testImportAcceptArray()
	{
		$form = new Form('myForm');
		$form->import(array(
			'foo' => true,
		));
		$this->assertInstanceOf('Acme\Form\Element', $form->foo);
	}

	public function testImportAcceptTraversable()
	{
		$form = new Form('myForm');
		$form->import(new \ArrayIterator(array(
			'foo' => true,
		)));
		$this->assertInstanceOf('Acme\Form\Element', $form->foo);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testImportRaiseExceptionInvalidArgument()
	{
		$form = new Form('myForm');
		$form->import('foo');
	}

	public function testExport()
	{
		$form = new Form('myForm', array(
			'foo' => true,
		));
		$this->assertEquals(array('foo' => true), $form->export());
	}

	public function testExportTraversable()
	{
		$form = new Form('myForm', new \ArrayIterator(array(
			'foo' => true,
		)));
		$this->assertEquals(array('foo' => true), $form->export());
	}

	public function testSetErrorAndGetErrors()
	{
		$form = new Form('myForm', array(
			'foo' => null,
			'bar' => null,
		));
		$form->setError('foo', 'This is Foo error');
		$form->setError('bar', 'This is Bar error');
		$errors = $form->getErrors();
		$this->assertEquals('This is Foo error', $errors['foo']);
		$this->assertEquals('This is Bar error', $errors['bar']);
	}

	public function testIsError()
	{
		$form = new Form('myForm', array(
			'foo' => null,
			'bar' => null,
		));
		$form->setError('foo', 'This is Foo error');
		$this->assertTrue($form->isError('foo'));
		$this->assertFalse($form->isError('bar'));
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testIsErrorRaiseExceptionUndefinedElement()
	{
		$form = new Form('myForm', array(
			'foo' => null,
		));
		$form->isError('bar');
	}

	public function testHasError()
	{
		$form = new Form('myForm', array(
			'foo' => null,
			'bar' => null,
		));
		$this->assertFalse($form->hasError());
		$form->setError('foo', 'This is Foo error');
		$this->assertTrue($form->hasError());
	}

	public function testImportErrors()
	{
		$form = new Form('myForm', array(
			'foo' => null,
			'bar' => null,
		));
		$this->assertFalse($form->isError('foo'));
		$this->assertFalse($form->isError('bar'));

		$form->importErrors(array(
			'foo' => 'This is Foo error',
			'bar' => 'This is Bar error',
		));
		$this->assertTrue($form->isError('foo'));
		$this->assertTrue($form->isError('bar'));
	}

	public function testImportErrorsWithPrefix()
	{
		$form = new Form('myForm', array(
			'test.foo' => null,
			'test.bar' => null,
		));
		$this->assertFalse($form->isError('test.foo'));
		$this->assertFalse($form->isError('test.bar'));

		$form->importErrors(
			array(
				'foo' => 'This is Foo error',
				'bar' => 'This is Bar error',
			),
			'test.'
		);
		$this->assertTrue($form->isError('test.foo'));
		$this->assertTrue($form->isError('test.bar'));
	}

	public function testCreateElementByArrayAccess()
	{
		$form = new Form('myForm');
		$form['foo'] = true;
		$this->assertInstanceOf('Acme\Form\Element', $form['foo']);
	}

	public function testCreateElementByPropertyAccess()
	{
		$form = new Form('myForm');
		$form->foo = true;
		$this->assertInstanceOf('Acme\Form\Element', $form->foo);
	}

	public function testOverrideElementValueByArrayAccess()
	{
		$form = new Form('myForm');
		$form['foo'] = true;
		$this->assertInstanceOf('Acme\Form\Element', $form['foo']);
		$this->assertTrue($form['foo']->value());
		$form['foo'] = false;
		$this->assertFalse($form['foo']->value());
	}

	public function testOverrideElementValueByPropertyAccess()
	{
		$form = new Form('myForm');
		$form->foo = true;
		$this->assertInstanceOf('Acme\Form\Element', $form->foo);
		$this->assertTrue($form->foo->value());
		$form->foo = false;
		$this->assertFalse($form->foo->value());
	}

	public function testOffsetExists()
	{
		$form = new Form('myForm', array(
			'foo' => true,
			'bar' => null,
		));
		$this->assertTrue($form->offsetExists('foo'));
		$this->assertTrue($form->offsetExists('bar')); // Attention!!
		$this->assertFalse($form->offsetExists('baz'));
	}

	public function testIsset()
	{
		$form = new Form('myForm', array(
			'foo' => true,
			'bar' => null,
		));
		$this->assertTrue(isset($form->foo));
		$this->assertTrue(isset($form->bar)); // Attention!!
		$this->assertFalse(isset($form->baz));
	}

	public function testToStringExportArrayOfElementValue()
	{
		$form = new Form('myForm', array(
			'foo' => true,
			'bar' => false,
		));
		$export = (string)$form;
		eval('$form2 = ' . $export . ';');
		$this->assertEquals($form2['foo'], $form->foo->value());
		$this->assertEquals($form2['bar'], $form->bar->value());
	}

	public function testTraversableReturnedElements()
	{
		$form = new Form('myForm', array(
			'foo' => true,
			'bar' => false,
		));
		$this->assertInstanceOf('\Traversable', $form);
		foreach ($form as $element) {
			$this->assertInstanceOf('Acme\Form\Element', $element);
		}
	}

	public function testToArraySortedByAttributeName()
	{
		$form = new Form('myForm', array(
			'charlie' => null,
			'alfa'    => null,
			'delta'   => null,
			'bravo'   => null,
		));
		$this->assertEquals(
			array('alfa', 'bravo', 'charlie', 'delta'),
			array_keys($form->toArray())
		);
	}

	public function testCount()
	{
		$form = new Form('myForm');
		$this->assertInstanceOf('\Countable', $form);
		$this->assertEquals(0, count($form));
		$form->import(array(
			'foo' => true,
			'bar' => false,
		));
		$this->assertEquals(2, count($form));
	}

}
