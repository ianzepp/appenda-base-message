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

require_once "Appenda/Message/Router/Delegating.php";
require_once "Appenda/Message/Processor.php";

class Appenda_Message_Router_Chain extends Appenda_Message_Router_Delegating
{
	/**
	 * @see Appenda_Message_Processor::processMessage()
	 *
	 * @param SimpleXMLElement $xml
	 * @return SimpleXMLElement
	 */
	public function processMessage (SimpleXMLElement $xml)
	{
		foreach ($this->getDelegates () as $beanName => $beanInstance )
		{
			if (is_null ($beanInstance))
			{
				$beanInstance = $this->getDelegateInstance ($beanName);
			}
			
			if ($beanInstance instanceof Appenda_Message_Processor === false)
			{
				$map ["message"] = "Invalid delegate class";
				$map ["beanName"] = $beanName;
				$map ["beanInstance"] = $beanInstance;
				$map ["expectedClass"] = "Appenda_Message_Processor";
				$map ["requestXml"] = $xml->asXML ();
				throw new Appenda_Exception ($map);
			}
			
			try
			{
				$xml = $beanInstance->processMessage ($xml);
			}
			catch (Exception $e)
			{
				$map ["message"] = "Caught exception in delegate processMessage()";
				$map ["beanName"] = $beanName;
				$map ["beanInstance"] = $beanInstance;
				$map ["requestXml"] = $xml->asXML ();
				$map ["exception"] = $e;
				throw new Appenda_Exception ($map);
			}
		}
		
		// Return the xml
		return $xml;
	}
}

