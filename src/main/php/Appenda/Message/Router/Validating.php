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

require_once "Appenda/Message/Router/PassThrough.php";

class Appenda_Message_Router_Validating extends Appenda_Message_Router_PassThrough
{
	private $schema;
	private $schemaSource;
	
	/**
	 * @see Appenda_Message_Processor::processMessage()
	 *
	 * @param SimpleXMLElement $xml
	 * @return SimpleXMLElement
	 */
	public function processMessage (SimpleXMLElement $xml)
	{
		$doc = new DOMDocument ();
		$doc->loadXML ($xml->asXML ());
		
		if (!$this->getSchemaSource ())
		{
			$this->setSchemaSource (file_get_contents ($this->getSchema (), true));
		}
		
		if (!$doc->schemaValidateSource ($this->getSchemaSource ()))
		{
			$map ["message"] = "XML/XSD validation failed";
			$map ["requestXml"] = $xml->asXML ();
			$map ["errors"] = libxml_get_errors ();
			$map ["this"] = $this;
			libxml_clear_errors ();
			throw new Appenda_Exception ($map);
		}
		
		// If it validated, return the original xml
		return $xml;
	}
	
	/**
	 * @return string
	 */
	public function getSchema ()
	{
		return $this->schema;
	}
	
	/**
	 * @param string $schema
	 */
	public function setSchema ($schema)
	{
		assert (is_string ($schema));
		$this->schema = $schema;
	}
	
	/**
	 * @return string
	 */
	public function getSchemaSource ()
	{
		return $this->schemaSource;
	}
	
	/**
	 * @param string $schemaSource
	 */
	public function setSchemaSource ($schemaSource)
	{
		assert (is_string ($schemaSource));
		$this->schemaSource = $schemaSource;
	}

}

