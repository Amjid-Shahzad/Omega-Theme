(function($){
    wp.customize.bind('ready', function() {
        const vars = [
            // Colors
            'website-background','page-background','header-background','footer-background',
            'mega-menu-background','color-primary','color-on-primary','color-secondary',
            'color-on-secondary','color-accent','color-on-accent','color-success',
            'color-error','text-primary','text-secondary','text-muted','link',
            'border','divider','shadow','hover','focus',

            // Fonts
            'base-font','heading-font','subheading-font','button-font',

            // Buttons
            'button-background','button-text','button-border-color','button-border-width',
            'button-radius','button-padding-top-buttom','button-padding-right-left',
            'button-background-hover','button-text-hover','button-border-color-hover',
            'button-border-width-hover','button-radius-hover','button-padding-top-buttom-hover',
            'button-padding-right-left-hover'
        ];

        vars.forEach(function(v){
            const type = v.includes('button') ? 'button_' : v.includes('font') ? 'font_' : 'color_';
            wp.customize('theme_' + type + v, function(value){
                value.bind(function(newVal){
                    document.documentElement.style.setProperty('--' + v, newVal);
                });
            });
        });
    });
})(jQuery);
