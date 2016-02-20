<?php
class Controller
{
	function exec()
	{
		$data['title'] = 'Страница не найдена';
		$data['body'] = load_tpl('404');
		return load_tpl('global-template', $data);
	}
}
