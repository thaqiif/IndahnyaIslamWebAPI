<?php

require_once("simplehtmldom/simple_html_dom.php");
header("Content-Type: application/json");

## We are getting what category we would like to fetch
if(!isset($_GET["c"])) die("{\"ok\": false, \"reason\": \"No category specified.\"}");
$category = $_GET["c"];

## We also getting what page? If not exist, we consider it one
(!isset($_GET["pg"])) ? $page = null : $page = $_GET["pg"];

//$category = $argv[1];
//$page = isset($argv[2]) ? $argv[2] : null;

## List of category that we accept
$categories = ["semasa", "dunia islam", "hids", "tokoh", "tarbiah", "kolumnis", "kolumnis/kalam-tokoh", "kolumnis/soal-jawab-agama", "kolumnis/perkongsian-siyasah", "infografik"];

## If the category we receive is not exist,
if(!in_array($category, $categories)) die("{\"ok\": false, \"reason\": \"Category not found.\"}");

$category = str_replace([" ", "%20"], "-", $category);
## If all tests pass, let us proceed!
## Get the web HTML
$html = file_get_html("https://www.indahnyaislam.my/category/$category".($page != null ? "/page/$page" : ""));

## Variable to store all posts
$posts = [];

## Straight forward, go to the main content
$mainContent = str_get_html($html->find("main[role=main]", 0)->innertext);

## Get the category title first
$categoryTitle = str_replace("Category: ", "", $mainContent->find("header.page-header h1.page-title", 0)->innertext);

## Get all the articles
$articles = $mainContent->find("article");

## We loop it, process each article one-by-one
foreach($articles as $article){
    ## Get article html
    $singleArticle = str_get_html($article->innertext);

    ## Extract the title and link
    $articleTitle = $singleArticle->find("header.entry-header > h1.entry-title > a", 0)->plaintext;
    $articleLink = $singleArticle->find("header.entry-header > h1.entry-title > a", 0)->getAttribute("href");
    $articleDate = $singleArticle->find("header.entry-header > div.entry-meta > div.posted-on > a > time.entry-date", 0)->plaintext;
    
    ## Extract the thumbnail image
    $articleThumbnail = $singleArticle->find("div.featured_image > img", 0)->getAttribute("src");

    ## We process other thumbnail dimension also
    $otherDimImage = $singleArticle->find("div.featured_image > img", 0)->getAttribute("srcset");
    $thumbnailImagesArray = [];

    # The value now looks like this, (image_link dim, image_link dim)
    ## So we separate those images by <comma><space>
    $images = explode(", ", $otherDimImage);
    ## Now, we process one by one image
    foreach($images as $img){
        ## We separate them by space first
        $separate = explode(" ", $img);
        ## We need to careful here. Probably the image link contain spaces. So, before we extract the dimension by space,
        ## We need to get the dim by very last space
        ## We use looping in this case
        $imglink = ""; $dim = "";
        for($i=0; $i < count($separate); $i++){
            if($i+1 == count($separate)){   ## Last index will be dim
                $dim .= $separate[$i]; continue;
            }
            $imglink .= $separate[$i];
        }

        ## We store it in array of thumbnails
        array_push($thumbnailImagesArray, ["dim" => $dim, "imglink" => $imglink]);
    }


    ## Now, the summary of the post
    if(isset($singleArticle->find("div.excerpt p", 0)->plaintext))
        $summary = $singleArticle->find("div.excerpt p", 0)->plaintext;

    ## We push the article data into array
    array_push($posts, [
        "title" => $articleTitle,
        "date" => $articleDate,
        "link" => $articleLink,
        "thumbnail" => $articleThumbnail,
        "other_thumbnail" => $thumbnailImagesArray,
        "summary" => isset($summary) ? $summary : ""
    ]);
}

## We setup the pagination
## We return only previous page, current page, next page, and the last page.
$paginationArray = [];
$previouspage = null; $currentpage = null; $nextpage = null; $lastpage = null;

$currentpage = $page == null ? 1 : intval($page);   ## Current

$pagination = $mainContent->find("nav ul.pagination li");
$lastpage = $pagination[count($pagination) - 1]->innertext;
if($lastpage != ""){
    $lastpage = str_replace("https://www.indahnyaislam.my/category/$category/page/", "", str_get_html($lastpage)->find("a", 0)->getAttribute("href"));
    $lastpage = intval(str_replace("/", "", $lastpage));
}else $lastpage = $currentpage;

if($currentpage != null && $currentpage > 1 && $currentpage <= $lastpage){
    $previouspage = $currentpage - 1;
}

if($currentpage !== null && $currentpage > 0 && $currentpage < $lastpage){
    $nextpage = $currentpage + 1;
}

## We encode it to json, and output
echo json_encode([
    "category" => $categoryTitle,
    "content" => $posts,
    "pagination" => [
        "prev" => $previouspage,
        "current" => $currentpage,
        "next" => $nextpage,
        "last" => $lastpage
    ]
]);

?>