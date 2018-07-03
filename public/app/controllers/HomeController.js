app.controller('HomeController', ['$scope', '$http', '$sce', '$location', function($scope, $http, $sce, $location){

  $scope.isLoading = true;
  $scope.infoData = false;

  $http.get(__homepageUri + 'rest/info')
    .then(function(res){
      $scope.infoData = res.data;
      $scope.isLoading = false;
    }, function(res){
      $scope.$parent.showAlert(res);
    });

}]);