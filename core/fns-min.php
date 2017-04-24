<?php

// если че, то грузим модель
function __autoload($class_name) {
    include 'model/'.strtolower($class_name) . '.php';
}

// mysql link
$link;

// подкл. к БД
function conn()
{
    global $link;
    
    $link = mysqli_connect("127.0.0.1", DB_USER, DB_PASS, DB_BASE);
    if($link)
    {
        mysqli_query ($link,"set character_set_client='utf8'");
        mysqli_query ($link,"set character_set_results='utf8'");
        mysqli_query ($link,"set collation_connection='utf8_general_ci'");
    }
    
    if(mysqli_connect_errno()) 
    {
        printf("Mysql error: no connetion %s\n", mysqli_connect_error());
        exit();
    }
    
    
    /*
	if(@$db = mysql_connect("127.0.0.1", DB_USER, DB_PASS))
	{
		mysql_query ("set character_set_client='utf8'");
		mysql_query ("set character_set_results='utf8'");
		mysql_query ("set collation_connection='utf8_general_ci'");
	}
	else
	{
		die("Помилка з'єднання з базою данних - невірний логін або пароль");
	}

	if(!mysql_select_db(DB_BASE, $db))
	{
		die("Помилка з'єднання з базою данних - неможливо обрати потрібну БД - база не існує");
	}
	return $db;*/
}

// выполнить SQL запрос без возврата результата
function query($query)
{
    global $link;
    
    conn();
         
    $result = mysqli_query($link, $query);
        
    if(!$result)
    {
        die(" $query <hr>Mysql query error: ".mysqli_error($link)." \n");
    }
    
    return $result;
}

function q_array($query)
{
	$r = query($query);
	for($i=mysqli_num_rows($r); $i>0; $i--)
	{
		$f=mysqli_fetch_array($r, MYSQLI_ASSOC);
		$result[] = $f;
	}
	if(!$result)
	{
		return array();
	}
	
	return $result;
}

function row($query)
{
	return mysqli_fetch_array(query($query));
}

// грузим темплейт
function load_tpl($template, $data_array='')
{
	// папка шаблона
	$template_folder = "view";

	if(is_array($template))
	{
		$data_array = $template;
		$template = 'global-template';
	}

	// если есть шаблон
	if(file_exists($template_folder . "/" . $template . ".html"))
	{
		// задерживаем буфер
		ob_start();

		// переносим все элементы массива в переменные
		is_array($data_array) ? extract($data_array, EXTR_OVERWRITE) : '';

		// подключаем шаблон
		require($template_folder . "/" . $template . ".html");

		// возвращаем получившуюся кашу из данных и html
		return ob_get_clean();
	}
	else 
	{
		// нет шаблона
		return "Error. Template $template not found in folder $template_folder";
	}
}

// пагинатор
function paginator_qty($table, $per_page = 100)
{
	$qty = row("SELECT count(id) as qty FROM `$table`");
	return ceil($qty['qty'] / $per_page);
}

function paginator_limit($table, $current_page = 0, $per_page = 100)
{
	$qty = paginator_qty($table, $per_page);
	
	if($current_page)
	{
		$current_page--;
	}
	
	if($current_page)
	{	
		$limit = ($current_page * $per_page) . ", " . $per_page;
	}
	else 
	{
		$limit = "0, $per_page";
	}
	return " limit " . $limit;
}

function paginator_interface($mod='', $page_var='page', $current_page=0, $total=0)
{
	if(!$current_page)
	{
		$current_page = 1;
	}
	
	for ($i=1; $i <= $total; $i++)
	{
		if($i == $current_page)
		{
			$navi .= "<a class='paginator paginator__active' href='?mod=$mod&$page_var=$i'><b>$i</b></a> ";
		}
		else 
		{
			$navi .= "<a class='paginator' href='?mod=$mod&$page_var=$i'>$i</a> ";
		}
	}
	
	if($total>1)
	{
		return $navi;
	}
}



// ведем логи
function logi()
{
	$ip = $_SERVER['REMOTE_ADDR'];
	$date = mktime();
	$client_name = $_SERVER['HTTP_USER_AGENT'];
	$rewarded_from = getenv(HTTP_REFERER);
	$host = $_SERVER['HTTP_HOST'];
	$sid = $_SESSION['SID'];
	$filename = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	query("
		insert into log
		set
			ip='".$ip."',
			date='".$date."',
			sid = '$sid',
			client_name='".$client_name."',
			host='".$host."',
			rewarded_from='".$rewarded_from."',
			filename='".$filename."'");
}


function client_ip()
{
    // define ip
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {$ip = $_SERVER['HTTP_CLIENT_IP'];}
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];}
    else {$ip = $_SERVER['REMOTE_ADDR'];}

    return $ip;
}


function is_mobile()
{
     return (bool)preg_match('#\b(ip(hone|od|ad)|android|opera m(ob|in)i|windows (phone|ce)|blackberry|tablet'.
        '|s(ymbian|eries60|amsung)|p(laybook|alm|rofile/midp|laystation portable)|nokia|fennec|htc[\-_]'.
        '|mobile|up\.browser|[1-4][0-9]{2}x[1-4][0-9]{2})\b#i', $_SERVER['HTTP_USER_AGENT'] );
}


