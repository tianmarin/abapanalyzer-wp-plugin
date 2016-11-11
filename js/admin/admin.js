/*global aaWPADMIN*/
/*global ajaxurl*/
jQuery(document).ready(function($){
var ppost		=aaWPADMIN.ppost;
//window.console.log(ppost);
/*
*
*
*/
//var action;
//if (action !== undefined){
//	window.console.log("action: "+action);
//	
//}
$.fn.extend({
	aaSearchElementList:function(){
		var aaForm =$(this).closest('form');
		var data = new FormData();
		data.append('id', $("input[name='"+ppost+"[id]']").val());
		data.append('action' , aaForm.data('search-action'));
		aaForm.find(':input[type!="hidden"]').not(':input[type=button], :input[type=submit], :input[type=reset]').each(function(){
			data.append($(this).attr("name").replace(ppost,'').replace('[','').replace(']',''),$(this).val());
//			data.append($(this).attr("name") , $(this).val());
//			window.console.log($(this));
//			window.console.log($(this).attr("name")+" : "+$(this).val());

		});
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: data,
			cache: false,
			dataType: 'json',
			processData: false,
			contentType: false,
			beforeSend: function () {
			},
			success: function (response) {
				window.console.log(JSON.stringify(response));
				$('#aa-new-element-list').html('');
				if(response.elementCount>0){
					$.each(response.data,function(i,val){
						var element=$('<div class="list-group-item"></div>');
						var addButton=$('<button type="button" class="close pull-right" aria-label="open" data-element-id="'+val.elementId+'"><span aria-hidden="true">+</span></button>');
						addButton.click(function(){
							$(this).aaAddClassElement();
						});
						element.append(addButton);
						element.append(val.elementTitle);
						element.append(val.elementBody);
						$('#aa-new-element-list').append(element).hide().fadeIn();
					});
				}else{
					window.console.log(JSON.stringify(response));
					var noElement=$('<div class="list-group-item"></div>');
					noElement.append(response.noElementTitle);
					noElement.append(response.noElementBody);
					$('#aa-new-element-list').append(noElement).hide().fadeIn();
				}
			},
		});
	},
	aaGetElementList:function(){
		var aaForm = $(this);
		var data = new FormData();
		data.append('action' , aaForm.data('get-action'));
		data.append('id', $("input[name='"+ppost+"[id]']").val());
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: data,
			cache: false,
			dataType: 'json',
			processData: false,
			contentType: false,
			beforeSend: function () {
			},
	   		success: function(response){
	   			window.console.log(JSON.stringify(response));
		   		$('#aa-element-list').html('');
				if(response.elementCount>0){
					$.each(response.data,function(i,val){
						var element=$('<div class="list-group-item"></div>');
						var addButton=$('<button type="button" class="close pull-right" aria-label="close" data-element-id="'+val.elementId+'"><span aria-hidden="true">&times;</span></button>');
						addButton.click(function(){
							$(this).aaRemoveClassElement();
						});
						element.append(addButton);
						element.append(val.elementTitle);
						element.append(val.elementBody);
						$('#aa-element-list').append(element).hide().fadeIn();
					});
				}else{
					var noElement=$('<div class="list-group-item"></div>');
					noElement.append(response.noElementTitle);
					noElement.append(response.noElementBody);
					$('#aa-element-list').append(noElement).hide().fadeIn();					
				}
	   		},
			error: function(jqXHR, textStatus, errorThrown){
				window.console.log('ERROR: : '+jqXHR+" - "+textStatus+" - "+errorThrown);
				window.console.log("Error. Contacte a su Adminsitrador.");
			}
		});
	},
	aaRemoveClassElement: function(){
		var elementId =$( this ).data('element-id');
		var data = new FormData();
		data.append('action', $('#aa-ajax-wp-filter').data('remove-action'));
		data.append('element_id', elementId);
		data.append('id', $("input[name='"+ppost+"[id]']").val());
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: data,
			cache: false,
			dataType: 'json',
			processData: false,
			contentType: false,
			beforeSend: function () {
			},
	   		success: function(response){
	   			window.console.log(JSON.stringify(response));
				if(response.status==='ok'){
					$('#aa-ajax-wp-filter').aaGetElementList();
				}
	   		},
			error: function(jqXHR, textStatus, errorThrown){
				window.console.log(jqXHR);
				window.console.log('ERROR: :  - '+textStatus+" - "+errorThrown);
			}
		});
	},
	aaAddClassElement: function(){
		var elementId =$( this ).data('element-id');
		var data = new FormData();
		data.append('action', $('#aa-ajax-wp-filter').data('add-action'));
		data.append('element_id', elementId);
		data.append('id', $("input[name='"+ppost+"[id]']").val());
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: data,
			cache: false,
			dataType: 'json',
			processData: false,
			contentType: false,
			beforeSend: function () {
			},
	   		success: function(response){
	   			window.console.log(JSON.stringify(response));
				if(response.status==='ok'){
					$('#aa-ajax-wp-filter').aaGetElementList();
					$('#aa-new-element-list').html('');
				}
	   		},
			error: function(jqXHR, textStatus, errorThrown){
				window.console.log(jqXHR);
				window.console.log('ERROR: :  - '+textStatus+" - "+errorThrown);
			}
		});
	},
});
$('#aa-ajax-wp-filter').submit(function(e){
	e.preventDefault();
});

if($('#aa-ajax-wp-filter').data('get-action') !== undefined){
	$('#aa-ajax-wp-filter').aaGetElementList();
	$('#aa-ajax-wp-filter input[type!="hidden"], #aa-ajax-wp-filter select').each(function(){
		$(this).on("input",function(){
			$(this).aaSearchElementList();
		});
	});
	$('#aa-ajax-wp-filter .aa-sortable').sortable({
		axis: 'y',
		placeholder: "placeholder",
		opacity: 0.8,
		start:function(){
		},
		stop:function(){
			var elementOrder = [];
			$(this).find('.list-group-item button').each(function(){
				elementOrder.push($(this).data('element-id'));
			});
			window.console.log(elementOrder);
			var aaForm =$(this).closest('form');
//			window.console.log(aaForm);
			var data = new FormData();
			data.append('id', $("input[name='"+ppost+"[id]']").val());
			data.append('action' , aaForm.data('update-action'));
			data.append('element_ids' , elementOrder);
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: data,
				cache: false,
				dataType: 'json',
				processData: false,
				contentType: false,
				beforeSend: function () {
				},
				success: function (response) {
					window.console.log(JSON.stringify(response));
				},
				error: function(jqXHR, textStatus, errorThrown){
					window.console.log('ERROR: : '+jqXHR+" - "+textStatus+" - "+errorThrown);
					window.console.log("Error. Contacte a su Adminsitrador.");
				},
			});
	
		},
		change:function(){
		},
		update: function( ) {
		},
	});
}

});
