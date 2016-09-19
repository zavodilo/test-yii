angular.module('ngAppMain', [])
	.controller('MainController', ['$scope','$http', function($scope, $http){
		console.log('controller working');
		//имя пользователя по умолчанию
		$scope.user_name = 'default';
		//совершил ли ошибку с этим словом
		$scope.error = false;
		//тест завершен
		$scope.test_end = false;
		//тест начался
		$scope.test_start = false;
		//кол-во ошибок, не больше 3
		$scope.error_count = 0;

		//отправляем имя пользователя и получаем тест
		$scope.sendName = function() {
			$http({
				method: 'GET',
				url: '/index.php?r=test/index' + '&name=' + $scope.user_name
			}).then(function successCallback(response) {
				$scope.test_start = true;
				//слова требующие перевода
				$scope.keys = response.data.keys;
				//слово которое переводится
				$scope.iterator = $scope.keys.shift();
				//значения из которых надо выбрать
				$scope.values = response.data.values;
				console.log('Тест начался.', $scope.iterator, $scope.keys, $scope.values);

			}, function errorCallback(response) {
				console.error('Нет связи с сервером.', response);
			});
		}

		//отправляем слово и перевод для сравнения
		$scope.sendAnswer = function(value) {
			$http({
				method: 'GET',
				url: '/index.php?r=test/Equals' + '&word1=' + $scope.iterator + '&word2=' + value
			}).then(function successCallback(response) {
				if (response.data.data == true || $scope.error == true) {
					//совершил ли ошибку с этим словом
					$scope.error = false;
					//слово которое переводится
					$scope.iterator = $scope.keys.shift();
					if ($scope.iterator == undefined) {
						end();
					}
				} else {
					//совершил ли ошибку с этим словом
					$scope.error = true;
					//кол-во ошибок, не больше 3
					$scope.error_count++;
					if ($scope.error_count >= 3) {
						end();
					}
				}
			}, function errorCallback(response) {
				console.error('Нет связи с сервером.', response);
			});
		}

		function end() {
			$scope.test_end = true;

			$http({
				method: 'GET',
				url: '/index.php?r=test/end'
			}).then(function successCallback(response) {
				//получаем имя пользователя от сервера
				$scope.name = response.data.name;
				//получаем очки пользователя
				$scope.score = response.data.score;
				//получаем слова, в который ошибся
				$scope.word_errors = response.data.word_errors;
				console.log('Тест закончился.', $scope.word_errors);

			}, function errorCallback(response) {
				console.error('Нет связи с сервером.', response);
			});

		}
	}]);
