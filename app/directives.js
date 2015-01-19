var appDirectives = angular.module('appDirectives', ['ui.bootstrap']);


appDirectives.directive('entityTable', function($compile,$window) {
	return {
		scope: {
			data: "=data",
		}, 
		restrict: 'E',
		controller: function ($scope, $resource, configure, stores, entityServerData, helper, $http) {
			var resource_params = {};
			$scope.modal_dialog = {};
			$scope.addiction_table_parameters = {'order':[], 'dir':''};
			$scope.editable_row_from_addiction_table = false;	
			var itemsPerPage = 10;
			$scope.$on('renderDynamicField', function(event, new_field) {
				console.log ($scope.form_model.dynamic_fields);
				$scope.form_model.dynamic_fields.push (new_field);
			});
			$scope.data.dynamic_fields = [];

			if ($scope.data.type != 'undefined'&&$scope.data.type) {
				if (typeof configure[$scope.data.type]!='undefined'&&typeof configure[$scope.data.type].limit !='undefined'){
					$scope.data.limit = configure[$scope.data.type].limit;
				}
			}

			$scope.$watch(function() { return angular.toJson($scope.data);}, function() {
				 resource_params = {};

				 if (typeof $scope.data != 'undefined'){
				 	if ($scope.data.type != 'undefined'&&$scope.data.type) {

						if (typeof configure[$scope.data.type]!='undefined'&&typeof configure[$scope.data.type].limit !='undefined') {
  							itemsPerPage = configure[$scope.data.type].limit;
  						}

						if (typeof $scope.pagination === 'undefined') {
							$scope.pagination = {
								'itemsPerPage': itemsPerPage,
								'maxSize':5,
							};
						}	

				 		if (helper.clearAddictionModel === true) {
				 			clear_model ();
							helper.clearAddictionModel = false;
				 		}
						
						for(var param in $scope.data) {
							resource_params[param] = $scope.data[param];
						}
						$scope.configure_types = configure[$scope.data.type].types;
						$scope.form_template = configure[$scope.data.type].form_template;
						$scope.dialog_add_button_template = configure[$scope.data.type].dialog_add_button_template ? configure[$scope.data.type].dialog_add_button_template : '';
						//$scope.toolbar = configure[$scope.data.toolbar].toolbar;
						if (configure[$scope.data.type].toolbar!='undefined'){
							$scope.toolbar = configure[$scope.data.type].toolbar;
							$scope.action_templates = [];
							if (typeof $scope.toolbar !== 'undefined') {
								for (var i = 0; i < $scope.toolbar.length; i++) {
									if (typeof $scope.toolbar[i].action_template != 'undefined') {
										$scope.action_templates.push($scope.toolbar[i].action_template);
									}
								}
							}
						}	

						$scope.action_templates = $.unique($scope.action_templates);
						get_data_from_server(resource_params);
					}
				}
			});

			$scope.selectedRows = [];
			$scope.error_message = false;
			$scope.editable_row = false;
			$scope.stores = stores;
			
			$scope.action_handler = function (handler){
				if (Object.keys($scope.selectedRows).length > 0) {
					var numb_editable_row = Object.keys($scope.selectedRows)[0],
						editable_row = $scope.rows[numb_editable_row],
						param = {'type':handler,'required':{'addiction_editable_row_id':editable_row['id']},'not_cache':Math.random()};
						$.extend($scope.addiction_table_parameters, param);
						$scope.editable_row_from_addiction_table = true;
						helper.clearAddictionModel = true;
						debugger;
				}
			}
			$scope.reset_editable_row_from_addiction_table = function () {
				$scope.editable_row_from_addiction_table = false;
				//$scope.selectedRows = [];
			}
			$scope.lightRow = function (id, number_row) {
				$scope.selectedRows = [];
				$scope.selectedRows[number_row] = id;
			}
			
			$scope.multiLightRow = function (id, number_row) {
				var index = $scope.selectedRows.indexOf(id);
				if (index > -1) {
		    		$scope.selectedRows.splice(index, 1);
				}
				else {
					$scope.selectedRows[number_row] = id;
				}
			}

			$scope.isSelectedRow = function (id) {
				return $scope.selectedRows.indexOf(id) != -1;
			}

			$scope.removeItem = function () {
				var ids_for_delete =  $scope.selectedRows.join(','),
					remove_resource_params = {'type':resource_params['type']};
				var indexes_for_remove = Object.keys($scope.selectedRows);
				$scope.rows = $.grep($scope.rows,function(row, index_row){
					return indexes_for_remove.indexOf(index_row.toString()) < 0;
				});
				$scope.selectedRows = [];
				remove_resource_params['ids'] = ids_for_delete.replace(/\,+/g,",");
				if (remove_resource_params['ids'][0]==',') {
					remove_resource_params['ids'] = remove_resource_params['ids'].substr(1);
				}
				entityServerData.removes(remove_resource_params, function(data) {
					if (data[0]!=1){
						alert('Ошибка при удалении');
					}
				});
			}	

			$scope.addItem = function () {
				clear_input_file();
				$scope.dialog_title = "Добавление: " + configure[$scope.data.type].dialog_title;
				$scope.form_model = {'dynamic_fields':[]};
				$scope.data.dynamic_fields = [];
				$scope.editable_row = true;
				$scope.modal_dialog.form.$setPristine(true)
			}
			
			$scope.editItem = function (row, type) {
				clear_input_file();
				$scope.dialog_title = "Редактирование: " + configure[$scope.data.type].dialog_title;
				$scope.form_model = {};
				row = row || '';
				type = type || '';
				if (row) {
					$scope.editable_row = row;
				}
				else {
					if (Object.keys($scope.selectedRows).length > 1) {
						$scope.error_message = 'ошибка';
					}
					else {
						$scope.error_message = false;
						var numb_editable_row = Object.keys($scope.selectedRows)[0];
						$scope.editable_row = $scope.rows[numb_editable_row];
					}
				}
				//get_data_from_server(resource_params);
				if (typeof $scope.editable_row != 'undefined') {
					var concrete_resource_params = {'type':resource_params['type'],'id':$scope.editable_row.id};
					entityServerData.query(concrete_resource_params,function(data) {
						$scope.form_model = angular.copy (data.results);
						$scope.form_model.dynamic_fields = angular.copy (data.dynamic_fields);
					});
				} else {
					alert('Выберите строку');
				}
				//$scope.form_model = angular.copy ($scope.editable_row);
			}
					
			$scope.reset_editable_row = function () {
				$scope.editable_row = false;
			}
		
			$scope.save_model_form = function () {
				//resource_params['id'] = 0;
				var save_resource_params = {'type':resource_params['type']};
				var fd = new FormData();
				if (typeof resource_params.required != 'undefined') {
					angular.forEach(resource_params.required, function(value, key) {
  						 fd.append(key, value);
					});
					
				}

				for(var param in $scope.form_model) {
						if (typeof $scope.form_model[param] == 'object'&&!$.isArray ($scope.form_model[param])){
						fd.append(param, helper.formating_date($scope.form_model[param]));
					}
					else {
						fd.append(param,$scope.form_model[param]);
					}
				}

				if ($("input[type='file']")[0]) {
					var file = $("input[type='file']")[0].files[0];
					   fd.append("uploadfile", file);
				}
				
				if($scope.form_model['id']) {
					$http.post('/catalog/'+save_resource_params['type']+'/controller.php', fd, {
			       				 withCredentials: true,
			       				 headers: {'Content-Type': undefined },
			       				 transformRequest: angular.identity
				    }).success(function (data,status) {
			        	if (data[0]==1) {
							var numb_editable_row = Object.keys($scope.selectedRows)[0];
							$scope.rows[numb_editable_row] = $scope.form_model;
							console.log ($scope.form_model);
						}
			     	}).error(function (data,status){
			     		console.log ('Ошибка соедениения с сервером при обновлении');
			     	});
				}
				else {
					$http.post('/catalog/'+save_resource_params['type']+'/controller.php', fd, {
			       				 withCredentials: true,
			       				 headers: {'Content-Type': undefined },
			       				 transformRequest: angular.identity
				    }).success(function (new_id, status) {
			        	if (new_id[0]!=0) {
							$scope.rows[new_id] = $scope.form_model;
							get_data_from_server(resource_params);
						} else {
							alert('Ошибка при добавлении');
						}
			     	}).error(function (data,status){
			     		alert('Ошибка соедениения с сервером при добавлении');
			     	});
				}
			}
		
			$scope.orderby = function (field_name){
				var temp  = !$scope.data.order[field_name];
				$scope.data.sort = field_name;
				$scope.data.dir = $scope.data.order[field_name];
				$scope.data.order = [];
				$scope.data.order[field_name] = temp;
			}

			function get_data_from_server (resource_params) {
				entityServerData.query(resource_params,function(data) {
					//$scope.rows = data;
					$scope.rows = data.results;
					$scope.pagination['bigTotalItems'] = data.total;
				});
			}

		    $scope.pageChanged = function() {
		    	$scope.data.start = ($scope.pagination['bigCurrentPage']*$scope.pagination['itemsPerPage']) - $scope.pagination['itemsPerPage'];
  	      		console.log('Page changed:' +  $scope.data.start);
		    };

		    var clear_model = function () {
		    	$scope.selectedRows = [];
				$scope.error_message = false;
				$scope.editable_row = false;

				if (typeof $scope.data.form !='undefined') {
					angular.forEach($scope.data.form, function(value, key) {
  						$scope.data.form[key] = '';
					});
				}
				$scope.data.order =[];
				$scope.data.dir ='';
				$scope.data.sort ='';
				$scope.pagination.bigCurrentPage = 1;
		    }

		    $scope.remove_dynamic_field = function (dynamic_field, type) {
				var dynamic_id = dynamic_field.id;
				if (dynamic_id) {
					entityServerData.removes({'dynamic_id':dynamic_id,'type':type}, function(data) {
						if (data[0]!=1){
							alert('Ошибка при удалении');
						}
						else {
							remove_dynamic_field_from_model (dynamic_field);
						}
					});
				} else {
					remove_dynamic_field_from_model (dynamic_field);
				}
			}
		
			var remove_dynamic_field_from_model = function (dynamic_field, type) {
				var length = $scope.form_model.dynamic_fields.length,
					dynamic_id = dynamic_field.id;
					i;
				for (var i = 0; i < length; i ++) {
					val = $scope.form_model.dynamic_fields[i];
					if ((dynamic_id&&val.id==dynamic_id)||(val.opt==dynamic_field.opt&&val.val===dynamic_field.val)) {
						$scope.form_model.dynamic_fields.splice(i, 1);
						break;
					}
				}
			},
			clear_input_file = function () {
				angular.forEach(
				    $("input[type='file']"),
				    function(inputElem) {
				     $(inputElem).val(null);
				    });
			}

		},
		templateUrl: 'templates/table.html',
		link: function($scope, iElm, iAttrs, controller) {
		}
	};
}).
directive('entityTopMenu', function($compile,$location) {
	return {
		scope: {
			data: "=data",
		}, 
		restrict: 'E',
		controller: function ($scope, Top_menu) {
			$scope.top_menu = Top_menu;
		},
		templateUrl: 'templates/top_menu.html',
		link: function($scope, iElm, iAttrs, controller) {
			$scope.active_top_menu = '#'+$location.path();
			$scope.$on('$locationChangeStart', function(event, next, current) {
				$scope.active_top_menu = '#'+$location.path();
			});
		}
	};
});
//entityServerData
appDirectives.factory('entityServerData', function($resource) {
  return $resource('/catalog/:type/controller.php', {'type':'@type'}, {
    query: {method:'GET', params:{}, isArray:false},	
    removes: {method:'DELETE', params:{}, isArray:false},
    update: {method:'POST', params:{}, isArray:false},
    add: {method:'PUT', params:{}, isArray:false},
  });
});

