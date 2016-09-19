<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

<h1>Приложение <i>Словарь</i></h1>

<div ng-app="ngAppMain" ng-strict-di>
    <div ng-controller="MainController">

        <!--Получаем имя пользователя-->
        <div ng-hide="test_start">
            <div class="input-group">
                <span class="input-group-addon" id="basic-addon1">Ваше имя:</span>
                <input type="text"  ng-model="user_name" class="form-control" placeholder="Username" aria-describedby="basic-addon1">
            </div>
            <button type="button" class="btn btn-default" ng-click="sendName()">Начать тест</button>
        </div>

        <!--Тестирование-->
        <div ng-show="test_start && !test_end">
            Выберите правильный перевод для слова "{{iterator}}":
            <span ng-repeat="value in values[iterator]">
                <br>
                <a ng-click="sendAnswer(value)">{{value}}</a>
            </span>
            <div class="alert alert-danger" role="alert" ng-show="error">
                <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                <span class="sr-only">Ошибка:</span>
                Вы ошиблись. У вас есть еще одна попытка.
            </div>
        </div>

        <!--Вывод очков и ошибок-->
        <div ng-show="test_end">
            <h4>Тест окончен</h4>
            <p>Пользователь: {{name}} набрал {{score}} очков</p>
            <span ng-show="word_errors.length > 0">
                <h5>Необходимо изучить слова:</h5>
                {{word_errors}}
            </span>
        </div>
    </div>
</div>
