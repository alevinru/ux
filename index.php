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
        $count=0;
        while ($count++<10) {
            $result = $transformator->transformToXML($information);
            $information -> loadXML($result);

            foreach ($information->childNodes as $node)
                if ($node->nodeType == XML_PI_NODE && $node->target =='ux'){
                    $information->removeChild ($node);
                    switch ($node->data) {
                        case 'print':
                            die($information->saveXML());
                    }
            }

        }
        die ("count exceed\n");
    } catch (Exception $e) {
        @print $result;
    }


    function domElementApplyArray($domElement, $array, $appliedName = false) {
        if (count($array)) {
            foreach ($array as $name => $value)
                $domElement->appendChild (
                    $domElement->ownerDocument->createElement($appliedName,$value)
                )->setAttribute('name',$name);
        }
    }

    function userDataXML() {
        $result = new DOMDocument('1.0', 'utf-8');
        $root = $result->createElement ('request');
        $root->setAttribute ('xmlns', 'http://unact.net/xml/ux');
        $root->setAttribute ('http-method', $_SERVER['REQUEST_METHOD']);
        $result->appendChild ($root);
        
        domElementApplyArray ($root, $_COOKIE, 'cookie');
        domElementApplyArray ($root, $_GET, 'GET');
        
        if (isset($_SERVER['CONTENT_TYPE'])) {
            $root->setAttribute ('content-type', $_SERVER['CONTENT_TYPE']);
            
            if ($_SERVER['CONTENT_TYPE']=='application/x-www-form-urlencoded')
                domElementApplyArray ($root, $_POST, 'POST');
            else {        
                $rawData=file_get_contents("php://input");
                if ($rawData)
                    $rawNode=$root->appendChild($result->createElement ('rawpost'));
                    if ($_SERVER['CONTENT_TYPE']=='text/xml') try {
                            $rawXML=new DOMDocument();
                            $rawXML->loadXML($rawData);
                            if (!$rawXML->documentElement->getAttribute('xmlns'))
                                $rawXML->documentElement->setAttribute('xmlns','http://unact.net/xml/unknown');
                            $rawNode->appendChild($result->importNode($rawXML->documentElement,true));
                        } catch (Exception $e) {
                            die($e->getMessage());
                    }
                    else
                        $rawNode->nodeValue=$rawData;
            }
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
        <xsl:processing-instruction name="ux">print</xsl:processing-instruction>
        <xsl:copy-of select="*"/>
    </xsl:template>


</xsl:transform>