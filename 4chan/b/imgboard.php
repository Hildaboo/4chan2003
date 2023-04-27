<?php
/* Image bulletin board

futaba.php v0.8 lot.051031 (EN)

This script is a modification of gazou.php of Let's PHP! <http://php.s3.to/>. (offline)
Distribution conditions conform to Let's PHP !. Please refresh and redistribute freely.
Please do not ask questions about this script Let's PHP!
The latest version is distributed at <http://www.2chan.net/script/>.
If you have any questions, please contact the preparation board @ Futaba <http://www.2chan.net/junbi/index2.html>.

Installation method:
Change the permissions of the desired directory to 777.
Create the src and thumb directories and set the permissions to 777.
Place futaba.php and call from the browser (necessary files are set automatically).
*/

include("board_config.php"); //Makes shit simpler.

function head(&$dat){
  $dat.='<html><head>
<META HTTP-EQUIV="Content-type" CONTENT="text/html; charset=Shift_JIS">
<!-- meta HTTP-EQUIV="pragma" CONTENT="no-cache" -->
<STYLE TYPE="text/css">
<!--
body,tr,td,th { font-size:12pt }
a:hover { color:#DD0000; }
span { font-size:20pt }
small { font-size:10pt }
-->
</STYLE>
<title>'.TITLE.'</title>
<script language="JavaScript"><!--
function l(e){var P=getCookie("pwdc"),N=getCookie("namec"),i;with(document){for(i=0;i<forms.length;i++){if(forms[i].pwd)with(forms[i]){pwd.value=P;}if(forms[i].name)with(forms[i]){name.value=N;}}}};onload=l;function getCookie(key, tmp1, tmp2, xx1, xx2, xx3) {tmp1 = " " + document.cookie + ";";xx1 = xx2 = 0;len = tmp1.length;	while (xx1 < len) {xx2 = tmp1.indexOf(";", xx1);tmp2 = tmp1.substring(xx1 + 1, xx2);xx3 = tmp2.indexOf("=");if (tmp2.substring(0, xx3) == key) {return(unescape(tmp2.substring(xx3 + 1, xx2 - xx1 - 1)));}xx1 = xx2 + 1;}return("");}
//--></script>
</head>
<body bgcolor="#FFFFEE" text="#800000" link="#0000EE" vlink="#0000EE">
<span style="left: 0px; font-size: 12pt;">[<a href="../b/imgboard.php">b</a> / <a href="../c/imgboard.php">c</a> / <a href="../d/imgboard.php">d</a> / <a href="../g/imgboard.php">g</a> / <a href="../h/imgboard.php">h</a> / <a href="../s/imgboard.php">s</a> / <a href="../w/imgboard.php">w</a> / <a href="../y/imgboard.php">y</a>]</span><span style="position: absolute; right: 0px; font-size: 12pt;">[<a href="/" target="_top">Home</a>]</span>
<p align=center>
<img src="'.BANNER.'" onclick="this.src=this.src;"><br>
<font color="#800000" size=5>
<b><SPAN>'.TITLE.'</SPAN></b></font>
<hr width="90%" size=1>
';
}

function form(&$dat,$resno,$admin=""){
  global $addinfo; $msg=""; $hidden="";
  $maxbyte = MAX_KB * 1024;
  $no=$resno;
  if($resno){
    $msg .= "[<a href=\"".PHP_SELF2."\">Index</a>]\n";
    $msg .= "<table width='100%'><tr><th bgcolor=#e04000>\n";
    $msg .= "<font color=#FFFFFF>Posting mode: Reply</font>\n";
    $msg .= "</th></tr></table>\n";
  }
  if($admin){
    $hidden = "<input type=hidden name=admin value=\"".ADMIN_PASS."\">";
    $msg = "<h4>You can use HTML.</h4>";
  }
  $dat.=$msg.'<center>
<form action="'.PHP_SELF.'" method="POST" enctype="multipart/form-data">
<input type=hidden name=mode value="regist">
'.$hidden.'
<input type=hidden name="MAX_FILE_SIZE" value="'.$maxbyte.'">
';
if($no){$dat.='<input type=hidden name=resto value="'.$no.'">
';}
$dat.='<table cellpadding=1 cellspacing=1>
<tr><td bgcolor=#eeaa88><b>Name</b></td><td><input type=text name=name size="28"></td></tr>
<tr><td bgcolor=#eeaa88><b>E-mail</b></td><td><input type=text name=email size="28"></td></tr>
<tr><td bgcolor=#eeaa88><b>Subject</b></td><td><input type=text name=sub size="35">
<input type=submit value="Submit"></td></tr>
<tr><td bgcolor=#eeaa88><b>Comment</b></td><td><textarea name=com cols="48" rows="4" wrap=soft></textarea></td></tr>
';
if(RESIMG || !$resno){
$dat.='<tr><td bgcolor=#eeaa88><b>File</b></td>
<td><input type=file name=upfile size="35">
[<label><input type=checkbox name=textonly value=on>No file</label>]</td></tr>
';}
$dat.='<tr><td bgcolor=#eeaa88><b>Password</b></td><td><input type=password name=pwd size=8 maxlength=8 value=""><small>(Password used for post deletion)</small></td></tr>
<tr><td colspan=2>
<small>
<LI>Supported file types are:GIF, JPG, PNG
<LI>Maximum file size allowed is '.MAX_KB.' KB.
<LI>Images greater than '.MAX_W.'x'.MAX_H.' pixels will be thumbnailed.
<LI>Please read the <a href="../rules.php">rules</a> befre posting.
<LI>Protect your username, use <a href="../tripcodes.php">tripcodes</a>!
'.$addinfo.'</small></td></tr></table></form></center><hr>';
}

function updatelog($resno=0){
  global $path;$p=0;

  $tree = file(TREEFILE);
  $find = false;
  if($resno){
    $counttree=count($tree);
    for($i = 0;$i<$counttree;$i++){
      list($artno,)=explode(",",rtrim($tree[$i]));
      if($artno==$resno){$st=$i;$find=true;break;} 
    }
    if(!$find) error("Thread doesn't exist.");
  }
  $line = file(LOGFILE);
  $countline=count($line);
  for($i = 0; $i < $countline; $i++){
    list($no,) = explode(",", $line[$i]);
    $lineindex[$no]=$i + 1; 
  }

  $counttree = count($tree);
  for($page=0;$page<$counttree;$page+=PAGE_DEF){
    $dat='';
    head($dat);
    form($dat,$resno);
    if(!$resno){
      $st = $page;
    }
    $dat.='<form action="'.PHP_SELF.'" method=POST>';

  for($i = $st; $i < $st+PAGE_DEF; $i++){
    if(empty($tree[$i])){continue;}
    $treeline = explode(",", rtrim($tree[$i]));
    $disptree = $treeline[0];
    $j=$lineindex[$disptree] - 1; //�Y���L����T����$j�ɃZ�b�g
    if(empty($line[$j])){continue;}   //$j���͈͊O�Ȃ玟�̍s
    list($no,$now,$name,$email,$sub,$com,$url,
         $host,$pwd,$ext,$w,$h,$time,$chk) = explode(",", $line[$j]);
    // URL�ƃ��[���Ƀ����N
    if($email) $name = "<a href=\"mailto:$email\">$name</a>";
    $com = auto_link($com);
    $com = eregi_replace("(^|>)(&gt;[^<]*)", "\\1<font color=".RE_COL.">\\2</font>", $com);
    // �摜�t�@�C����
    $img = $path.$time.$ext;
    $src = IMG_DIR.$time.$ext;
    // <img�^�O�쐬
    $imgsrc = "";
    if($ext && is_file($img)){
      $size = filesize($img);//alt�ɃT�C�Y�\��
      if($w && $h){//�T�C�Y�����鎞
        if(@is_file(THUMB_DIR.$time.'s.jpg')){
          $imgsrc = "<a href=\"".$src."\" target=_blank><img src=".THUMB_DIR.$time.'s.jpg'.
      " border=0 align=left width=$w height=$h hspace=20 alt=\"".$size." B\"></a>";
        }else{
          $imgsrc = "<a href=\"".$src."\" target=_blank><img src=".$src.
      " border=0 align=left width=$w height=$h hspace=20 alt=\"".$size." B\"></a>";
        }
      }else{//����ȊO
        $imgsrc = "<a href=\"".$src."\" target=_blank><img src=".$src.
      " border=0 align=left hspace=20 alt=\"".$size." B\"></a>";
      }
      $dat.="File:<a href=\"$src\" target=_blank>$time$ext</a>-($size B)<br>$imgsrc";
    }
    // ���C���쐬
    $dat.="<input type=checkbox name=\"$no\" value=delete><font color=#cc1105 size=+1><b>$sub</b></font> \n";
    $dat.="<font color=#117743><b>$name</b></font> $now No.$no &nbsp; \n";
    if(!$resno) $dat.="[<a href=".PHP_SELF."?res=$no>Reply</a>]";
    $dat.="\n<blockquote>$com</blockquote>";

     // ���낻�������B
     if($lineindex[$no]-1 >= LOG_MAX*0.95){
      $dat.="<font color=\"#f00000\"><b>LOG FULL</b></font><br>\n";
     }

    //���X�쐬
    if(!$resno){
     $s=count($treeline) - 10;
     if($s<1){$s=1;}
     elseif($s>1){
      $dat.="<font color=\"#707070\">editthislmao".
             ($s - 1)."asdwrt5565445</font><br>\n";
     }
    }else{$s=1;}
    for($k = $s; $k < count($treeline); $k++){
      $disptree = $treeline[$k];
      $j=$lineindex[$disptree] - 1;
      if($line[$j]=="") continue;
      list($no,$now,$name,$email,$sub,$com,$url,
           $host,$pwd,$ext,$w,$h,$time,$chk) = explode(",", $line[$j]);
      // URL�ƃ��[���Ƀ����N
      if($email) $name = "<a href=\"mailto:$email\">$name</a>";
      $com = auto_link($com);
      $com = eregi_replace("(^|>)(&gt;[^<]*)", "\\1<font color=".RE_COL.">\\2</font>", $com);

    // �摜�t�@�C����
    $img = $path.$time.$ext;
    $src = IMG_DIR.$time.$ext;
    // <img�^�O�쐬
    $imgsrc = "";
    if($ext && is_file($img)){
      $size = filesize($img);//alt�ɃT�C�Y�\��
      if($w && $h){//�T�C�Y�����鎞
        if(@is_file(THUMB_DIR.$time.'s.jpg')){
          $imgsrc = "<small>�T���l�C���\��</small><br><a href=\"".$src."\" target=_blank><img src=".THUMB_DIR.$time.'s.jpg'.
      " border=0 align=left width=$w height=$h hspace=20 alt=\"".$size." B\"></a>";
        }else{
          $imgsrc = "<a href=\"".$src."\" target=_blank><img src=".$src.
      " border=0 align=left width=$w height=$h hspace=20 alt=\"".$size." B\"></a>";
        }
      }else{//����ȊO
        $imgsrc = "<a href=\"".$src."\" target=_blank><img src=".$src.
      " border=0 align=left hspace=20 alt=\"".$size." B\"></a>";
      }
      $imgsrc="<br> &nbsp; &nbsp; <a href=\"$src\" target=_blank>$time$ext</a>-($size B) $imgsrc";
    }

      // ���C���쐬
      $dat.="<table border=0><tr><td nowrap align=right valign=top>>></td><td bgcolor=#F0E0D6 nowrap>\n";
      $dat.="<input type=checkbox name=\"$no\" value=delete><font color=#cc1105 size=+1><b>$sub</b></font> \n";
      $dat.="<font color=#117743><b>$name</b></font> $now No.$no &nbsp; \n";
      $dat.="$imgsrc<blockquote>$com</blockquote>";
      $dat.="</td></tr></table>\n";
    }
    $dat.="<br clear=left><hr>\n";
    clearstatcache();//�t�@�C����stat���N���A
    $p++;
    if($resno){break;} //res����tree1�s����
  }
$dat.='<table align=right><tr><td nowrap align=center>
<input type=hidden name=mode value=usrdel>[Delete post][<input type=checkbox name=onlyimgdel value=on>File ONLY]<br>
Password<input type=password name=pwd size=8 maxlength=8 value="">
<input type=submit value="Submit"></form></td></tr></table>';

    if(!$resno){ //res���͕\�����Ȃ�
      $prev = $st - PAGE_DEF;
      $next = $st + PAGE_DEF;
    // ���y�[�W����
      $dat.="<table align=left border=1><tr>";
      if($prev >= 0){
        if($prev==0){
          $dat.="<form action=\"".PHP_SELF2."\" method=get><td>";
        }else{
          $dat.="<form action=\"".$prev/PAGE_DEF.PHP_EXT."\" method=get><td>";
        }
        $dat.="<input type=submit value=\"hjtesadf\">";
        $dat.="</td></form>";
      }else{$dat.="<td>Previous</td>";}

      $dat.="<td>";
      for($i = 0; $i < count($tree) ; $i+=PAGE_DEF){
        if($st==$i){$dat.="[<b>".($i/PAGE_DEF)."</b>] ";}
        else{
          if($i==0){$dat.="[<a href=\"".PHP_SELF2."\">0</a>] ";}
          else{$dat.="[<a href=\"".($i/PAGE_DEF).PHP_EXT."\">".($i/PAGE_DEF)."</a>] ";}
        }
      }
      $dat.="</td>";

      if($p >= PAGE_DEF && count($tree) > $next){
        $dat.="<form action=\"".$next/PAGE_DEF.PHP_EXT."\" method=get><td>";
        $dat.="<input type=submit value=\"hi5oyueopw\">";
        $dat.="</td></form>";
      }else{$dat.="<td>Next</td>";}
        $dat.="</tr></table><br clear=all>\n";
    }
    foot($dat);
    if($resno){echo $dat;break;}
    if($page==0){$logfilename=PHP_SELF2;}
        else{$logfilename=$page/PAGE_DEF.PHP_EXT;}
    $fp = fopen($logfilename, "w");
    set_file_buffer($fp, 0);
    rewind($fp);
    fputs($fp, $dat);
    fclose($fp);
    chmod($logfilename,0666);
  }
  if(!$resno&&is_file(($page/PAGE_DEF+1).PHP_EXT)){unlink(($page/PAGE_DEF+1).PHP_EXT);}
}
/* �t�b�^ */
function foot(&$dat){
  $dat.='
<center>
<small><!-- GazouBBS v3.0 --><!-- �ӂ��Ή�0.8 -->
- <a href="http://php.s3.to" target=_top>GazouBBS</a> + <a href="http://www.2chan.net/" target=_top>futaba</a> + <a href="http://www.4chan.net" target=_top>moot</a> -
</small>
</center>
</body></html>';
}
/* �I�[�g�����N */
function auto_link($proto){
  $proto = ereg_replace("(https?|ftp|news)(://[[:alnum:]\+\$\;\?\.%,!#~*/:@&=_-]+)","<a href=\"\\1\\2\" target=\"_blank\">\\1\\2</a>",$proto);
  return $proto;
}
/* �G���[��� */
function error($mes,$dest=''){
  global $upfile_name,$path;
  if(is_file($dest)) unlink($dest);
  head($dat);
  echo $dat;
  echo "<br><br><hr size=1><br><br>
        <center><font color=red size=5><b>$mes<br><br><a href=".PHP_SELF2.">Go back</a></b></font></center>
        <br><br><hr size=1>";
  die("</body></html>");
}

function  proxy_connect($port) {
  $a="";$b="";
  $fp = @fsockopen ($_SERVER["REMOTE_ADDR"], $port,$a,$b,2);
  if(!$fp){return 0;}else{return 1;}
}
/* �L���������� */
function regist($name,$email,$sub,$com,$url,$pwd,$upfile,$upfile_name,$resto){
  global $path,$badstring,$badfile,$badip,$pwdc,$textonly;
  $dest="";$mes="";

  // ����
  $time = time();
  $tim = $time.substr(microtime(),2,3);

  // �A�b�v���[�h����
  if($upfile&&file_exists($upfile)){
    $dest = $path.$tim.'.tmp';
    move_uploaded_file($upfile, $dest);
    //���ŃG���[�Ȃ火�ɕύX
    //copy($upfile, $dest);
    $upfile_name = CleanStr($upfile_name);
    if(!is_file($dest)) error("L3",$dest);
    $size = getimagesize($dest);
    if(!is_array($size)) error("L4",$dest);
    $chk = md5_of_file($dest);
    foreach($badfile as $value){if(ereg("^$value",$chk)){
      error("L5",$dest); //����摜
    }}
    chmod($dest,0666);
    $W = $size[0];
    $H = $size[1];

    switch ($size[2]) {
      case 1 : $ext=".gif";break;
      case 2 : $ext=".jpg";break;
      case 3 : $ext=".png";break;
      case 4 : $ext=".swf";break;
      case 5 : $ext=".psd";break;
      case 6 : $ext=".bmp";break;
      case 13 : $ext=".swf";break;
      default : $ext=".xxx";error("damn",$dest);
    }

    // �摜�\���k��
    if($W > MAX_W || $H > MAX_H){
      $W2 = MAX_W / $W;
      $H2 = MAX_H / $H;
      ($W2 < $H2) ? $key = $W2 : $key = $H2;
      $W = ceil($W * $key);
      $H = ceil($H * $key);
    }
    $mes = "$upfile_name uploaded!<br><br>";
  }

  foreach($badstring as $value){if(ereg($value,$com)||ereg($value,$sub)||ereg($value,$name)||ereg($value,$email)){
  error("gggggg(str)",$dest);};}
  if($_SERVER["REQUEST_METHOD"] != "POST") error("aaaaaaaaaa(post)",$dest);
  // �t�H�[�����e���`�F�b�N
  if(!$name||ereg("^[ |�@|]*$",$name)) $name="";
  if(!$com||ereg("^[ |�@|\t]*$",$com)) $com="";
  if(!$sub||ereg("^[ |�@|]*$",$sub))   $sub=""; 

  if(!$resto&&!$textonly&&!is_file($dest)) error("Error: No File must be checked",$dest);
  if(!$com&&!is_file($dest)) error("L7",$dest);

  $name=ereg_replace("�Ǘ�","\"�Ǘ�\"",$name);
  $name=ereg_replace("�폜","\"�폜\"",$name);

  if(strlen($com) > 1000) error("Error: Comment too long!",$dest);
  if(strlen($name) > 100) error("Error: Name too long!",$dest);
  if(strlen($email) > 100) error("Error: E-mail too long!",$dest);
  if(strlen($sub) > 100) error("Error: subject too long!",$dest);
  if(strlen($resto) > 10) error("Error: Thread has reached bump limit.",$dest);
  if(strlen($url) > 10) error("�ُ�ł�",$dest);

  //�z�X�g�擾
  $host = gethostbyaddr($_SERVER["REMOTE_ADDR"]);

  foreach($badip as $value){ //����host
   if(eregi("$value$",$host)){
    error("���₳��܂���(host)",$dest);
  }}
  if(eregi("^mail",$host)
    || eregi("^ns",$host)
    || eregi("^dns",$host)
    || eregi("^ftp",$host)
    || eregi("^prox",$host)
    || eregi("^pc",$host)
    || eregi("^[^\.]\.[^\.]$",$host)){
    $pxck = "on";
  }
  if(eregi("ne\\.jp$",$host)||
    eregi("ad\\.jp$",$host)||
    eregi("bbtec\\.net$",$host)||
    eregi("aol\\.com$",$host)||
    eregi("uu\\.net$",$host)||
    eregi("asahi-net\\.or\\.jp$",$host)||
    eregi("rim\\.or\\.jp$",$host)
    ){$pxck = "off";}
  else{$pxck = "on";}

  if($pxck=="on" && PROXY_CHECK){
    if(proxy_connect('80') == 1){
      error("Nice proxy dumbass(80)",$dest);
    } elseif(proxy_connect('8080') == 1){
      error("Nice proxy dumbass(8080)",$dest);
    }
  }

  // No.�ƃp�X�Ǝ��Ԃ�URL�t�H�[�}�b�g
  srand((double)microtime()*1000000);
  if($pwd==""){
    if($pwdc==""){
      $pwd=rand();$pwd=substr($pwd,0,8);
    }else{
      $pwd=$pwdc;
    }
  }

  $c_pass = $pwd;
  $pass = ($pwd) ? substr(md5($pwd),2,8) : "*";
  $yd = gmdate("w", $time+9*60*60) ;
  $now = gmdate("m/d/y",$time+9*60*60)." ".gmdate("H:i",$time+9*60*60);
  if(DISP_ID){
    if($email&&DISP_ID==1){
      $now .= " ID:???";
    }else{
      $now.=" ID:".substr(crypt(md5($_SERVER["REMOTE_ADDR"].IDSEED.gmdate("Mdy", $time+9*60*60)),'id'),-8);
    }
  }
  //�e�L�X�g���`
  $email= CleanStr($email);  $email=ereg_replace("[\r\n]","",$email);
  $sub  = CleanStr($sub);    $sub  =ereg_replace("[\r\n]","",$sub);
  $url  = CleanStr($url);    $url  =ereg_replace("[\r\n]","",$url);
  $resto= CleanStr($resto);  $resto=ereg_replace("[\r\n]","",$resto);
  $com  = CleanStr($com);
  // ���s�����̓���B 
  $com = str_replace( "\r\n",  "\n", $com); 
  $com = str_replace( "\r",  "\n", $com);
  // �A�������s����s
  $com = ereg_replace("\n((�@| )*\n){3,}","\n",$com);
  if(!BR_CHECK || substr_count($com,"\n")<BR_CHECK){
    $com = nl2br($com);		//���s�����̑O��<br>��������
  }
  $com = str_replace("\n",  "", $com);	//\n�𕶎��񂩂�����B

  $name=ereg_replace("thf","��",$name);
  $name=ereg_replace("[\r\n]","",$name);
  $names=$name;
  $name = CleanStr($name);
  if(ereg("(#|��)(.*)",$names,$regs)){
    $cap = $regs[2];
    $cap=strtr($cap,"&amp;", "&");
    $cap=strtr($cap,"&#44;", ",");
    $name=ereg_replace("(#|��)(.*)","",$name);
    $salt=substr($cap."H.",1,2);
    $salt=ereg_replace("[^\.-z]",".",$salt);
    $salt=strtr($salt,":;<=>?@[\\]^_`","ABCDEFGabcdef"); 
    $name.="</b>!".substr(crypt($cap,$salt),-10)."<b>";
  }

  if(!$name) $name="Anonymous";
  if(!$com) $com="";
  if(!$sub) $sub=""; 

  //���O�ǂݍ���
  $fp=fopen(LOGFILE,"r+");
  flock($fp, 2);
  rewind($fp);
  $buf=fread($fp,1000000);
  if($buf==''){error("error load log",$dest);}
  $line = explode("\n",$buf);
  $countline=count($line);
  for($i = 0; $i < $countline; $i++){
    if($line[$i]!=""){
      list($artno,)=explode(",", rtrim($line[$i]));  //�t�ϊ��e�[�u���쐬
      $lineindex[$artno]=$i+1;
      $line[$i].="\n";
  }}

  // ��d���e�`�F�b�N
  $imax=count($line)>20 ? 20 : count($line)-1;
  for($i=0;$i<$imax;$i++){
   list($lastno,,$lname,,,$lcom,,$lhost,$lpwd,,,,$ltime,) = explode(",", $line[$i]);
   if(strlen($ltime)>10){$ltime=substr($ltime,0,-3);}
   if($host==$lhost||substr(md5($pwd),2,8)==$lpwd||substr(md5($pwdc),2,8)==$lpwd){$pchk=1;}else{$pchk=0;}
   if(RENZOKU && $pchk && $time - $ltime < RENZOKU)
    error("Error: flood detected, post discarded.",$dest);
   if(RENZOKU && $pchk && $time - $ltime < RENZOKU2 && $upfile_name)
    error("Error: flood detected, post discarded.",$dest);
   if(RENZOKU && $pchk && $com == $lcom && !$upfile_name)
    error("Error: dulplicate post detected",$dest);
  }

  // ���O�s���I�[�o�[
  if(count($line) >= LOG_MAX){
    for($d = count($line)-1; $d >= LOG_MAX-1; $d--){
      list($dno,,,,,,,,,$dext,,,$dtime,) = explode(",", $line[$d]);
      if(is_file($path.$dtime.$dext)) unlink($path.$dtime.$dext);
      if(is_file(THUMB_DIR.$dtime.'s.jpg')) unlink(THUMB_DIR.$dtime.'s.jpg');
      $line[$d] = "";
      treedel($dno);
    }
  }
  // �A�b�v���[�h����
  if($dest&&file_exists($dest)){
    $imax=count($line)>200 ? 200 : count($line)-1;
    for($i=0;$i<$imax;$i++){ //�摜�d���`�F�b�N
     list(,,,,,,,,,$extp,,,$timep,$chkp,) = explode(",", $line[$i]);
     if($chkp==$chk&&file_exists($path.$timep.$extp)){
      error("Error: dulplicate file exists",$dest);
    }}
  }
  list($lastno,) = explode(",", $line[0]);
  $no = $lastno + 1;
  isset($ext)?0:$ext="";
  isset($W)?0:$W="";
  isset($H)?0:$H="";
  isset($chk)?0:$chk="";
  $newline = "$no,$now,$name,$email,$sub,$com,$url,$host,$pass,$ext,$W,$H,$tim,$chk,\n";
  $newline.= implode('', $line);
  ftruncate($fp,0);
  set_file_buffer($fp, 0);
  rewind($fp);
  fputs($fp, $newline);

    //�c���[�X�V
  $find = false;
  $newline = '';
  $tp=fopen(TREEFILE,"r+");
  set_file_buffer($tp, 0);
  rewind($tp);
  $buf=fread($tp,1000000);
  if($buf==''){error("error tree update",$dest);}
  $line = explode("\n",$buf);
  $countline=count($line);
  for($i = 0; $i < $countline; $i++){
    if($line[$i]!=""){
      $line[$i].="\n";
      $j=explode(",", rtrim($line[$i]));
      if($lineindex[$j[0]]==0){
        $line[$i]='';
  } } }
  if($resto){
    for($i = 0; $i < $countline; $i++){
      $rtno = explode(",", rtrim($line[$i]));
      if($rtno[0]==$resto){
        $find = TRUE;
        $line[$i]=rtrim($line[$i]).','.$no."\n";
        $j=explode(",", rtrim($line[$i]));
        if(count($j)>MAX_RES){$email='sage';}
        if(!stristr($email,'sage')){
          $newline=$line[$i];
          $line[$i]='';
        }
        break;
  } } }
  if(!$find){if(!$resto){$newline="$no\n";}else{error("�X���b�h������܂���",$dest);}}
  $newline.=implode('', $line);
  ftruncate($tp,0);
  set_file_buffer($tp, 0);
  rewind($tp);
  fputs($tp, $newline);
  fclose($tp);
  fclose($fp);

    //�N�b�L�[�ۑ�
  setcookie ("pwdc", $c_pass,time()+7*24*3600);  /* 1�T�ԂŊ����؂� */
  if(function_exists("mb_internal_encoding")&&function_exists("mb_convert_encoding")
      &&function_exists("mb_substr")){
    if(ereg("MSIE|Opera",$_SERVER["HTTP_USER_AGENT"])){
      $i=0;$c_name='';
      mb_internal_encoding("SJIS");
      while($j=mb_substr($names,$i,1)){
        $j = mb_convert_encoding($j, "UTF-16", "SJIS");
        $c_name.="%u".bin2hex($j);
        $i++;
      }
      header("Set-Cookie: namec=$c_name; expires=".gmdate("D, d-M-Y H:i:s",time()+7*24*3600)." GMT",false);
    }else{
      $c_name=$names;
      setcookie ("namec", $c_name,time()+7*24*3600);  /* 1�T�ԂŊ����؂� */
    }
  }

  if($dest&&file_exists($dest)){
    rename($dest,$path.$tim.$ext);
    if(USE_THUMB){thumb($path,$tim,$ext);}
  }
  updatelog();

  echo "<html><head><META HTTP-EQUIV=\"refresh\" content=\"1;URL=".PHP_SELF2."\"></head>";
  echo "<body>$mes Updating index...</body></html>";
}

//�T���l�C���쐬
function thumb($path,$tim,$ext){
  if(!function_exists("ImageCreate")||!function_exists("ImageCreateFromJPEG"))return;
  $fname=$path.$tim.$ext;
  $thumb_dir = THUMB_DIR;     //�T���l�C���ۑ��f�B���N�g��
  $width     = MAX_W;            //�o�͉摜��
  $height    = MAX_H;            //�o�͉摜����
  // �摜�̕��ƍ����ƃ^�C�v���擾
  $size = GetImageSize($fname);
  switch ($size[2]) {
    case 1 :
      if(function_exists("ImageCreateFromGIF")){
        $im_in = @ImageCreateFromGIF($fname);
        if($im_in){break;}
      }
      if(!is_executable(realpath("./gif2png"))||!function_exists("ImageCreateFromPNG"))return;
      @exec(realpath("./gif2png")." $fname",$a);
      if(!file_exists($path.$tim.'.png'))return;
      $im_in = @ImageCreateFromPNG($path.$tim.'.png');
      unlink($path.$tim.'.png');
      if(!$im_in)return;
      break;
    case 2 : $im_in = @ImageCreateFromJPEG($fname);
      if(!$im_in){return;}
       break;
    case 3 :
      if(!function_exists("ImageCreateFromPNG"))return;
      $im_in = @ImageCreateFromPNG($fname);
      if(!$im_in){return;}
      break;
    default : return;
  }
  // ���T�C�Y
  if ($size[0] > $width || $size[1] >$height) {
    $key_w = $width / $size[0];
    $key_h = $height / $size[1];
    ($key_w < $key_h) ? $keys = $key_w : $keys = $key_h;
    $out_w = ceil($size[0] * $keys) +1;
    $out_h = ceil($size[1] * $keys) +1;
  } else {
    $out_w = $size[0];
    $out_h = $size[1];
  }
  // �o�͉摜�i�T���l�C���j�̃C���[�W���쐬
  if(function_exists("ImageCreateTrueColor")&&get_gd_ver()=="2"){
    $im_out = ImageCreateTrueColor($out_w, $out_h);
  }else{$im_out = ImageCreate($out_w, $out_h);}
  // ���摜���c���Ƃ� �R�s�[���܂��B
#  ImageCopyResampled($im_out, $im_in, 0, 0, 0, 0, $out_w, $out_h, $size[0], $size[1]);
  ImageCopyResized($im_out, $im_in, 0, 0, 0, 0, $out_w, $out_h, $size[0], $size[1]);
  // �T���l�C���摜��ۑ�
  ImageJPEG($im_out, $thumb_dir.$tim.'s.jpg',60);
  chmod($thumb_dir.$tim.'s.jpg',0666);
  // �쐬�����C���[�W��j��
  ImageDestroy($im_in);
  ImageDestroy($im_out);
}
//gd�̃o�[�W�����𒲂ׂ�
function get_gd_ver(){
  if(function_exists("gd_info")){
    $gdver=gd_info();
    $phpinfo=$gdver["GD Version"];
  }else{ //php4.3.0�����p
    ob_start();
    phpinfo(8);
    $phpinfo=ob_get_contents();
    ob_end_clean();
    $phpinfo=strip_tags($phpinfo);
    $phpinfo=stristr($phpinfo,"gd version");
    $phpinfo=stristr($phpinfo,"version");
  }
  $end=strpos($phpinfo,".");
  $phpinfo=substr($phpinfo,0,$end);
  $length = strlen($phpinfo)-1;
  $phpinfo=substr($phpinfo,$length);
  return $phpinfo;
}
//�t�@�C��md5�v�Z php4.2.0�����p
function md5_of_file($inFile) {
 if (file_exists($inFile)){
  if(function_exists('md5_file')){
    return md5_file($inFile);
  }else{
    $fd = fopen($inFile, 'r');
    $fileContents = fread($fd, filesize($inFile));
    fclose ($fd);
    return md5($fileContents);
  }
 }else{
  return false;
}}
//�c���[�폜
function treedel($delno){
  $fp=fopen(TREEFILE,"r+");
  set_file_buffer($fp, 0);
  flock($fp, 2);
  rewind($fp);
  $buf=fread($fp,1000000);
  if($buf==''){error("error tree del");}
  $line = explode("\n",$buf);
  $countline=count($line);
  if($countline>2){
    for($i = 0; $i < $countline; $i++){if($line[$i]!=""){$line[$i].="\n";};}
    for($i = 0; $i < $countline; $i++){
      $treeline = explode(",", rtrim($line[$i]));
      $counttreeline=count($treeline);
      for($j = 0; $j < $counttreeline; $j++){
        if($treeline[$j] == $delno){
          $treeline[$j]='';
          if($j==0){$line[$i]='';}
          else{$line[$i]=implode(',', $treeline);
            $line[$i]=ereg_replace(",,",",",$line[$i]);
            $line[$i]=ereg_replace(",$","",$line[$i]);
            $line[$i].="\n";
          }
          break 2;
    } } }
    ftruncate($fp,0);
    set_file_buffer($fp, 0);
    rewind($fp);
    fputs($fp, implode('', $line));
  }
  fclose($fp);
}
/* �e�L�X�g���` */
function CleanStr($str){
  global $admin;
  $str = trim($str);//�擪�Ɩ����̋󔒏���
  if (get_magic_quotes_gpc()) {//�����폜
    $str = stripslashes($str);
  }
  if($admin!=ADMIN_PASS){//�Ǘ��҂̓^�O�\
    $str = htmlspecialchars($str);//�^�O���֎~
    $str = str_replace("&amp;", "&", $str);//���ꕶ��
  }
  return str_replace(",", "&#44;", $str);//�J���}��ϊ�
}
/* ���[�U�[�폜 */
function usrdel($no,$pwd){
  global $path,$pwdc,$onlyimgdel;
  $host = gethostbyaddr($_SERVER["REMOTE_ADDR"]);
  $delno = array("dummy");
  $delflag = FALSE;
  reset($_POST);
    while ($item = each($_POST)){
     if($item[1]=='delete'){array_push($delno,$item[0]);$delflag=TRUE;}
    }
  if($pwd==""&&$pwdc!="") $pwd=$pwdc;
  $fp=fopen(LOGFILE,"r+");
  set_file_buffer($fp, 0);
  flock($fp, 2);
  rewind($fp);
  $buf=fread($fp,1000000);
  fclose($fp);
  if($buf==''){error("error user del");}
  $line = explode("\n",$buf);
  $countline=count($line);
  for($i = 0; $i < $countline; $i++){if($line[$i]!=""){$line[$i].="\n";};}
  $flag = FALSE;
  $countline=count($line)-1;
  for($i = 0; $i<$countline; $i++){
    list($dno,,,,,,,$dhost,$pass,$dext,,,$dtim,) = explode(",", $line[$i]);
    if(array_search($dno,$delno) && (substr(md5($pwd),2,8) == $pass || $dhost == $host||ADMIN_PASS==$pwd)){
      $flag = TRUE;
      $line[$i] = "";			//�p�X���[�h���}�b�`�����s�͋��
      $delfile = $path.$dtim.$dext;	//�폜�t�@�C��
      if(!$onlyimgdel){
        treedel($dno);
      }
      if(is_file($delfile)) unlink($delfile);//�폜
      if(is_file(THUMB_DIR.$dtim.'s.jpg')) unlink(THUMB_DIR.$dtim.'s.jpg');//�폜
    }
  }
  if(!$flag) error("Error: wrong password");
}
/* �p�X�F�� */
function valid($pass){
  if($pass && $pass != ADMIN_PASS) error("Error: wrong password");

  head($dat);
  echo $dat;
  echo "[<a href=\"".PHP_SELF2."\">Index</a>]\n";
  echo "[<a href=\"".PHP_SELF."\">Rebuild</a>]\n";
  echo "<table width='100%'><tr><th bgcolor=#E08000>\n";
  echo "<font color=#FFFFFF>Manager Mode</font>\n";
  echo "</th></tr></table>\n";
  echo "<p><form action=\"".PHP_SELF."\" method=POST>\n";
  // ���O�C���t�H�[��
  if(!$pass){
    echo "<center><input type=radio name=admin value=del checked>Manager panel ";
    echo "<input type=radio name=admin value=post>Manager post<p>";
    echo "<input type=hidden name=mode value=admin>\n";
    echo "<input type=password name=pass size=8>";
    echo "<input type=submit value=\" login \"></form></center>\n";
    die("</body></html>");
  }
}
/* �Ǘ��ҍ폜 */
function admindel($pass){
  global $path,$onlyimgdel;
  $all=0;$msg="";
  $delno = array("dummy");
  $delflag = FALSE;
  reset($_POST);
  while ($item = each($_POST)){
   if($item[1]=='delete'){array_push($delno,$item[0]);$delflag=TRUE;}
  }
  if($delflag){
  $fp=fopen(LOGFILE,"r+");
  set_file_buffer($fp, 0);
  flock($fp, 2);
  rewind($fp);
  $buf=fread($fp,1000000);
  if($buf==''){error("error admin del");}
  $line = explode("\n",$buf);
  $countline=count($line)-1;
  for($i = 0; $i < $countline; $i++){if($line[$i]!=""){$line[$i].="\n";};}
    $find = FALSE;
    for($i = 0; $i < $countline; $i++){
      list($no,$now,$name,$email,$sub,$com,$url,$host,$pw,$ext,$w,$h,$tim,$chk) = explode(",",$line[$i]);
      if($onlyimgdel=="on"){
        if(array_search($no,$delno)){//�摜�����폜
          $delfile = $path.$tim.$ext;	//�폜�t�@�C��
          if(is_file($delfile)) unlink($delfile);//�폜
          if(is_file(THUMB_DIR.$tim.'s.jpg')) unlink(THUMB_DIR.$tim.'s.jpg');//�폜
        }
      }else{
        if(array_search($no,$delno)){//�폜�̎��͋��
          $find = TRUE;
          $line[$i] = "";
          $delfile = $path.$tim.$ext;	//�폜�t�@�C��
          if(is_file($delfile)) unlink($delfile);//�폜
          if(is_file(THUMB_DIR.$tim.'s.jpg')) unlink(THUMB_DIR.$tim.'s.jpg');//�폜
          treedel($no);
        }
      }
    }
    if($find){//���O�X�V
      ftruncate($fp,0);
      set_file_buffer($fp, 0);
      rewind($fp);
      fputs($fp, implode('', $line));
    }
    fclose($fp);
  }
  // �폜��ʂ�\��
  echo "<input type=hidden name=mode value=admin>\n";
  echo "<input type=hidden name=admin value=del>\n";
  echo "<input type=hidden name=pass value=\"$pass\">\n";
  echo "<center><P>Select thread to moderate\n";
  echo "<p><input type=submit value=\"Delete\">";
  echo "<input type=reset value=\"Update\">";
  echo "[<input type=checkbox name=onlyimgdel value=on>Delete images only]";
  echo "<P><table border=1 cellspacing=0>\n";
  echo "<tr bgcolor=6080f6><th>Select</th><th>Post No.</th><th>Time</th><th>Subject</th>";
  echo "<th>Name</th><th>Comment</th><th>Host</th><th>File<br>(Bytes)</th><th>md5</th>";
  echo "</tr>\n";
  $line = file(LOGFILE);

  for($j = 0; $j < count($line); $j++){
    $img_flag = FALSE;
    list($no,$now,$name,$email,$sub,$com,$url,
         $host,$pw,$ext,$w,$h,$time,$chk) = explode(",",$line[$j]);
    // �t�H�[�}�b�g
    $now=ereg_replace('.{2}/(.*)$','\1',$now);
    $now=ereg_replace('\(.*\)',' ',$now);
    if(strlen($name) > 10) $name = substr($name,0,9).".";
    if(strlen($sub) > 10) $sub = substr($sub,0,9).".";
    if($email) $name="<a href=\"mailto:$email\">$name</a>";
    $com = str_replace("<br />"," ",$com);
    $com = htmlspecialchars($com);
    if(strlen($com) > 20) $com = substr($com,0,18) . ".";
    // �摜������Ƃ��̓����N
    if($ext && is_file($path.$time.$ext)){
      $img_flag = TRUE;
      $clip = "<a href=\"".IMG_DIR.$time.$ext."\" target=_blank>".$time.$ext."</a><br>";
      $size = filesize($path.$time.$ext);
      $all += $size;			//���v�v�Z
      $chk= substr($chk,0,10);
    }else{
      $clip = "";
      $size = 0;
      $chk= "";
    }
    $bg = ($j % 2) ? "d6d6f6" : "f6f6f6";//�w�i�F

    echo "<tr bgcolor=$bg><th><input type=checkbox name=\"$no\" value=delete></th>";
    echo "<th>$no</th><td><small>$now</small></td><td>$sub</td>";
    echo "<td><b>$name</b></td><td><small>$com</small></td>";
    echo "<td>$host</td><td align=center>$clip($size)</td><td>$chk</td>\n";
    echo "</tr>\n";
  }

  echo "</table><p><input type=submit value=\"Delete$msg\">";
  echo "<input type=reset value=\"Update\"></form>";

  $all = (int)($all / 1024);
  echo "[Total data: <b>$all</b> KB]";
  die("</center></body></html>");
}
function init(){
  $err="";
  $chkfile=array(LOGFILE,TREEFILE);
  if(!is_writable(realpath("./")))error("Error: is root writeable?");
  foreach($chkfile as $value){
    if(!file_exists(realpath($value))){
      $fp = fopen($value, "w");
      set_file_buffer($fp, 0);
      if($value==LOGFILE)fputs($fp,"1,10/01/03 00:00,Anonymous,,,It works!,,,,,,,,\n");
      if($value==TREEFILE)fputs($fp,"1\n");
      fclose($fp);
      if(file_exists(realpath($value)))@chmod($value,0666);
    }
    if(!is_writable(realpath($value)))$err.=$value."�������܂���<br>";
    if(!is_readable(realpath($value)))$err.=$value."��ǂ߂܂���<br>";
  }
  @mkdir(IMG_DIR,0777);@chmod(IMG_DIR,0777);
  if(!is_dir(realpath(IMG_DIR)))$err.=IMG_DIR."������܂���<br>";
  if(!is_writable(realpath(IMG_DIR)))$err.=IMG_DIR."�������܂���<br>";
  if(!is_readable(realpath(IMG_DIR)))$err.=IMG_DIR."��ǂ߂܂���<br>";
  if(USE_THUMB){
    @mkdir(THUMB_DIR,0777);@chmod(THUMB_DIR,0777);
    if(!is_dir(realpath(IMG_DIR)))$err.=THUMB_DIR."������܂���<br>";
    if(!is_writable(realpath(THUMB_DIR)))$err.=THUMB_DIR."�������܂���<br>";
    if(!is_readable(realpath(THUMB_DIR)))$err.=THUMB_DIR."��ǂ߂܂���<br>";
  }
  if($err)error($err);
}

init();		
$iniv=array('mode','name','email','sub','com','pwd','upfile','upfile_name','resto','pass','res','post','no');
foreach($iniv as $iniva){
  if(!isset($$iniva)){$$iniva="";}
}
switch($mode){
  case 'regist':
    regist($name,$email,$sub,$com,'',$pwd,$upfile,$upfile_name,$resto);
    break;
  case 'admin':
    valid($pass);
    if($admin=="del") admindel($pass);
    if($admin=="post"){
      echo "</form>";
      form($post,$res,1);
      echo $post;
      die("</body></html>");
    }
    break;
  case 'usrdel':
    usrdel($no,$pwd);
  default:
    if($res){
      updatelog($res);
    }else{
      updatelog();
      echo "<META HTTP-EQUIV=\"refresh\" content=\"0;URL=".PHP_SELF2."\">";
    }
}
?>
