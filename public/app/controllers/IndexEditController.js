app.controller('IndexEditController', ['$scope', '$http', '$sce', '$location', '$routeParams', '$mdDialog', '$timeout',  function($scope, $http, $sce, $location, $routeParams, $mdDialog, $timeout){

  $scope.indexName = typeof $routeParams.indexName !== 'undefined' ? $routeParams.indexName : '';
  $scope.editMode = typeof $routeParams.indexName !== 'undefined';
  $scope.index = {
    number_of_shards: 1,
    number_of_replicas: 1,
    analysis: {
      filter:{},
      analyzer:{}
    }
  };
  $scope.pageTitle = $scope.editMode ? $scope.$parent.trans('indices.edit.title', {indexName: $scope.indexName}) : $scope.$parent.trans('indices.add.title');

  $scope.fetchData = function() {
    $scope.isLoading = true;
    $http.get(__homepageUri + 'rest/indices/' + $scope.indexName)
      .then(function(res){
        res.data[$scope.indexName].settings.index.number_of_shards = parseInt(res.data[$scope.indexName].settings.index.number_of_shards);
        res.data[$scope.indexName].settings.index.number_of_replicas = parseInt(res.data[$scope.indexName].settings.index.number_of_replicas);
        $scope.index = res.data[$scope.indexName].settings.index;
        console.log($scope.index);
        $scope.isLoading = false;
      }, function(res){
        $scope.isLoading = false;
        $scope.$parent.showAlert(res);
      });
  }

  $scope.$parent.subMenu = 'datastudio';
  if($scope.editMode) {
    $scope.isLoading = true;
    $scope.fetchData();
  }

  $scope.submitting = false;

  $scope.submit = function(form) {
    if (form.$valid) {
      $scope.submitting = true;
      var data = {
        number_of_shards: $scope.index.number_of_shards,
        number_of_replicas: $scope.index.number_of_replicas,
        analysis: $scope.index.analysis
      };
      $http.put(__homepageUri + 'rest/index/put?indexName=' + encodeURIComponent(form.indexName.$modelValue), data)
        .then(function (res) {
          $scope.submitting = false;
          $location.path(__homepageUri + 'indices');
        }, function (res) {
          $scope.submitting = false;
          $scope.$parent.showAlert(res, $scope.$parent.trans('dialog.error'));
        });
    }
  }

  $scope.deleting = false;

  $scope.deleteIndex = function() {
    var confirm = $mdDialog.confirm()
      .title($scope.$parent.trans('indices.delete.dialogTitle'))
      .textContent($scope.$parent.trans('indices.delete.confirm', {indexName: $scope.indexName}))
      .ariaLabel($scope.$parent.trans('indices.delete.dialogTitle'))
      .ok($scope.$parent.trans('dialog.yes'))
      .cancel($scope.$parent.trans('dialog.no'));

    $mdDialog.show(confirm).then(function(result) {
      $scope.deleting = true;
      $http.delete(__homepageUri + 'rest/indices/' + $scope.indexName + '/delete')
        .then(function (res) {
          $scope.deleting = false;
          $location.path(__homepageUri + 'indices');
        }, function (res) {
          $scope.deleting = false;
          $scope.$parent.showAlert(res, $scope.$parent.trans('dialog.error'));
        });
    }, function() {

    });
  };


}]);