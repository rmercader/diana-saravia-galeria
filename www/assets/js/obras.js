$(document).ready(function(){

	$("#nombre_artista").autocomplete({
		source: site_url_admin + '/artistas/pornombre/',
		minLength: 3,
    	select: function(event, ui) {
            $("#id_artista").val(ui.item.id);
    		$("#nombre_artista").val(ui.item.value);
    		return false;
    	}
	});

	$("#nombre_categoria_obra").autocomplete({
		source: site_url_admin + "/categorias_obras/pornombre/",
		minLength: 3,
    	select: function(event, ui) {
            $("#id_categoria_obra").val(ui.item.id);
    		$("#nombre_categoria_obra").val(ui.item.value);
    		return false;
    	}
	});

});