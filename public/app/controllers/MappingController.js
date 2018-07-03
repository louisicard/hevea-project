app.controller('MappingController', ['$scope', '$http', '$sce', '$location', '$routeParams', '$mdDialog', '$timeout',  function($scope, $http, $sce, $location, $routeParams, $mdDialog, $timeout){

  $scope.isLoading = true;

  $scope.$parent.subMenu = 'datastudio';

  $scope.indexName = typeof $routeParams.indexName !== 'undefined' ? $routeParams.indexName : '';

  $scope.pageTitle = $scope.$parent.trans('mapping.title', {indexName: $scope.indexName});

  $scope.mappingName = null;
  $scope.mapping = null;
  $scope.editMode = false;

  $scope.fetchData = function() {
    $scope.isLoading = true;
    $http.get(__homepageUri + 'rest/indices')
      .then(function(res){
        for(var index in res.data) {
          if(index === $scope.indexName) {
            var index = res.data[index];
            var mappings = [];
            for(var mappingName in index.mappings) {
              mappings.push(mappingName);
            }
            if(mappings.length > 1) {
              $scope.$parent.showAlert($scope.$parent.trans('mapping.multipleMappings'));
            }
            else if(mappings.length == 1) {
              $scope.mappingName = mappings[0];
              $scope.mapping = index.mappings[mappings[0]];
              $scope.editMode = true;
            }
            console.log($scope.mappingName);
            console.log($scope.mapping);
          }
        }
        $scope.isLoading = false;
      }, function(res){
        $scope.isLoading = false;
        $scope.$parent.showAlert(res);
      });
  }

  $scope.fetchData();

  $scope.getSubfieldNames = function(fieldName) {
    if($scope.mapping != null && typeof $scope.mapping.properties[fieldName] !== 'undefined' && typeof $scope.mapping.properties[fieldName].fields !== 'undefined') {
      var subfields = [];
      for(var subfield in $scope.mapping.properties[fieldName].fields) {
        subfields.push(subfield);
      }
      return subfields;
    }
    return [];
  }

}]);