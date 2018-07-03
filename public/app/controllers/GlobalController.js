app.controller('GlobalController', ['$scope', '$routeParams', '$location', '$http', '$mdDialog', function($scope, $routeParams, $location, $http, $mdDialog){

  $scope.bodyClass = 'default';
  $scope.menuClass = 'expanded';
  $scope.subMenu = null;

  $scope.toggleMenu = function() {
    $scope.menuClass = $scope.menuClass == 'expanded' ? 'collapsed' : 'expanded';
  }

  $scope.showAlert = function(err, title, done) {

    if(title == null) {
      title = $scope.trans('dialog.error');
    }

    try {
      var elasticError = typeof err.data.error !== 'undefined' ? JSON.parse(err.data.error) : null;
    }catch(ex){ var elasticError = null; }

    $mdDialog.show(
      $mdDialog.alert()
        .clickOutsideToClose(true)
        .title(title)
        .textContent($scope.trans('dialog.serverError', {text: elasticError != null ? elasticError.error.reason : err}))
        .ariaLabel(title)
        .ok('OK')
    ).then(function () {
      if(typeof done !== 'undefined' && done !== null) {
        done();
      }
    });
  }

  $scope.rwdHide = function() {
    if(jQuery(window).width() <= 450) {
      $scope.toggleMenu();
    }
  }

  $scope.trans = function(id, params) {
    var parts = id.split(".");
    var obj = __i18n;
    var r = false;
    for(var i = 0; i < parts.length; i++) {
      if(typeof obj[parts[i]] !== 'undefined') {
        obj = obj[parts[i]];
        r = true;
      }
      else {
        return id;
      }
    }
    if(r && typeof params !== 'undefined') {
      obj = obj.replace(/%\w+%/g, function (all) {
        var placeholder = all.substring(1, all.length - 1);
        return params[placeholder] || placeholder;
      });
    }

    return r ? obj : id;
  }

}]);