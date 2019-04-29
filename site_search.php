<?php
    $html = file_get_contents('https://dev.ghostshield.com/sitemap');
    //Create a new DOM document
    $dom = new DOMDocument;

    //Parse the HTML. The @ is used to suppress any parsing errors
    //that will be thrown if the $html string isn't valid XHTML.
    @$dom->loadHTML($html);

    //Get all links. You could also use any other tag name here,
    //like 'img' or 'table', to extract other tags.
    $links = $dom->getElementsByTagName('a');

    //Iterate over the extracted links and display their URLs
    $link_arr = array();
    foreach ($links as $link) {
        //Extract and show the "href" attribute.
        //echo $link->nodeValue;
        if ($link->getAttribute('href')) {
            /* Get relative links */
            if (substr($link->getAttribute('href'),0,1) == "/" && substr($link->getAttribute('href'),0,2) != "//") {
                /*Add URL to Array */
                $url = 'https://dev.ghostshield.com'.$link->getAttribute('href');
                
                $title = '';
                $dom = new DOMDocument();

                if($dom->loadHTMLFile($url)) {
                    $list = $dom->getElementsByTagName("title");
                    if ($list->length > 0) {
                        $title = $list->item(0)->textContent;
                    }
                }
                
                $body = "";
                foreach($dom->getElementsByTagName("body")->item(0)->childNodes as $child) {
                    $body .= $dom->saveHTML($child);
                }
                
                $collection = array("url" => $url, 
                             "title" => $title,
                                   "body" => $body);
            }
            /* Get absolute links */
            if(strpos($link->getAttribute('href'), 'ghostshield.com')) {
                /*Add URL to Array */
                $url = 'http:'.$link->getAttribute('href');
                
                $title = '';
                $dom = new DOMDocument();

                if($dom->loadHTMLFile($url)) {
                    $list = $dom->getElementsByTagName("title");
                    if ($list->length > 0) {
                        $title = $list->item(0)->textContent;
                    }
                }
                
                $body = "";
                foreach($dom->getElementsByTagName("body")->item(0)->childNodes as $child) {
                    $body .= $dom->saveHTML($child);
                }
                
                $collection = array("url" => $url, 
                             "title" => $title,
                                   "body" => $body);
            }
            $link_arr[] = $collection;
        }
    }

print_r(($link_arr));

//    foreach ($links as $link){
//         $link_arr = array();
//        //Extract and show the "href" attribute.
//        //echo $link->nodeValue;
//        if ($link->getAttribute('href')) {
//            //if(!strpos($link->getAttribute('href'), "ghostshield.com")) {
//                if (substr($link->getAttribute('href'), 0) == "/") {
////                    array_push($link_arr, 'http://dev.ghostshield.com'.$link->getAttribute('href')); 
//                    echo 'https://dev.ghostshield.com'.$link->getAttribute('href');
//                } 
//            /*}*/ else {
//                    array_push($link_arr, 'https:'.$link->getAttribute('href'));       
//                }
//        }
//        print_r($link_arr);
//    }
?>