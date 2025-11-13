( function ( wp ) {
    const { select, subscribe } = wp.data;
    const domReady = wp.domReady;

    // Debounce helper (simple)
    function debounce(fn, delay) {
        let t;
        return function () {
            const args = arguments;
            clearTimeout(t);
            t = setTimeout(() => fn.apply(this, args), delay);
        };
    }

    // Find the editor content wrapper where block content is rendered
    function getEditorContentWrapper() {
        // Gutenberg content area wrapper
        // .editor-styles-wrapper is typically present and wraps the editor content area
        return document.querySelector('.editor-styles-wrapper') || document.querySelector('#editor');
    }

    // Create or return a single <style> tag
    function ensureStyleTag() {
        const wrapper = getEditorContentWrapper();
        if (!wrapper) return null;
        let tag = wrapper.querySelector('#page-dynamic-style');
        if (!tag) {
            tag = document.createElement('style');
            tag.id = 'page-dynamic-style';
            wrapper.appendChild(tag);
        }
        return tag;
    }

    // Create or return a single <script> tag (for live execution in editor)
    function ensureScriptTag() {
        const wrapper = getEditorContentWrapper();
        if (!wrapper) return null;
        let tag = wrapper.querySelector('#page-dynamic-script');
        if (!tag) {
            tag = document.createElement('script');
            tag.id = 'page-dynamic-script';
            tag.type = 'text/javascript';
            // Use 'data-editor-injected' attribute to identify it's an injected live script
            tag.setAttribute('data-editor-injected', '1');
            wrapper.appendChild(tag);
        }
        return tag;
    }

    // Update style tag contents (replace whole text)
    function updateStyleContent(css) {
        const tag = ensureStyleTag();
        if (!tag) return;
        tag.textContent = css || '';
    }

    // Update script tag: to re-execute code, replace the node with a new one
    function updateScriptContent(js) {
        const wrapper = getEditorContentWrapper();
        if (!wrapper) return;
        const old = wrapper.querySelector('#page-dynamic-script');
        if (old) {
            old.parentNode.removeChild(old);
        }
        if (!js || js.trim() === '') {
            return;
        }
        const tag = document.createElement('script');
        tag.id = 'page-dynamic-script';
        tag.type = 'text/javascript';
        tag.setAttribute('data-editor-injected', '1');
        try {
            tag.textContent = js;
        } catch (e) {
            // fallback for exotic browser behavior
            tag.appendChild(document.createTextNode(js));
        }
        wrapper.appendChild(tag);
    }

    // Initialize and subscribe to editor state
    domReady( () => {
        let prevCSS = null;
        let prevJS = null;

        // Update function (debounced for typing)
        const runUpdate = debounce(() => {
            const meta = select('core/editor').getEditedPostAttribute('meta') || {};
            const css = typeof meta.page_custom_css === 'string' ? meta.page_custom_css : '';
            const js  = typeof meta.page_custom_js === 'string' ? meta.page_custom_js : '';

            if ( css !== prevCSS ) {
                updateStyleContent( css );
                prevCSS = css;
            }

            if ( js !== prevJS ) {
                // Remove old script and inject new one to re-execute
                updateScriptContent( js );
                prevJS = js;
            }
        }, 220 ); // 220ms debounce for smooth typing

        // Run once on load to inject saved values
        runUpdate();

        // Subscribe to editor changes
        subscribe( () => {
            // We only need to call runUpdate; compare happens inside runUpdate
            runUpdate();
        } );
    } );
} )( window.wp );
console.log("Hello I am editor-live-preview.js");