appDirectives.factory('pagesServerData', function($resource) {
  		return $resource('/catalog/pages/controller.php', {}, {
		    query: {method:'GET', params:{}, isArray:true},	
		    removes: {method:'GET', params:{}, isArray:false},
		    update: {method:'POST', params:{}, isArray:false},
		    add: {method:'POST', params:{}, isArray:false},
  		});
});

appDirectives.directive("fileread", [function () {
    return {
        scope: {
            fileread: "="
        },
        link: function (scope, element, attributes) {
            element.bind("change", function (changeEvent) {
                var reader = new FileReader();
                reader.onload = function (loadEvent) {
                    scope.$apply(function () {
                        scope.fileread = loadEvent.target.result;
                    });
                }
                reader.readAsDataURL(changeEvent.target.files[0]);
            });
        }
    }
}]);

appDirectives.directive('ckEditor', [function () {
        return {
            require: '?ngModel',
            restrict: 'C',
            link: function (scope, elm, attr, model) {
                var isReady = false;
                var data = [];
                var ck = CKEDITOR.replace(elm[0],{'filebrowserBrowseUrl':'/catalog/kcfinder/browse.php?type=files',
												  'filebrowserImageBrowseUrl':'/catalog/kcfinder/browse.php?type=images',
												  'filebrowserFlashBrowseUrl':'/catalog/kcfinder/browse.php?type=flash',
												  'filebrowserUploadUrl':'/catalog/kcfinder/upload.php?type=files',
												  'filebrowserImageUploadUrl':'/catalog/kcfinder/upload.php?type=images',
												  'filebrowserFlashUploadUrl':'/catalog/kcfinder/upload.php?type=flash'});
                function setData() {
                    if (!data.length) { return; }

                    var d = data.splice(0, 1);
                    ck.setData(d[0] || '<span></span>', function () {
                        setData();
                        isReady = true;
                    });
                }

                ck.on('instanceReady', function (e) {
                    if (model) { setData(); }

                    if ($(".fff .panel-body").length) {
                    	var tallestcolumn = $(document).height();
 						var header_height = $(".cke_top.cke_reset_all").height()+100;
 						console.log (header_height);
  				    	$(".fff .panel-body").height(tallestcolumn);
			        	$(".cke_contents.cke_reset").height(tallestcolumn-header_height);
                    }
                });

                elm.bind('$destroy', function () {
                    ck.destroy(false);
                });

                if (model) {
                    ck.on('change', function () {
                        scope.$apply(function () {
                            var data = ck.getData();
                            if (data == '<span></span>') {
                                data = null;
                            }
                            model.$setViewValue(data);
                        });
                    });

                    model.$render = function (value) {
                        if (model.$viewValue === undefined) {
                            model.$setViewValue(null);
                            model.$viewValue = null;
                        }

                        data.push(model.$viewValue);

                        if (isReady) {
                            isReady = false;
                            setData();
                        }
                    };
                }
            }
        };
    }]);


    // Angular File Upload module does not include this directive
    // Only for example


    /**
    * The ng-thumb directive
    * @author: nerv
    * @version: 0.1.2, 2014-01-09
    */
    appDirectives.directive('ngThumb', ['$window', function($window) {
        var helper = {
            support: !!($window.FileReader && $window.CanvasRenderingContext2D),
            isFile: function(item) {
                return angular.isObject(item) && item instanceof $window.File;
            },
            isImage: function(file) {
                var type =  '|' + file.type.slice(file.type.lastIndexOf('/') + 1) + '|';
                return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
            }
        };

        return {
            restrict: 'A',
            template: '<canvas/>',
            link: function(scope, element, attributes) {
                if (!helper.support) return;

                var params = scope.$eval(attributes.ngThumb);

                if (!helper.isFile(params.file)) return;
                if (!helper.isImage(params.file)) return;

                var canvas = element.find('canvas');
                var reader = new FileReader();

                reader.onload = onLoadFile;
                reader.readAsDataURL(params.file);

                function onLoadFile(event) {
                    var img = new Image();
                    img.onload = onLoadImage;
                    img.src = event.target.result;
                }

                function onLoadImage() {
                    var width = params.width || this.width / this.height * params.height;
                    var height = params.height || this.height / this.width * params.width;
                    canvas.attr({ width: width, height: height });
                    canvas[0].getContext('2d').drawImage(this, 0, 0, width, height);
                }
            }
        };
    }]);