<?php

class Controller
{
	function exec()
	{

		// задаем тайтл
		$data['title'] = 'Ничего себе. Все людям. ';

		// впихиваем html-контент прямо в строку
		// так делать не надо, но работать будет
		$data['body'] .= ' <i>привет мир</i> ' ;

		// можно подгрузить контент из шаблона, который лежить view/hello-world.html
		$data['body'] .= load_tpl('hello-world');

		// впихиваем данные в массив. главное - это ключ массива 'somedata'
		$tmp['somedata'] = 'какие-то левые данные';

		// грузим шаблон и впихиваем в него нашу 'somedata'
		$data['body'] .= load_tpl('hello2', $tmp);

		// и теперь все, что у нас получилось возвращаем в глобальном шаблоне
		return load_tpl($data);


		// еще пару слов

		// MYSQL
		// {
		// есть три функи q_array, query, row
		// их можно вызвать отовсюду
		//
		// q_array - возвращает весь массив данных
		// $array = q_array('SELECT * FROM `table`');
		//
		// второй вариант - query.  тупо для занесения или обновления данных
		// query('INSERT || UPDATE table `hello` ... ');
		//
		// третий row - достаем только одну запись из БД
		// row("SELECT * FROM user where login = 'superman'");
		// }


		// и еще

		// к шаблону можно кастомно цеплять js, css
		// $data['css'][''] = '/css/file1.css';
		// $data['css'][''] = '/css/somefile2.css';
		// $data['js'][''] = '/js/blabla.js';
		// $data['js'][''] = 'http://cdn.js.com/hoho.js';
		// $data['js'][''] = '/js/file2.js';


		// и глянь controller/ajax.php
		// и в принципе ты готов писать код

	}
}
