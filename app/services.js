var appServices = angular.module('appServices', ['ngCookies']);

appServices.service(function() {
	return {}
});

app.factory('helper', function() {
  var  formating_date = function (date){
  			if (typeof date == 'object') {
  				var now = new Date(date);
				var formated_date = now.format("dd.mm.yyyy");
  	  			return formated_date;
  			}
  			return date;
  		};
  return {
   'formating_date': formating_date,
   'clearAddictionModel' : false
  };
});