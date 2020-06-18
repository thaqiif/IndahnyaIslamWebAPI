<?php

require_once("simplehtmldom/simple_html_dom.php");
header("Content-Type: application/json");

## Check the page link exist or not
if(!isset($_GET["url"])) die("{\"ok\": false, \"reason\": \"No url specified.\"}");
$url = urldecode($_GET["url"]);

//$url = $argv[1];

## Get the html
$html = file_get_html($url);

## Going specific to the post
$content = str_get_html($html->find("main#main article", 0)->innertext);

## Focus on header now (title, category and date)
$header = str_get_html($content->find("header.entry-header", 0));
$postTitle = $header->find("h1.entry-title", 0)->plaintext;
$category = $header->find("div.entry-meta div.byline span.cat-links a[rel=category tag]", 0)->innertext;
$posted_on = $header->find("div.entry-meta div.posted-on a[rel=bookmark] time", 0)->innertext;

## Focus on the content now!
/*$entry = $content->find("div.entry-content", 0)->innertext;
$entry_json = json_encode(html_to_obj($entry)); ## We use the json to send html content entry
*/
$entry_html = $content->find("div.entry-content", 0)->innertext;

$entry_data = [];
$entry = $content->find("div.entry-content > p");
foreach($entry as $ent){
    ## Check either it contain image
    if(str_get_html($ent->innertext)->find("img", 0)){
        $entry_data[] = ["img" => str_get_html($ent->innertext)->find("img", 0)->getAttribute("src")];
    }else{
        $entry_data[] = ["p" => $ent->innertext];
    }
}

echo json_encode([
    "title" => $postTitle,
    "category" => $category,
    "posted_on" => $posted_on,
    "content" => $entry_data,
    //"html" => $entry_html
]);


/**
 * Function html to json
 * Credits link: https://stackoverflow.com/questions/23062537/how-to-convert-html-to-json-using-php
 */
function html_to_obj($html) {
    $dom = new DOMDocument();
    $dom->loadHTML($html);
    return element_to_obj($dom->documentElement);
}

function element_to_obj($element) {
    $obj = array( "tag" => $element->tagName );
    foreach ($element->attributes as $attribute) {
        $obj[$attribute->name] = $attribute->value;
    }
    foreach ($element->childNodes as $subElement) {
        if ($subElement->nodeType == XML_TEXT_NODE) {
            $obj["html"] = $subElement->wholeText;
        }
        else {
            $obj["children"][] = element_to_obj($subElement);
        }
    }
    return $obj;
}
?>