jQuery(document).ready(function($){
	$(document).on({
		ajaxStart	: function() { $('#aa_loading').addClass("abap_frontend_loading");},
		ajaxStop	: function() { $('#aa_loading').removeClass("abap_frontend_loading"); }
	});

	$.fn.extend({
		animateCss:	function(animationName,finalState){
			if	(finalState	===	"on"){
				$(this).show();
			}
			var	animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
			this.addClass('animated	' + animationName).one(animationEnd,function(){
				if(finalState === "off"){
					$(this).hide();
				}
				if(finalState === "on"){
					$(this).show();
				}
				$(this).removeClass('animated'+animationName);
			});
		},
		abapChangeScreen: function(html,script){
			var content=$(this);
			$(this).fadeOut(500)
			.queue(function(n){
				$.ajax({
					url: aa_url+html,
					cache: false,
					dataType: "html",
					}).done(function(htmlResponse){
					window.console.log("HTML "+aa_url+html+" cargado");
					content.html(htmlResponse);
	//				content.animateCss('fadeInUpBig',"on");
				});
				$.getScript(aa_url+script)
				.done(function() {
					window.console.log("Script "+aa_url+script+" cargado");
				});
				n();	//next queue item
			}).fadeIn(500);
			
/*		$.ajax({
			method: "GET",
			url: aa_url+script,
			dataType: "script"
		});
*/		},
	});
//	$('#aa_content').abapChangeScreen("html/intro.html","js/client/intro-min.js");
//	$('#aa_content').abapChangeScreen("html/editor.html","js/client/editor-min.js");
//	$('#aa_content').abapChangeScreen("html/analysis_selector.html","js/client/analysis_selector-min.js");
	$('#aa_content').abapChangeScreen("html/analysis_adjust.html","js/client/analysis_adjust.js");
	
	//$('#aa_content').html("test");
/*	$.ajax({
		url: aa_url+"html/intro.html",
		cache: false,
		dataType: "html",
	}).done(function( html ) {
		$('#aa_content').html(html);
	});
	$.ajax({
		method: "GET",
		url: aa_url+"js/client/intro-min.js",
		dataType: "script"
	});
	
	
*/	

});