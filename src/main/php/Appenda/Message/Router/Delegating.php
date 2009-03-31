<?php

/**
 * The MIT License
 * 
 * Copyright (c) 2009 Ian Zepp
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
 * @author Ian Zepp
 * @package 
 */

require_once "Appenda/Message/Processor.php";
require_once "Appenda/Message/Router.php";

abstract class Appenda_Message_Router_Delegating implements Appenda_Message_Router
{
	private $delegates;
	private $primaryDelegate;
	
	/**
	 * Enter description here...
	 *
	 * @return array
	 */
	public function getDelegates ()
	{
		return $this->delegates;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return bool
	 */
	public function hasDelegates ()
	{
		return !empty ($this->delegates);
	}
	
	/**
	 * Enter description here...
	 * 
	 * @return Appenda_Message_Processor
	 */
	public function getPrimaryDelegate ()
	{
		return $this->primaryDelegate;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return bool
	 */
	public function hasPrimaryDelegate ()
	{
		return $this->primaryDelegate instanceof Appenda_Endpoint;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param array $delegates
	 */
	public function setDelegates (array $delegates)
	{
		$this->delegates = $delegates;
	}
	
	/**
	 * Enter description here...
	 * 
	 * @param Appenda_Message_Processor $endpoint
	 */
	public function setPrimaryDelegate (Appenda_Message_Processor $primaryDelegate)
	{
		$this->primaryDelegate = $primaryDelegate;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param string $beanName
	 * @return object|null
	 */
	public function getDelegateInstance ($beanName)
	{
		assert (is_string ($beanName));
		$bean = Appenda_Bundle_BeanContainer::getBeanInstance ($beanName);
		return $bean instanceof Appenda_Bundle_Bean ? $bean->getBeanInstance () : null;
	}
}