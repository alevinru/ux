<?php
    
    $transformator = new XSLTProcessor(); 
    $transformator -> registerPHPFunctions();
    
    $transformation = new DOMDocument();
    
    $transformator->importStylesheet($transformation);   
 
    $information = new DOMDocument();
    
    
    try {
        
        while (1) {
            $result = $transformator->transformToDoc($information);
            $information -> loadXML($result);
            
            $command = $information -> documentElement -> nodeName ();
            
            switch ($command) {
                case 'output':
                    // print $i
                case 'save':
            }
        }
    } catch (Exception $e) {
        @print $result;
    }

?>

<xsl:transform version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns="http://unact.net/xml" 
  xmlns:ux="http://unact.net/xml"
  xmlns:php="http://php.net/xsl"
  xmlns:e="http://exslt.org/common"
  extension-element-prefixes="e"
  exclude-result-prefixes="e php"
>

    <xsl:temlpate match="/">
        <ux:context/>
    </xsl:temlpate>


</xsl:transform>