function memc($key, $data)
{
    return false;
    continue;

    if(class_exists('Memcache'))
    {
        $memcache = new Memcache;
        $memcache->connect('localhost', 11211); //or die ("Can't connect memcache");
    }

    if(!$data && $memcache)
    {
        file_put_contents('debug.txt', "SET KEY $key\n");
        return $memcache->get($key);
    }
    elseif($memcache)
    {
        file_put_contents('debug.txt', "GET KEY $key len: ".strlen($memcache->set($key, $data))."\n");
        return $memcache->set($key, $data);
    }
}


// загрузка картинок
function file_upload($name, $future_filename='', $extension='.jpg', $folder='i')
{	
	if($_FILES[$name])
	{
		if (($_FILES[$name]['error'] > 0) && ($_FILES[$name]['error'] <= 3))
		{
			
			switch ($_FILES[$name]['error'])
			{
				case 1: echo 'Error upload file upload_max_filesize'; break;
				case 2: echo 'Error upload file max_file_size'; break;
				case 3: echo 'Error upload file - attack attempt'; break;
			}
			exit;
		}
		if($_FILES[$name]['error'] != 4)
		{
			if($_FILES[$name]["type"] == 'image/png')
			{
				$extension = '.png';
			}
			
			$future_filename ?
			$randomfilename = $future_filename.$extension :
			$randomfilename = rand().rand().$extension;

			$upfile = "$folder/".$randomfilename;
			if(file_exists($upfile))
			{
				$result.= "Error upload file already exist";
				exit;
			}
			if ($_FILES[$name]['tmp_name'])
			{
				# echo $_FILES[$name]['tmp_name']."_bla_";
				if (!move_uploaded_file($_FILES[$name]['tmp_name'], $upfile))
				{
					$result.= 'Error upload file move_uploaded_file';
					exit;
				}
			}
			else
			{
				$result.= 'Error upload file';
				exit;
			}
			
			echo $result;	
		}
	}
	return $randomfilename;
}

// ресайз картинок
function resize_im($image, $suffix = 's_', $w=null, $h=null, $data_folder = 'i', $quality = 90)
{
	if(!$w) $width=300;
	else $width=$w;
	
	substr($image, -4) == '.png' ? $is_png = true : $is_png = false; 
	
	if($is_png)
	{
		$srcImage = imagecreatefrompng("$data_folder/".$image) ;
	}
	else 
	{
		$srcImage = ImageCreateFromJPEG("$data_folder/".$image) ;
	}
	

	$srcWidth = ImageSX( $srcImage );
	$srcHeight = ImageSY( $srcImage );
	if( $srcWidth<=$width && !$w) $width=$srcWidth;
	$q = $srcWidth/$width;
	if(!$h)$y = $srcHeight/$q;
	else $y = intval($h);
	$destImage = imagecreatetruecolor( $width, $y);
	imagecopyresampled( $destImage, $srcImage, 0, 0, 0, 0, $width, $y, $srcWidth, $srcHeight );

	if($is_png)
	{
		imagepng($destImage, "$data_folder/$suffix".$image );
	}
	else 
	{
		ImageJPEG($destImage, "$data_folder/$suffix".$image, $quality );
	}
	ImageDestroy($destImage);
	ImageDestroy($srcImage);
	return $suffix.$image;
}


// безопасный вывод данных
function safe_output($array)
{
	foreach ($array as $key => &$val)
	{
		if(is_array($val))
		{
			$array[$key] = safe_output($val);
		}
		else 
		{
			$array[$key] = htmlspecialchars(stripslashes($val));
		}
	}
	
	return $array;
}


// безопасный ввод даннх в базу
function safe_db_input($array)
{
	foreach ($array as $key => &$val)
	{
		if(is_array($val))
		{
			$array[$key] = safe_db_input($val);
		}
		else 
		{
			$array[$key] = mysql_escape_string($val);
		}
	}
	return $array;
}



function l($str)
{
    // return eng lang
    if(getUserLanguage() != 'ru') return $str;

    // return translations
	$langpack = file_get_contents('i18n.ini');
	if(!strstr($langpack, $str))
	{
		file_put_contents('i18n.ini', $str."^\n", FILE_APPEND);
	}

	$langpack = explode("\n", file_get_contents('i18n.ini'));
	if($_SESSION['lang'] != 'ru')
	{
		foreach ($langpack as $word)
		{
			$word = explode("^", $word);
			if($word[0] == $str && !empty($word[1]))
			{
				return trim($word[1]);
			}
		}
	}
	return trim($str);
}

function getUserLanguage() {
    $langs = array();
    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
// break up string into pieces (languages and q factors)
        preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i',
            $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang_parse);
        if (count($lang_parse[1])) {
// create a list like â??enâ?? => 0.8
            $langs = array_combine($lang_parse[1], $lang_parse[4]);
// set default to 1 for any without q factor
            foreach ($langs as $lang => $val) {
                if ($val === '') $langs[$lang] = 1;
            }
// sort list based on value
            arsort($langs, SORT_NUMERIC);
        }
    }
//extract most important (first)
    foreach ($langs as $lang => $val) { break; }
//if complex language simplify it
    if (stristr($lang,"-")) {$tmp = explode("-",$lang); $lang = $tmp[0]; }
    return $lang;
}