function showText() {
	var grid = Ext.getCmp('news-list');
	var record = grid.getSelectionModel().getSelected();
	if(record) {
		textDialog.setTitle('Редактирование новости');
		text_controller = 'news/controller.php';
		text_task = 'saveText';
		var id = record.get('id');
		Ext.Ajax.request({ 		
				url:'news/controller.php', 
				params:{'task':'getText', 'id':id}, 					 
				method:'POST',										 
				callback: function (options, success, response) {			 				 	
					Ext.getCmp('shops_text_editor').setValue(response.responseText);
					Ext.getCmp('text-id').setValue(id);	
					textDialog.show();				
				 }, 
			 	failure:function(response,options){
					showErrorMessage('Ошибка связи с сервером');
				}	
			});						
	}
}
