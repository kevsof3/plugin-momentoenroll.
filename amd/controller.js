angular.module('momentoenroll', [])
.controller('StatusController', ['$scope', 'Utilities', '$interval', function($scope, Utilities, $interval){
    $scope.users_sync_status = {};
    $scope.categories_sync_status = {};
    $scope.courses_sync_status = {};
    $scope.enrolments_sync_status = {};
    $scope.call_sync = null;

    $scope.query_sync = function(){
        Utilities.execute('enrol/momentoenroll/rest.php', 'POST', {action: 'status'}, function(response){
            $scope.users_sync_status = response.users_sync;
            $scope.categories_sync_status = response.categories_sync;
            $scope.courses_sync_status = response.courses_sync;
            $scope.enrolments_sync_status = response.enrolments_sync;

            if (!(response.users_sync.status != 1 || response.categories_sync.status != 1 || response.courses_sync.status != 1 || response.enrolments_sync.status != 1)){
                    $interval.cancel($scope.call_sync);
            }
        }, null);
    };

    $scope.load = function(){
        $scope.sync_status = {};

        Utilities.execute('enrol/momentoenroll/rest.php', 'POST', {action: 'status'}, function(response){
            $scope.users_sync_status = response.users_sync;
            $scope.categories_sync_status = response.categories_sync;
            $scope.courses_sync_status = response.courses_sync;
            $scope.enrolments_sync_status = response.enrolments_sync;

            if (response.users_sync.status != 1 || response.categories_sync.status != 1 || response.courses_sync.status != 1 || response.enrolments_sync.status != 1){
                $scope.call_sync = $interval($scope.query_sync, 3000);
            }
        }, null);
    };

    $scope.load();
}])
.controller('SyncController', ['$scope', 'Utilities', '$interval', function($scope, Utilities, $interval){
    $scope.sync_running = 0;

    $scope.confirmSync = function(){
        $('#confirmSync').modal('show');
    };

    $scope.startSync = function(){
        $scope.sync_running = 1;

        Utilities.execute('enrol/momentoenroll/rest.php', 'POST', {action: 'start_sync'}, function(response){
            stop = $interval(function () {
                $scope.load();
                $interval.cancel(stop);
            }, 3000);
        }, null);
    };

    $scope.load = function(){
        Utilities.execute('enrol/momentoenroll/rest.php', 'POST', {action: 'status'}, function(response){
            $scope.sync_running = response.sync.status;
        }, null);
    };

    $scope.load();
}])
.factory('Utilities', ['$http', '$location', function($http, $location){
    function _execute(uri, metodo, data, onSuccess, onError){
        //var baseUrl = $location.protocol() + '://' + $location.host() + ($location.port() == 80 ? '' : ':' + $location.port()) + '/';
		$http({
			method: metodo,
			url: baseUrl + uri,
			data: data,
			headers: {'Cache-Control': 'no-cache'}
		}).then(function successCallback(response) {
			console.log(response);

			if (response){
                if (onSuccess){
                    onSuccess(response.data);
                }
			}
			
			}, function errorCallback(response) {
			console.log(response);
			if (onError)
				onError(response);
		});
	};
    
    return {
        execute: function(uri, metodo, data, onSuccess, onError){
			_execute(uri, metodo, data, onSuccess, onError);
		}
    };
}]);
