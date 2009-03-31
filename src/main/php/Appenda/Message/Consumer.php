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

require_once "Appenda/Message/Router/Chain.php";

class Appenda_Message_Consumer extends Appenda_Message_Router_Chain
{
	private $brokerContext;
	private $maximumLifetime = PHP_INT_MAX;
	private $maximumMessages = PHP_INT_MAX;
	
	public function processPendingMessages ()
	{
		// Setup the monitoring
		$loopStarted = time ();
		$loopCount = 0;
		
		while (++$loopCount)
		{
			try
			{
				$this->processMessage ($requestXml = $this->getMessage ());
			}
			catch (Exception $e)
			{
				$map ["message"] = "Caught exception";
				$map ["exception"] = $e;
				$map ["requestXml"] = isset ($requestXml) ? $requestXml : "<not set>";
				$map ["loopCount"] = $loopCount;
				$map ["loopStarted"] = $loopStarted;
				$map ["loopTime"] = time () - $loopStarted;
				$map ["this"] = $this;
				file_put_contents ("php://stderr", new Exception ($map));
				continue;
			}
			
			// Are we out of time?
			if ((time () - $loopStarted) >= $this->getMaximumLifetime ())
			{
				file_put_contents ("php://stderr", "Exiting, reached maximum lifetime");
				break;
			}
			
			// Are we at max message count?
			if ($loopCount >= $this->getMaximumMessages ())
			{
				file_put_contents ("php://stderr", "Exiting, reached maximum message count");
				break;
			}
		}
	}
	
	/**
	 * @return SimpleXMLElement
	 */
	public function getMessage ()
	{
		return simplexml_load_file ($this->getBrokerContext ());
	}
	
	/**
	 * @return string
	 */
	public function getBrokerContext ()
	{
		return $this->brokerContext;
	}
	
	/**
	 * @param string $brokerContext
	 */
	public function setBrokerContext ($brokerContext)
	{
		assert (is_string ($brokerContext));
		$this->brokerContext = $brokerContext;
	}
	
	/**
	 *
	 * @return integer
	 */
	public function getMaximumLifetime ()
	{
		return $this->maximumLifetime;
	}
	
	/**
	 *
	 * @param integer $maximumLifetime
	 * @return
	 */
	public function setMaximumLifetime ($maximumLifetime)
	{
		assert (is_integer ($maximumLifetime) || preg_match ("/^[0-9]+$/", $maximumLifetime));
		$this->maximumLifetime = $maximumLifetime;
	}
	
	/**
	 *
	 * @return integer
	 */
	public function getMaximumMessages ()
	{
		return $this->maximumMessages;
	}
	
	/**
	 *
	 * @param integer $maximumMessages
	 * @return v
	 */
	public function setMaximumMessages ($maximumMessages)
	{
		assert (is_integer ($maximumMessages) || preg_match ("/^[0-9]+$/", $maximumMessages));
		$this->maximumMessages = $maximumMessages;
	}
}