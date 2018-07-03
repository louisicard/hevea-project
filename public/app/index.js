var app = angular.module("HeveaApp", [
  'ngAnimate',
  'ngRoute',
  'ngResource',
  'ngAria',
  'ngSanitize',
  'ngMaterial'
]);

//POLYFILLS
if (!String.prototype.startsWith) {
  String.prototype.startsWith = function (searchString, position) {
    position = position || 0;
    return this.substr(position, searchString.length) === searchString;
  };
}

if (!String.prototype.endsWith) {
  String.prototype.endsWith = function(searchString, position) {
    var subjectString = this.toString();
    if (typeof position !== 'number' || !isFinite(position) || Math.floor(position) !== position || position > subjectString.length) {
      position = subjectString.length;
    }
    position -= searchString.length;
    var lastIndex = subjectString.lastIndexOf(searchString, position);
    return lastIndex !== -1 && lastIndex === position;
  };
}

if ( !String.prototype.includes ) {
  String.prototype.includes = function(search, start) {
    'use strict';
    if (typeof start !== 'number') {
      start = 0;
    }

    if (start + search.length > this.length) {
      return false;
    } else {
      return this.indexOf(search,start) !== -1;
    }
  };
}

app.config(['$locationProvider', '$routeProvider', function($locationProvider, $routeProvider){
  var devRoute = window.location.href.indexOf('app_dev.php') >= 0;
  var prefix = devRoute ? '/app_dev.php' : '';

  $routeProvider.
  when(prefix + (devRoute ? '' : '/'), {
    templateUrl: 'app/views/home.html',
    controller: 'HomeController'}).
  when(prefix + '/login', {
    templateUrl: 'app/views/login.html',
    controller: 'SecurityController'}).
  when(prefix + '/indices', {
    templateUrl: 'app/views/indices.html',
    controller: 'IndicesController'}).
  when(prefix + '/indices/edit/:indexName', {
    templateUrl: 'app/views/index_edit.html',
    controller: 'IndexEditController'}).
  when(prefix + '/indices/add', {
    templateUrl: 'app/views/index_edit.html',
    controller: 'IndexEditController'}).
  when(prefix + '/mapping/edit/:indexName', {
    templateUrl: 'app/views/mapping_edit.html',
    controller: 'MappingController'}).
  otherwise({
    template: "<div></div>",
    controller: function ($window, $location, $rootScope) {
      if (!$rootScope.isInitialLoad) {
        $window.location.href = $location.absUrl();
      }
    }
  });;
  $locationProvider.html5Mode(true);
}]);

app.run(function ($rootScope) {
  $rootScope.$on('$routeChangeSuccess', function() {
    $rootScope.isInitialLoad = (typeof $rootScope.isInitialLoad === "undefined");
  });
});

angular.module('HeveaApp').directive('jsonText', function() {
  return {
    restrict: 'A',
    require: 'ngModel',
    link: function(scope, element, attr, ngModel) {
      function into(input) {
        try {
          return JSON.parse(input);
        }
        catch(ex) {
          return null;
        }
      }
      function out(data) {
        return JSON.stringify(data, null, 2);
      }
      ngModel.$parsers.push(into);
      ngModel.$formatters.push(out);

      ngModel.$validators.json = function(modelValue, viewValue) {
        return modelValue !== null;
      };

    }
  };
});