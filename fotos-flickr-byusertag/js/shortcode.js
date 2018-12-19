(function($) {
	tinymce.create('tinymce.plugins.flickr_by_tag_plugin', {
        init: function(ed, url) {
            ed.addCommand('flickr_by_tag_insertar_shortcode', function() {
                selected = tinyMCE.activeEditor.selection.getContent();
                var content = '';

                ed.windowManager.open({
					title: 'Listado fotos de Flickr',
					body: [{
						type: 'textbox',
						name: 'usuario',
						label: 'Id de Usuario'
					},{
						type: 'textbox',
						name: 'tag',
						label: 'Etiqueta'
					},{
						type: 'textbox',
						name: 'cant',
						label: 'Cantidad'
					}],
					onsubmit: function(e) {
						ed.insertContent( '[muestra_fotos usuario="' + e.data.usuario + '" tag="' + e.data.tag + '" cant="' + e.data.cant + '"]' );
					}
				});
                tinymce.execCommand('mceInsertContent', false, content);
            });
            ed.addButton('flickr_button', {title : 'Insertar fotos de Flickr', cmd : 'flickr_by_tag_insertar_shortcode', image: url.replace('/js', '') + '/images/logo-shortcode.png' });
        },   
    });
    tinymce.PluginManager.add('flickr_button', tinymce.plugins.flickr_by_tag_plugin);
})(jQuery);

boton_shortcode_flickr_by_tag