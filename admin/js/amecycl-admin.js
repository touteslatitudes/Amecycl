jQuery(document).ready(function($){
	'use strict';
	// script utilise pour la partie admin du plugin amecycl
	
	function GetURLParameter(sParam)
	{
		var sPageURL = window.location.search.substring(1);
		var sURLVariables = sPageURL.split('&');
		for (var i = 0; i < sURLVariables.length; i++)
		{
			var sParameterName = sURLVariables[i].split('=');
			if (sParameterName[0] == sParam)
			{
				return sParameterName[1];
			}
		}
	}

	// utilisation d'onglets (tabs) et conservation du dernier onglet selectionne
	$("#tabs").tabs({ active: 0 });
	$("#tabs").tabs("option", "active", $("#acy-lasttab").val());

    $('.submit').click(function(e) {
		var selectedTab = $("#tabs").tabs('option', 'active');
        $("#acy-lasttab").val(selectedTab);
    });

	// utilisation de tooltips
	$( "div.wrap label" ).tooltip();
	$( "div.wrap input" ).tooltip();
	$( "div.wrap select" ).tooltip();

	// fenetre msg
	$( "#dialog-msg" ).dialog({
		resizable: false,
		height: 100,
		width: 400,
		position: {my: "top+50", at: "top", of: window }
	});

	// fenetre de confirmation
	$( "#dialog-confirm" ).dialog({
		resizable: false,
		height: 180,
		width: 400,
		modal: true,
		buttons: {
			"Supprimer": function() {
				$( this ).dialog( "close" );
				var action = GetURLParameter('action');
				if (action == 'delete') {
					var pathname = location.pathname;
					var page = GetURLParameter('page');
					if (page == 'region-admin') {
						var region = GetURLParameter('region');

						$.ajax({
							type : 'post',
							url : acyvar.url + 'wp-admin/admin-ajax.php',
							data : { action: 'delete-region', rid: region },	
							dataType: 'json',
							success: function(response) {
								if(response.status) {
									var newurl = pathname+'?page='+page;
									location.assign(newurl);
								}
								else {
									alert(response.msg);
								}
							},
							statusCode: {
								404: function() {
									alert( "page not found" );
								}
							}
						});
					}
					else if (page == 'setting-admin') {
						var setting = GetURLParameter('setting');

						$.ajax({
							type : 'post',
							url : acyvar.url + 'wp-admin/admin-ajax.php',
							data : { action: 'delete-setting', sid: setting },	
							dataType: 'json',
							success: function(response) {
								if(response.status) {
									var newurl = pathname+'?page='+page;
									location.assign(newurl);
								}
								else {
									alert(response.msg);
								}
							},
							statusCode: {
								404: function() {
									alert( "page not found" );
								}
							}
						});
						
					}
				}
			},
			"Annuler": function() {
				$( this ).dialog( "close" );
			}
		}
	});

    $('.selectfile').click(function(){
		$('#fileinput').click();
    });

	$('#fileinput').change(function() {
		var filename = $('#fileinput')[0].files[0].name;
		$('#selected_filename').val(filename);		
	});

	$('.upload').on('click', function() {
        var file_data = $('#fileinput').prop('files')[0];
		var region = $('#edited_region')[0].value;
		
        if(file_data != undefined) {
            var form_data = new FormData();                  
            form_data.append('file', file_data);
            form_data.append('edited_region', region);
            $.ajax({
                type: 'POST',
                url: acyvar.url + 'wp-admin/admin-ajax.php?action=upload-region-file',
                contentType: false,
                processData: false,
                data: form_data,
				dataType: 'json',
                success:function(response) {
					if(response.status) {
						$('#acy-filename').innerHTML = response.options;
                        alert('Fichier téléchargé avec succès');
                    } else {
                        alert('Erreur de téléchargement.');
                    }
					
                    $('#fileinput').val('');
                    $('#selected_filename').val('');
                }
                
			});
        }
        return false;
    });
		
});