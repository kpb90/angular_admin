var appControllers = angular.module('appControllers', ['ui.bootstrap','angularBootstrapNavTree','angularFileUpload']);

appControllers.controller('AppCtrl', function($scope, $locale, rus_locale) {
	angular.copy(rus_locale, $locale);
});

appControllers.controller('AuthCtrl', function($scope, $locale, $timeout, rus_locale, $cookies, $http, $location) {
	$scope.status = {
      authorized: false,
    };
	$scope.error_login_psw = false;
    $http.post('/catalog/users/controller.php', {
                       'task': 'auntefitication'
           }).success(function (data,status) {
                if(data['result']==1) {
                  $scope.status.authorized = true;
              	}
              	else
              	{	
              		$location.path("/login")
              	}
              }).error(function (data,status){
              });

    $scope.loginByCredentials = function(username, password) {
       $http.post('/catalog/users/controller.php', {
                       'task': 'login',
                       'login': username,
                      'pwd': password
           }).success(function (data,status) {
                if(data['result']==1) {
                  $scope.status.authorized = true;
                  $location.path("/")
                  console.log ($scope.status.authorized);
                } else {
                  $scope.status.authorized = false;
                  $scope.error_login_psw = true;
                  $timeout(function(){$scope.error_login_psw = false}, 5000);
                }
              }).error(function (data,status){
                $scope.status.authorized = false;
              });

    };

     $scope.logout = function() {
       $http.post('/catalog/users/controller.php', {
                       'task': 'logout'
           }).success(function (data,status) {
           	if(data['result']==1) {
           		 $scope.status.authorized = false;
           		 $location.path("/login")
           	}
              }).error(function (data,status){
        });
    };

});

appControllers.controller('TableCtrl', function($scope, $location, $routeParams,helper) {
	$scope.table_parameters = {
		type: $routeParams.type,
		order:[],
		dir:true
	}
	
	$scope.formating_date = helper.formating_date
});


appControllers.controller('AdditionalTableCtrl', function($scope) {
	
});

  appControllers.controller('AbnTestController', function($scope, $timeout, $resource, pagesServerData) {
    var apple_selected, tree, current_branch, firstable_init = true;
    $scope.my_data = [];
    $scope.my_tree = tree = {};
	$scope.data = {content:'', menu_title:'', file_name:''};
	$scope.add_page = false;
	$scope.form_model = {};
	$scope.remove_page =  false;
	$scope.name_remove_page = '';
	$scope.error_message = "";
	pagesServerData.query({'task':'getTree'}, function(data) {
		$scope.my_data.push ({'id':0, 'label':'Страницы сайта', 'children':[{}], 'expanded':true});
		$scope.my_tree_handler(tree.get_first_branch());
	});

    $scope.my_tree_handler = function(branch) {
    	if (branch.id > 0) {
			pagesServerData.query({'task':'getPageInfo','id':branch.id}, function(data) {
				 if (data[0]) {
						$scope.data.content = data[0].content ? data[0].content : '';
						$scope.data.menu_title = data[0].menu_title ? data[0].menu_title : '';
						$scope.data.file_name = data[0].file_name ? data[0].file_name : '';
				 }
			});
    	}

		if (branch.expanded==true&&typeof branch.children[0].label == 'undefined') {
			pagesServerData.query({'task':'getTree','node':branch.id}, function(data) {
				var count_remove_el = branch.children.length;
       			for (var i = 0; i < data.length; i++) {
					var leaf = data[i].leaf==false ? [{}] : [];
					tree.add_branch(branch, {'id':data[i].id, 'label':data[i].label, 'children':leaf});
				}
				branch.children.splice(0, count_remove_el);
			}).$promise.then(function() {
				if (firstable_init===true) {
       				tree.select_next_branch(tree.get_first_branch());
       				firstable_init = false;
				}
    		});

		}
    };

    $scope.saveItem = function () {
    	var current_branch = tree.get_selected_branch(),
    		menu_title = $scope.form_model.name_page;
    	
    	pagesServerData.query({'task':'addPage','parent':current_branch.id,'title':menu_title}, function(data) {
			if (data[0].result == 1) {
				tree.add_branch(current_branch, {'id':data[0].id, 'label':menu_title, 'children':[]});
			}
		});
    }
	
	$scope.savePageInfo = function () {
		var current_branch = tree.get_selected_branch(),
			index_root_branch_in_tree = -1;
	 	pagesServerData.update({'task':'savePageInfo','text':$scope.data.content, 'menu_title':$scope.data.menu_title, 'file_name':$scope.data.file_name,'id':current_branch.id}, function(data) {
			if (data.result == 1) {
				var root = get_root_of_branch (current_branch);
				index_root_branch_in_tree = tree.get_index_branch_in_tree (root.id);
				tree.find_branch (index_root_branch_in_tree, null, rename_menu_title);
			}
		});	
	}

    $scope.removeItem = function () {
    		$scope.remove_page =  true;
    		$scope.name_remove_page = tree.get_selected_branch().label;
    }

    $scope.reset_remove_page = function () {
		$scope.remove_page = false;
    }

    $scope.addItem = function () {
		$scope.add_page = true;
    }

    $scope.reset_add_page = function () {
    	$scope.add_page = false;
    	$scope.form_model.name_page = '';
    }

    var delete_branch = function (item) {
	    item.delete = true;
        item.children = [];
  		console.log ('delete_branch');
	}

	var rename_menu_title = function (item) {
		item.label = $scope.data.menu_title;
	}

	var get_root_of_branch = function (branch) {
		var root = branch;
		while (branch = tree.get_parent_branch(branch)){
			root = branch;
		}
		return root;
	}

    $scope.removePage = function () {
		var current_branch = tree.get_selected_branch(),
			index_root_branch_in_tree = -1,
			root = {};

		pagesServerData.removes({'task':'deletePage', 'id':current_branch.id}, function(data) {
			if (data.result == "success") {
				root = get_root_of_branch (current_branch);
				index_root_branch_in_tree = tree.get_index_branch_in_tree (root.id);
				tree.find_branch (index_root_branch_in_tree,null,delete_branch);
			}
		});	
		//tree.remove_branch (current_branch);
    }
});


