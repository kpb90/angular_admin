var app = angular.module('myApp',['appControllers','appDirectives','appFilters','appServices','ngRoute','ngResource','appConfig','appStores']);

app.config(['$routeProvider',
  function($routeProvider) {
    $routeProvider.
      when('/pages', {
        templateUrl: 'templates/pages.html',
        controller: 'TableCtrl'
      }).
      when('/catalog', {
        templateUrl: 'templates/excel.html',
        controller: 'excelCtrl'
      }).
      when('/login', {
        templateUrl: 'templates/auth.html',
        controller: 'excelCtrl'
      }).
      when('/:type', {
        templateUrl: 'templates/news.html',
        controller: 'TableCtrl'
      }).
      otherwise({
        redirectTo: '/pump'
      });
  }]);