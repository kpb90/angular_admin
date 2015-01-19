Ext.onReady(function(){
						
	var selectionModel = new Ext.tree.DefaultSelectionModel();
	selectionModel.on('selectionchange', function(model, node){	
		if(node) {
			var id = node.id;
			if(id != '0') {
				Ext.Ajax.request({ 		
					url:'pages/controller.php', 
					params:{'task':'getPageInfo', 'id':id}, 					 
					method:'POST',										 
					callback: function (options, success, response) {			 				 					
						var json = Ext.util.JSON.decode(response.responseText);
						Ext.getCmp('name-text').setValue(json.title);
						Ext.getCmp('filename-text').setValue('/' + json.file_name + '.html');
						Ext.getCmp('editor').setValue(json.content);						
						Ext.getCmp('delete-page-btn').enable();
						Ext.getCmp('save-page-btn').enable();		
					}, 
					failure:function(response,options){
						showErrorMessage('Ошибка связи с сервером');
					}	
				});	
			} else {
				disableAll();
			}	
		} else {
			disableAll();
		}	
	})
		
	function disableAll() {
		Ext.getCmp('name-text').setValue('');
		Ext.getCmp('filename-text').setValue('');
		Ext.getCmp('editor').setValue('');		
		Ext.getCmp('delete-page-btn').disable();		
		Ext.getCmp('save-page-btn').disable();					
	}
	
	var tree = new Ext.tree.TreePanel({   
	    id:'tree-panel',
	    autoHeight: false,
	    bodyStyle:'padding:5px',
	    useArrows: false,
	    autoScroll: true,
	    animate: true,
	    preloadChildren:false,
	    enableDD: false,
	    containerScroll: true,
	    border: true,
	    height: tableHeight,
	    rootVisible: true,
	    selModel: selectionModel,    
	    dataUrl:'pages/controller.php?task=getTree',
	     root: {
	        text: 'Страницы сайта',
	        draggable: false,      
	        id: '0'
	    },
	    tbar:[{
	    	id:'add-page-btn',
	    	text:'Добавить страницу',
			icon: 'images/new.gif',
			cls:"x-btn-text-icon",     	
	    	handler: addPage 
	    }, '-', {
	    	id:'delete-page-btn',
	    	text:'Удалить',
			icon: 'images/delete.gif',
			cls:"x-btn-text-icon",     	
	    	handler: deletePage     	
	    }, '-']     
	});
	
	

	

	var emtyPanel = new Ext.Panel({
		border: false,
		width: 5,
		html:'&nbsp;'	
	})
	
	var pagePanel = new Ext.Panel({
		bodyStyle:'background-color:white',
		border: false,
		height: tableHeight,
		layout:'column',
		renderTo: 'code',
		items:[{
			layout: 'form',
			border: false,
			items: tree,
			bodyStyle:'margin-right:10px',
			height: tableHeight,
			columnWidth: .3
		},{
			layout: 'form',
			columnWidth: .01,
			border: false,
			items: emtyPanel		
		}, {
			layout: 'form',
			border: false,
			items: [{
				bodyStyle:'background-color:white;padding:5px 5px 5px 10px',
				layout: 'form',
				labelAlign : 'top',
				border: true,
				height: tableHeight,
				items: [{
					fieldLabel: 'Название',	
					xtype:'textfield',
					id:'name-text',		
					width:300
				}, {
					fieldLabel: 'Адрес',	
					id:'filename-text',
					xtype:'textfield',
					width:300
				}, {
					id: 'page_editor',
  					height: tableHeight * 0.8,
  					width: (tableWidth - 20) * 0.95 - (tableWidth - 20) * 0.3,
					fieldLabel: 'Текст',
					xtype:'ckeditor',
					id:'editor',
					CKConfig: { 							
						toolbar:[ 
							['Source','-','Templates'],
							['Cut','Copy','Paste','PasteText','PasteFromWord'],
							['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],							
							['NumberedList','BulletedList','-','Outdent','Indent', 'Link','Unlink','Anchor'],																
							['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar'],
							['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'TextColor','BGColor', 'ShowBlocks'],								
							['Bold','Italic','Underline','Strike','-','Subscript','Superscript','Styles','Format','Font','FontSize']																								
						],
						enterMode: CKEDITOR.ENTER_BR,
						shiftEnterMode : CKEDITOR.ENTER_P  
					}	
				}],
				tbar: [{
					text:'Сохранить',					
					handler: savePage,
					id:'save-page-btn',
					icon: 'images/save.gif',
					cls:"x-btn-text-icon"     	     	
				}, '-']					
			}],
			columnWidth: .69
				
		}] 
	});
	
	tree.getRootNode().expand(false, false, selectAfterLoad);
	setSelectedMenu(0);
	
	function savePage() {
		var title = Ext.getCmp('name-text').getValue();
		var content = Ext.getCmp('editor').getValue();
		var filename = Ext.getCmp('filename-text').getValue();
		var node = selectionModel.getSelectedNode();
		if(node) {
			var id = node.id;
			if(id != '0') {
				Ext.Ajax.request({ 		
					url:'pages/controller.php', 
					params:{'task':'savePageInfo', 'id':id, 'filename':filename, 'title':title, 'text': content}, 					 
					method:'POST',										 
					callback: function (options, success, response) {			 				 						
						var json = Ext.util.JSON.decode(response.responseText);
						if(json.result == '1') {
							//node.setText(title);
						}
					}, 
					failure:function(response,options){
						showErrorMessage('Ошибка связи с сервером');
					}	
				});	
			} 
		}	
	}
	
	function selectAfterLoad() {
		var root = tree.getRootNode();
		if(root.hasChildNodes()) {
			var node = root.firstChild;
			node.select();
		}
		
	}
	
	function addPage() {
		var node = selectionModel.getSelectedNode();
		var parent = node.id;
		Ext.Msg.prompt('Новая страница', 'Введите название новой страницы', function(buttonId, text) {		
			if(buttonId == 'ok') {
				if(text) {
					Ext.Ajax.request({ 		
						url:'pages/controller.php', 
						params:{'task':'addPage', 'parent':parent, 'title':text}, 					 
						method:'POST',										 
						callback: function (options, success, response) {			 				 						
							var json = Ext.util.JSON.decode(response.responseText);
							if(json.result == '1') {
								var id = json.id;
								var newNode = new Ext.tree.TreeNode({
									id: id,
									leaf: true,
									text: text
								})
															
								node.appendChild(newNode); 
								node.leaf = false;
								tree.expandPath(newNode.getPath());
								newNode.select();
							}
						}, 
						failure:function(response,options){
							showErrorMessage('Ошибка связи с сервером');
						}	
					});	
					
				} else {
					Ext.Msg.alert('Ошибка', 'Не введено название страницы');
				}
			}
		});
	}
	
	function deletePage() {
		var delNode = selectionModel.getSelectedNode();		
		Ext.Msg.confirm('Удаление страницы', 'Вы действительно хотите удалить страницу: '+ delNode.text +'?', 
			function(btn){
				if(btn == 'yes'){
					Ext.Ajax.request({ 		
						url:'pages/controller.php', 
						params:{'task':'deletePage', 'id':delNode.id}, 					 
						method:'POST',										 
						callback: function (options, success, response) {							
							var parent = delNode.parentNode;
							parent.removeChild(delNode, true);
							if(parent.childNodes.length == 0) {
								parent.leaf = true;
							}
							tree.expandPath(parent.getPath());
							parent.select();
						}
					})				
				}
			}
		);
		
	}
})
  	