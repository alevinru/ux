<?php
    header ('content-type:text/xml');
    set_error_handler(exception_error_handler);
    
    $transformator = new XSLTProcessor(); 
    $transformator -> registerPHPFunctions();
    
    $transformation = new DOMDocument ('1.0', 'utf-8');
    
    $transformation -> load (__FILE__);
    
    $transformator->importStylesheet($transformation);   
 
    $information = userDataXML();
    
    
    try {
        
        while (1) {
            $result = $transformator->transformToXML($information);
            $information -> loadXML($result);
            die ($result);

/*            
            $command = $information -> documentElement -> nodeName ();
            
            switch ($command) {
                case 'output':
                    // print $i
                case 'save':
            }
*/
        }
    } catch (Exception $e) {
        @print $result;
    }


    function domElementApplyArray($domElement, $array, $appliedName = false) {
        if (count($array)) {
/*            
            $e = $appliedName
               ? $domElement->ownerDocument->createElement ($appliedName)
               : $domElement;
            
            if ($appliedName) $domElement->appendChild ($e);
*/            
            foreach ($array as $name => $value)
                $domElement->appendChild ($domElement->ownerDocument->createElement($appliedName,$value))->setAttribute('name',$name);
        }
    }

    function userDataXML() {
        $result = new DOMDocument('1.0', 'utf-8');
        $root = $result->createElement ('userinput');
        $root->setAttribute ('xmlns', 'http://unact.net/xml/ux');
        $root->setAttribute ('method', $_SERVER['REQUEST_METHOD']);
        $root->setAttribute ('content-type', $_SERVER['CONTENT_TYPE']);
        $result->appendChild ($root);
        
        domElementApplyArray ($root, $_COOKIE, 'cookie');
        domElementApplyArray ($root, $_GET, 'GET');
        
        if ($_SERVER['CONTENT_TYPE']=='application/x-www-form-urlencoded')
            domElementApplyArray ($root, $_POST, 'POST');
        else {        
            $rawData=file_get_contents("php://input");
            if ($rawData)
                $rawNode=$root->appendChild($result->createElement ('rawpost'));
                if ($_SERVER['CONTENT_TYPE']=='text/xml') try {
                    $rawXML=new DOMDocument();
                    $rawXML->loadXML($rawData);
                    $rawNode->appendChild($result->importNode($rawXML->documentElement,true));
                } catch (Exception $e) {
                    die($e->getMessage());
                }
                else
                    $rawNode->nodeValue=$rawData;
        }
        
        return $result;
        
    }
    
    function exception_error_handler($errno, $errstr, $errfile, $errline ) {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }


?>

<xsl:transform version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns="http://www.w3.org/1999/XSL/Transform" 
>

    <xsl:template match="/">
        <xsl:copy-of select="."/>
    </xsl:template>


</xsl:transform>