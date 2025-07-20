<?php
// прикол этого класса в том, что стоит написать метод 'function vtoroi_primer(){}'
// как сразу можно вызвать по адресу http://karkas.dev/ajax/vtoroi_primer
// и не трахаться с роутерами и обработками параметров.

class Controller
{
	function exec()
	{
		// берем второй агрумет например если
		// вызываем 'http://karkas.dev/ajax/primer'
		// то результат $method 'primer'
		$method = next(explode('/', $_GET['route']));

		// если метод есть, то просто его вызываем
		if(method_exists($this, $method))
		{
			call_user_func(array($this, $method));
		}
	}

	// вот и вся магия ajax
	function primer()
	{
		echo "Вот и вызван наш пример! Ура!";
	}

	function vtoroi_primer()
	{
		echo "Второй пример!";
	}
}
