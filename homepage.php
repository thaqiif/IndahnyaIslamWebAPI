<?php

require_once("simplehtmldom/simple_html_dom.php");
header("Content-Type: application/json");

## Get the website
$html = file_get_html('https://www.indahnyaislam.my/');

$posts = [];    ## Variable that store posts

## Content Root
$rootContent = "html body #content_wrapper div div.col-main";
$widgets = $html->find("$rootContent .home_main_widget");

## We going to loop the widget!
## In the main page, we have 4 widgets which are Semasa, Tarbiah, Infografik and Dunia Islam
## We gonna Extract them all.
foreach($widgets as $widget){
    ## Get the items of widget
    $widgetHtml = str_get_html($widget->innertext);
    
    ## Extract the widget title
    if(!isset($widgetHtml->find("div.gum_post_grid_header div h3.widget-title a", 0)->innertext)) continue; ## If not exist, it is probably Infografik
    $widgetTitle = ucfirst(strtolower($widgetHtml->find("div.gum_post_grid_header div h3.widget-title a", 0)->innertext));

    ## Widget Posts Store
    $widgetPosts = [];

    ## Extract postsss!
    $postsBlocks = $widgetHtml->find("div.gum_posts_block_wrapper div.gum_posts_block div");

    ## We gonna loop the posts by div.
    ## It contains large, small and clearfix divs
    foreach($postsBlocks as $postsBlock){
        ## Get the HTML content
        $pblock = str_get_html($postsBlock->outertext);

        ## We will check either it is large post block or small post block
        if(isset($pblock->find("div.large_post", 0)->innertext)){
            $spost = str_get_html($pblock->find("div.large_post div.single_post_block", 0)->innertext);   ## Get the single post
            $postLink = $spost->find("a", 0)->getAttribute("href");
            $postThumb = $spost->find("a > img", 0)->getAttribute("src");
            $postThumbAlt = $spost->find("a > img", 0)->getAttribute("alt");
            $postTitle = $spost->find("a.p_title", 0)->plaintext;

            ## Look for category!
            $postMeta = str_get_html($spost->find("div.gum_post_block_meta > ul", 0)->innertext);
            $categoryTitle = $postMeta->find("li.cat a", 0)->plaintext;
            $categoryLink = $postMeta->find("li.cat a", 0)->getAttribute("href");

            $date = $postMeta->find("li.date", 0)->plaintext;
            $p = [
                "title" => $postTitle,
                "link" => $postLink,
                "thumbnail" => $postThumb,
                "thumbnail_alt" => $postThumbAlt,
                "date" => $date,
                "category" => [
                    "title" => $categoryTitle,
                    "link" => $categoryLink
                ]
            ];

            array_push($widgetPosts, $p);
        }else if(isset($pblock->find("div.small_post_block", 0)->innertext)){
            $sposts = $pblock->find("div.small_post_block div.single_post_block");   ## Get all the single posts
            foreach($sposts as $spost){
                $sp = str_get_html($spost);
                ## Get the image block
                $imgBlock = str_get_html($sp->find("div.small_post_block_img", 0));
                $postLink = $imgBlock->find("a", 0)->getAttribute("href");
                $postThumb = $imgBlock->find("a > img", 0)->getAttribute("src");
                $postThumbAlt = $imgBlock->find("a > img", 0)->getAttribute("alt");

                ## Get the post block
                $postBlock = str_get_html($sp->find("div.small_post_block_copy", 0));
                $postTitle = $postBlock->find("a.post_title", 0)->plaintext;

                ## Look for post meta!
                $postMeta = str_get_html($postBlock->find("div.gum_post_block_meta > ul", 0)->innertext);
                $categoryTitle = $postMeta->find("li.cat a", 0)->plaintext;
                $categoryLink = $postMeta->find("li.cat a", 0)->getAttribute("href");

                $date = $postMeta->find("li.date", 0)->plaintext;
                $p = [
                    "title" => $postTitle,
                    "link" => $postLink,
                    "thumbnail" => $postThumb,
                    "thumbnail_alt" => $postThumbAlt,
                    "date" => $date,
                    "category" => [
                        "title" => $categoryTitle,
                        "link" => $categoryLink
                    ]
                ];
    
                array_push($widgetPosts, $p);

            }
        }
    }

    ## Append the value to the main arrays
    $wpat = [
        "category" => $widgetTitle,
        "content" => $widgetPosts
    ];

    array_push($posts, $wpat);
}

echo json_encode($posts);

?>