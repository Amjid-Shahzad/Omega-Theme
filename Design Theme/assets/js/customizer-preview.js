(function ($) {
    wp.customize('theme_custom_js', function (value) {
        value.bind(function (newCode) {
            removeInjectedScript();
            injectJS(newCode);
        });
    });

    function injectJS(code) {
        const script = document.createElement('script');
        script.type = 'text/javascript';
        script.textContent = code;
        script.dataset.injected = 'true';
        document.body.appendChild(script);
    }

    function removeInjectedScript() {
        document.querySelectorAll('script[data-injected="true"]').forEach(el => el.remove());
    }
})(jQuery);