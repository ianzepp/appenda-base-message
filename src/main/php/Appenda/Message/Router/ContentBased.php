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

class Appenda_Message_Router_ContentBased extends Appenda_Message_Router_Delegating
{
	/**
	 * @see Appenda_Message_Processor::processMessage()
	 *
	 * @param SimpleXMLElement $xml
	 * @return SimpleXMLElement
	 */
	public function processMessage (SimpleXMLElement $xml)
	{
		// Lookup the delegate id
		$delegateId = $this->getDelegateId ($this->getNamespaceKey ($xml));
		
		if (!$delegateId)
		{
			$map ["message"] = "Unable to find namespace key in delegate mapping";
			$map ["requestXml"] = $xml->asXML ();
			$map ["this"] = $this;
			throw new Appenda_Exception ($map);
		}
		
		// Load the mapped bean
		$delegate = $this->getDelegateInstance ($delegateId);
		
		if ($delegate instanceof Appenda_Message_Processor === false)
		{
			$map ["message"] = "Invalid delegate class type found";
			$map ["delegate"] = $delegate;
			$map ["expectedClass"] = "Appenda_Message_Processor";
			$map ["requestXml"] = $xml->asXML ();
			$map ["this"] = $this;
			throw new Appenda_Exception ($map);
		}
		
		// Pass it off
		return $delegate->processMessage ($xml);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param SimpleXMLElement $xml
	 * @return string
	 */
	public function getNamespaceKey (SimpleXMLElement $xml)
	{
		$messageNamespace = (string) array_shift ($xml->getNamespaces (false));
		$messageName = (string) $xml->getName ();
		return strtolower ('{' . $messageNamespace . '}' . $messageName);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param string $namespaceKey
	 * @return Appenda_Endpoint
	 */
	public function getDelegateId ($namespaceKey)
	{
		$delegates = $this->getDelegates ();
		$delegateExists = array_key_exists ($namespaceKey, $delegates);
		return $delegateExists ? $delegates [$namespaceKey] : null;
	}
}

