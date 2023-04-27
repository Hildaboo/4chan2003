<?php
extract($_POST,EXTR_SKIP);
extract($_GET,EXTR_SKIP);
extract($_COOKIE,EXTR_SKIP);
$upfile_name=isset($_FILES["upfile"]["name"]) ? $_FILES["upfile"]["name"] : "";
$upfile=isset($_FILES["upfile"]["tmp_name"]) ? $_FILES["upfile"]["tmp_name"] : "";

define("LOGFILE", 'img.log');		//logname of imagea
define("TREEFILE", 'tree.log');		//logname of trees
define("IMG_DIR", 'src/');		//where images are held
define("THUMB_DIR", 'thumb/');		//where thumbs are held
define("BANNER", '../rid.php'); //Board banner, point to script if rotating.
define("TITLE", '/g/ - Guro');		//board title
define("HOME",  '../');			//root dir
define("MAX_KB", '1024');			//max file size
define("MAX_W",  '250');			//max width
define("MAX_H",  '250');			//max height
define("PAGE_DEF", '5');			//max images per page
define("LOG_MAX",  '5000');		//max log size
define("ADMIN_PASS", 'admin');	//admin password
define("RE_COL", '789922');               //color of replies
define("PHP_SELF", 'imgboard.php');	//name of main script
define("PHP_SELF2", 'imgboard.htm');	//name of the main htm file
define("PHP_EXT", '.htm');		//extension used by pages 
define("RENZOKU", '5');			//seconds between posts
define("RENZOKU2", '10');		//seconds between (image) posts
define("MAX_RES", '30');		//maximum bumps
define("USE_THUMB", 1);		//Enable thumbnails? 1: ya 0: na
define("PROXY_CHECK", 0);		//check for proxies? (if enabled ONLY japanese users will be able to post)
define("DISP_ID", 0);		//dsiplay ID's 0: na 1: ya
define("BR_CHECK", 15);		//max lines per post (0 = no limit)
define("IDSEED", 'idの種');		//???
define("RESIMG", 0);		//???

$path = realpath("./").'/'.IMG_DIR;
$badstring = array("dummy_string","dummy_string2"); 
$badfile = array("dummy","dummy2");
$badip = array("addr.dummy.com","addr2.dummy.com"); 
$addinfo='';
?>