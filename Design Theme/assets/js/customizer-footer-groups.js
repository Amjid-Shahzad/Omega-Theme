(function($) {
    wp.customize.bind('ready', function() {
        var control = $('#customize-control-footer_template select');
        if (!control.length) return;

        var groupsData = control.data('groups');
        if (!groupsData) return;

        control.empty(); // clear default options

        $.each(groupsData, function(groupLabel, groupChoices) {
            var optgroup = $('<optgroup>').attr('label', groupLabel);
            $.each(groupChoices, function(value, label) {
                optgroup.append($('<option>').val(value).text(label));
            });
            control.append(optgroup);
        });
    });



    // footer refresh css and js files on change
    ( function( $ ) {

	function loadFileContent( templateSlug, callback ) {
		wp.ajax.post( 'theme_get_footer_files', { template: templateSlug } )
			.done( function( response ) {
				if ( response.success ) {
					if ( response.css !== undefined ) {
						wp.customize.instance('footer_custom_css').set( response.css );
					}
					if ( response.js !== undefined ) {
						wp.customize.instance('footer_custom_js').set( response.js );
					}
				}
			})
			.fail( function( err ) {
				console.error('Footer file fetch failed:', err);
			});
	}

	wp.customize( 'footer_template', function( value ) {
		value.bind( function( newTemplate ) {
			if ( newTemplate.startsWith('footer-') ) {
				const slug = newTemplate.replace('footer-', '');
				loadFileContent( slug );
			}
		});
	});

})( jQuery );

})(jQuery);
