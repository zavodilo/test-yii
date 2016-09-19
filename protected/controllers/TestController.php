<?php

class TestController extends Controller
{
	protected $description = [
		'apple' => 'яблоко',
		'pear' => 'персик',
		'orange' => 'апельсин',
		'grape' => 'виноград',
		'lemon' => 'лимон',
		'pineapple' => 'ананас',
		'watermelon' => 'арбуз',
		'coconut' => 'кокос',
		'banana' => 'банан',
		'pomelo' => 'помело',
		'strawberry' => 'клубника',
		'raspberry' => 'малина',
		'melon' => 'дыня',
		'apricot' => 'абрикос',
		'cherry' => 'вишня',
		'mango' => 'манго',
		'pomegranate' => 'гранат',
		'pear' => 'слива',
	];


	/**
	 * Сохраняет имя пользователя в сессию, т.к. сессия хранится на сервере, то это не противоречит тз
	 * Возвращает json из списка слов для теста и списка вариантов ответов
	 */
	public function actionIndex()
	{
		//массив слов для теста
		$keys = [];
		//варианты ответов
        $values = [];
		//русские и английские слова из массива
		$description_values = array_merge(array_keys($this->description), array_values($this->description));
		foreach ($description_values as $word) {
			//определяем ключ из массива
            if (isset($this->description[$word])) {
                $key = $this->description[$word];
            } else {
                $key = array_search($word, $this->description);
            }
			$keys[] = $key;
			//добавляем правильный ответ в массив
            $values[$key] = [$word];
            $help_array = [];
			//если слово есть в ключах массива, то верем значения для вариантов ответов,
			//если нет, то берем ключи для вариантов ответов
            if (isset($this->description[$word])) {
                $help_array = array_keys($this->description);
            } else {
                $help_array = array_values($this->description);
            }
			//удаляем дубликат
            unset($help_array[array_search($word, $help_array)]);
            $help_array = array_values($help_array);
			//добавляем варианты ответов
			for($i = 0; $i < 3; $i++) {
                //тут не могут попадаться одинаковые значения
				//получаем случайный вариант ответа
                $help_value = $help_array[rand(0, count($help_array) - 1)];
                $values[$key][] = $help_value;
				//удаляем, чтоб не было дублей
                unset($help_array[array_search($help_value, $help_array)]);
                $help_array = array_values($help_array);
			}
			//перемешиваем массив
			shuffle($values[$key]);
		}
		//кол-во очков
        Yii::app()->session['score'] = 0;
		//ошибки
        Yii::app()->session['word_errors'] = [];
		//имя пользователя
		Yii::app()->session['name'] = $_GET['name'];
		//отдаем json
		return $this->renderJSON(['keys' => $keys, 'values' => $values]);
	}

	/**
	 * Сравнивает слова на соответствие переводу, записывает в сессию очки и ошибки
	 * Возвращает json, где в data хранится ответ, правильно ли ответил
	 */
	public function actionEquals()
	{
		//слово для которого выбран ответ
		$word1 = $_GET['word1'];
		//ответ пользователя
		$word2 = $_GET['word2'];
		//ищем есть ли перевод в массиве
        if (
            (isset($this->description[$word1]) && $this->description[$word1] == $word2) ||
            (isset($this->description[$word2]) && $this->description[$word2] == $word1)
        ) {
			//Сохраним очки в сессию
            Yii::app()->session['score'] = isset(Yii::app()->session['score']) ? Yii::app()->session['score'] + 1 : 1;
            return $this->renderJSON(['data' => true]);
		} else {
            //Сохраним ошибки в сессию
            if (isset(Yii::app()->session['word_errors'])) {
                $array_errors = Yii::app()->session['word_errors'];
                $array_errors[$word1] = true;
                Yii::app()->session['word_errors'] = $array_errors;
            } else {
                Yii::app()->session['word_errors'] =  [$word1];
            }

            return $this->renderJSON(['data' => false]);
		}
	}

    public function actionEnd()
    {
        return $this->renderJSON([
			//имя пользователя
            'name' => Yii::app()->session['name'],
			//очки
            'score' => Yii::app()->session['score'],
			//строка ошибок
            'word_errors' => implode(', ', array_keys(Yii::app()->session['word_errors'])),
        ]);
    }

	/**
	 * Return data to browser as JSON and end application.
	 * @param array $data
	 */
    protected function renderJSON($data)
	{
		header('Content-type: application/json');
		echo CJSON::encode($data);

		foreach (Yii::app()->log->routes as $route) {
			if($route instanceof CWebLogRoute) {
				$route->enabled = false; // disable any weblogroutes
			}
		}
		Yii::app()->end();
	}
}