appControllers.controller('DatepickerDemoCtrl', function($scope) {
 $scope.dateOptions = {
    'show-weeks':false,
    'show-today':false
  };

    $scope.format = 'dd.MM.yyyy';
  	
    $scope.open = function($event) {
    $event.preventDefault();
    $event.stopPropagation();
    $scope.opened = true;
  };
});

appControllers.controller('AddCharacteristicCtrl', function($scope, $http) {
	$scope.add_field = false;
	// type - pump, trinity_catalog
	$scope.addField = function (form_model_id, type) {
		$scope.form_model_id = form_model_id;
		$scope.type_add_field = type;
		$scope.add_field = true;
	}

	$scope.saveField = function () {	
		var form_model_id = $scope.form_model_id ? $scope.form_model_id : 0,
			obj_for_redraw = {'id':form_model_id, 'opt':$scope.new_characteristic_opt,'val':$scope.new_characteristic_val};
			new_characteristic_opt = $scope.new_characteristic_opt,
			new_characteristic_val = $scope.new_characteristic_val;
		console.log (obj_for_redraw);
		if ($scope.form_model_id){
			if ($scope.type_add_field) {
				$http.put('/catalog/pump/controller.php', {
		        		'task':'add_new_field',
		            	'form_model_id': $scope.form_model_id,
		            	'type_add_field': $scope.type_add_field,
		            	'new_characteristic_opt': $scope.new_characteristic_opt,
		            	'new_characteristic_val': $scope.new_characteristic_val,
		     	}).success(function (data,status) {
		           $scope.$emit('renderDynamicField', obj_for_redraw);
		     	}).error(function (data,status){
		     		console.log ('save field error');
		     	});
			}
		} else {
		    $scope.$emit('renderDynamicField', obj_for_redraw);
		}
	}

	$scope.resetAddField = function () {
		$scope.form_model_id = 0;
		$scope.type_add_field = '';
		$scope.add_field = false;
		$scope.new_characteristic_opt = '';
		$scope.new_characteristic_val = '';
	}

});
appControllers.controller('storeCtrl', function($scope, $http) {

	if (typeof $scope.stores[$scope.field.store]=='undefined')
	{
		$http.get('/catalog/dynamic_store.php?type='+$scope.field.store)
		.success(function(data){$scope.stores[$scope.field.store]=data})
		.error(function(){console.log ('error');});
	}

});
appControllers.controller('excelCtrl', function($scope, $http) {
	$scope.message = '';
	$scope.send_file_to_server = function () {
				var fd = new FormData();
				if ($("input[type='file']")[0]) {
					var file = $("input[type='file'][name='catalog']")[0].files[0];
					   fd.append("catalog", file);
					   fd.append("action", 'upload_catalog');
				}
				$http.post('/catalog/catalog.php', fd, {
			       				 withCredentials: true,
			       				 headers: {'Content-Type': undefined },
			       				 transformRequest: angular.identity
				 }).success(function (data,status) {
			        	$scope.message =  data;
			     	}).error(function (data,status){
			     		$scope.message = 'Ошибка при загрузке excel';
			     	});


			}

});


   appControllers.controller('AppController', ['$scope', 'FileUploader', function($scope, FileUploader) {
        var uploader = $scope.uploader = new FileUploader({
            url: 'upload.php'
        });

        // FILTERS

        uploader.filters.push({
            name: 'customFilter',
            fn: function(item /*{File|FileLikeObject}*/, options) {
               // return this.queue.length < 10;
               return true;
            }
        });
		var file_change = function () {
			console.log('file_change');
		}
        // CALLBACKS

        uploader.onWhenAddingFileFailed = function(item /*{File|FileLikeObject}*/, filter, options) {
            console.info('onWhenAddingFileFailed', item, filter, options);
        };
        uploader.onAfterAddingFile = function(fileItem) {
            console.info('onAfterAddingFile', fileItem);
        };
        uploader.onAfterAddingAll = function(addedFileItems) {
            console.info('onAfterAddingAll', addedFileItems);
            uploader.uploadAll();
        };
        uploader.onBeforeUploadItem = function(item) {
            console.info('onBeforeUploadItem', item);
        };
        uploader.onProgressItem = function(fileItem, progress) {
            console.info('onProgressItem', fileItem, progress);
        };
        uploader.onProgressAll = function(progress) {
            console.info('onProgressAll', progress);
        };
        uploader.onSuccessItem = function(fileItem, response, status, headers) {
            console.info('onSuccessItem', fileItem, response, status, headers);
        };
        uploader.onErrorItem = function(fileItem, response, status, headers) {
            console.info('onErrorItem', fileItem, response, status, headers);
        };
        uploader.onCancelItem = function(fileItem, response, status, headers) {
            console.info('onCancelItem', fileItem, response, status, headers);
        };
        uploader.onCompleteItem = function(fileItem, response, status, headers) {
            console.info('onCompleteItem', fileItem, response, status, headers);
        };
        uploader.onCompleteAll = function() {
            console.info('onCompleteAll');
        };

        console.info('uploader', uploader);
    }]);