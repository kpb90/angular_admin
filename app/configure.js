var appConfig = angular.module('appConfig', []);
appConfig.constant('configure', {
	'pump':{
		'types': [{'name':'id','type' : 'id', 'visible' : false, 'header' : 'ID', 'required':true},
				{'name':'title_pump','type' : 'string', 'visible' : true, 'header' : 'Название насоса', 'required':true},
				{'name':'seria_pump','type' : 'combobox', 'store':'seria_pump', 'visible' : true, 'header' : 'Серия', 'required':true},
				{'name':'model_pump','type' : 'string', 'visible' : true, 'header' : 'Модель насоса', 'required':true},
				{'name':'work_run_dry','type' : 'string', 'visible' : false, 'header' : 'Работа по сухому ходу до (мин)', 'required':true},
	         	{'name':'solids_concentration','type' : 'string', 'visible' : false, 'header' : 'Объемная концетрация твердых частиц (%)', 'required':true},
	         	{'name':'size_of_solid','type' : 'string', 'visible' : false, 'header' : 'Размер твердых частиц до (мм)', 'required':true},
	         	{'name':'productivity','type' : 'string', 'visible' : false, 'header' : 'Производительность до (м3/ч)', 'required':true},
	         	{'name':'delivery_height','type' : 'string', 'visible' : false, 'header' : 'Высота подачи до (м)', 'required':true},
	         	{'name':'viscosity','type' : 'string', 'visible' : false, 'header' : 'Вязкость до (мПа*с)', 'required':true},
	         	{'name':'density','type' : 'combobox', 'store':'density', 'visible' : true, 'header' : 'Плотность,  г/см3 до от', 'required':true},
	         	{'name':'engine','type' : 'string', 'visible' : true, 'header' : 'Двигатель', 'required':true},
	         	{'name':'housing_material','type' : 'string', 'visible' : true, 'header' : 'Материал корпуса', 'required':true},
	         	{'name':'fluid_temperature','type' : 'string', 'visible' : true, 'header' : 'Температура жидкости', 'required':true},
	         	{'name':'ambient_temperature','type' : 'string', 'visible' : true, 'header' : 'Температура окружающей среды', 'required':true},
	         	{'name':'shaft','type' : 'string', 'visible' : false, 'header' : 'Вал', 'required':true},
	         	{'name':'o_ring','type' : 'string', 'visible' : false, 'header' : 'Уплотнительное кольцо', 'required':true},
	         	{'name':'support_sleeve','type' : 'string', 'visible' : false, 'header' : 'Опорная втулка', 'required':true},
	         	{'name':'self_priming','type' : 'combobox', 'store':'bool', 'visible' : true, 'header' : 'Самовсасывание', 'required':true}],
		//'toolbar': [{'title':'Новости','handler':'news', 'action_template':'templates/adiction_table.html'}],
		'dialog_title':'Насосы',
		'dialog_add_button_template':'templates/add_button.html',
		//'form_template':'templates/form_news.html',
		'limit':10
	},
	'chart':{
		'types': [{'name':'id','type' : 'id', 'visible' : false, 'header' : 'ID', 'required':true},
				{'name':'pict','type' : 'pict', 'visible' : true, 'header' : 'Картинка', 'required':false},
				{'name':'seria_pump','type' : 'combobox', 'store':'seria_pump', 'visible' : true, 'header' : 'Серия насоса', 'required':false},
				{'name':'model_pump','type' : 'string', 'visible' : true, 'header' : 'Модель насоса', 'required':false},
				{'name':'coord','type' : 'string', 'visible' : true, 'header' : 'Координаты (1,2; 2,3; 3,1)', 'required':false},
				{'name':'priority','type' : 'string', 'visible' : true, 'header' : 'Приоритет', 'required':false}],
		'toolbar': [{'title':'Картинки','handler':'news', 'action_template':'templates/multi_upload.html'}],
		'dialog_title':'Графики',
		//'form_template':'templates/form_news.html',
		'limit':10
	}
});

appConfig.constant('Top_menu', 
	[{'title':'Насосы','href':'#/pump'},
	 {'title':'Графики','href':'#/chart'},
	 {'title':'Excel','href':'#/catalog'}]
);