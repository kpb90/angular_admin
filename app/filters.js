var appFilters = angular.module('appFilters', []);
appFilters.filter('unsafe', function($sce) {
                return function(val) {
                    return $sce.trustAsHtml(val);
                };
            });