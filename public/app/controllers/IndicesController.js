app.controller('IndicesController', ['$scope', '$http', '$sce', '$location', function($scope, $http, $sce, $location){

  $scope.isLoading = true;
  $scope.indices = {};
  $scope.$parent.subMenu = 'datastudio';
  $scope.basePath = __homepageUri;

  $scope.fetchData = function() {
    $scope.isLoading = true;
    $http.get(__homepageUri + 'rest/indices')
      .then(function(res){
        $scope.indices = res.data;
        console.log(res.data);
        $scope.isLoading = false;
      }, function(res){
        $scope.isLoading = false;
        $scope.$parent.showAlert(res);
      });
  }

  $scope.fetchData();

  $scope.formatSize = function(size) {
    if(size < 1024)
      return size + ' ' + $scope.$parent.trans('indices.list.bytes');
    if(size < 1024 * 1024)
      return Math.round(size / 1024 * 100) / 100 + ' ' + $scope.$parent.trans('indices.list.kiloBytes');
    if(size < 1024 * 1024 * 1024)
      return Math.round(size / 1024 / 1024 * 100) / 100 + ' ' + $scope.$parent.trans('indices.list.megaBytes');
    if(size < 1024 * 1024 * 1024 * 1024)
      return Math.round(size / 1024 / 1024 / 1024 * 100) / 100 + ' ' + $scope.$parent.trans('indices.list.gigaBytes');
    return size + ' ' + $scope.$parent.trans('indices.list.bytes');
  }

  $scope.formatDate = function(timestamp) {
    var date = new Date(parseInt(timestamp));
    return date.toLocaleString();
  }

  $scope.displayMappingInformation = function(mappings) {
    var keys = [];
    for(var key in mappings){
      keys.push(key);
    }
    if(keys.length == 0) {
      return '<span class="no-mapping">Pas de mapping défini</span>';
    }
    else if(keys.length == 1){
      return '<span class="mapping-ok">Mapping OK</span>';
    }
    else {
      return '<span class="multiple-mappings">Multiples mapings</span>';
    }
  }

  $scope.getMappingNames = function(mappings) {
    var keys = [];
    for(var key in mappings){
      keys.push(key);
    }
    return keys;
  }


}]);