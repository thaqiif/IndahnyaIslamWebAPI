
# Indahnya Islam Web API

This is Web Scraper API created using PHP for Indahnya Islam website. It will return JSON string as output of their website. Head to code for learning (I wrote comment on each section of code. Might helps you learn)!

I deployed the code on Heroku for public use. Go to Documentation for details.

*Last code written:* 20/08/2019 5:09PM

## What is Indahnya Islam?
> Indahnya Islam is a Today News and informative website related to Islamic on [https://www.indahnyaislam.my/](https://www.indahnyaislam.my/)
> — My POV

## Disclaimer

This project intended for educational purpose only and have no intention to abuse their website. Please use it with respect to their website. 

## Documentation

Below are the API for this project. Use it gently with intention to learn. Don't abuse their website (or spamming). Thank you.

`GET` https://api-ii.herokuapp.com/homepage.php
`Desc` Fetch posts from the home page.
`Return` Array of JSON Object with following attribute (each):
| Name     | Type   | Description              |
|----------|--------|--------------------------|
| category | String | The category of the post |
| content  | Object | Refer content            |

Object name: **content**
| Name            | Type   | Description          	       						 |
|-----------------|--------|-----------------------------------------------------|
| title           | String | The title of post               					 |
| link            | String | The permalink of the post       					 |
| thumbnail       | String | Image thumbnail of the post        				 |
| thumbnail_alt   | String | *Optional.* Image alt (caption) of the post 		 |
| other_thumbnail | Object | *Optional.* Contains two attribute; dim and imglink |
| date            | String | Date in "Month Day, Year"       					 |
| category    	  | Object | Refer category                    					 |
| summary         | String | *Optional.* The summary of the post                 |

Object name: **category**
| Name  | Type   | Description                   |
|-------|--------|-------------------------------|
| title | String | The name of category          |
| link  | String | The permalink to the category |

---

`GET` https://api-ii.herokuapp.com/category.php
`Desc` Fetch posts under category
`Input` As following table
| Name | Type    | Description                                                                                                                                                                                 |
|------|---------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| c    | String  | The name of one of the following categories: `semasa`, `dunia islam`, `hids`, `tokoh`, `tarbiah`, `kolumnis`, `kolumnis/kalam-tokoh`, `kolumnis/soal-jawab-agama`, `kolumnis/perkongsian-siyasah`, `infografik` |
| pg   | Integer | *Optional.* The number of page for the category                                                                                                                                               |
`Return` A JSON Object with following attribute (each):
| Name       | Type   | Description                |
|------------|--------|----------------------------|
| category   | String | The name of the category   |
| content    | Object | Refer content object above |
| pagination | Object | Refer pagination           |

Object name: **pagination**
| Name    | Type    | Description          |
|---------|---------|----------------------|
| prev    | Integer | Previous page number |
| current | Integer | Current page number  |
| next    | Integer | Next page number     |
| last    | Integer | Last page number     |

---
`GET` https://api-ii.herokuapp.com/article.php
`Desc` Return the content of a post
`Input` As following table
| Name    | Type    | Description               |
|---------|---------|---------------------------|
| url     | String  | The permalink of the post |
`Return` A JSON Object
| Name      | Type   | Description                    |
|-----------|--------|--------------------------------|
| title     | String | The title of the post          |
| category  | String | The category of the post       |
| posted_on | String | A date in "Month Day, Year" 	  |
| content   | Object | Array of content_post object   |

Object name: **content_post**
| Name      | Type   | Description                         |
|-----------|--------|-------------------------------------|
| p         | String | *Optional.* A paragraph from the post |
| img       | String | *Optional.* An image from the post    |

## Credits (Library I used)
1. [PHP Simple HTML DOM Parser](https://simplehtmldom.sourceforge.io/) — Simple Web Scraping PHP library

## Legal

This code is in no way affiliated with, authorized, maintained, sponsored or endorsed by Indahnya Islam or any of its affiliates or subsidiaries. This is an independent and unofficial code. Use at your own risk.