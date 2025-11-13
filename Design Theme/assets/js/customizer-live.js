(function () {
  "use strict";

  // Run after DOM is ready
  function onReady(fn) {
    if (document.readyState !== "loading") fn();
    else document.addEventListener("DOMContentLoaded", fn);
  }

  onReady(function () {
    // Ensure Customizer preview API exists
    if (!window.wp || !wp.customize) {
      console.error("❌ wp.customize not available in preview.");
      return;
    }

    wp.customize.bind("preview-ready", function () {
      console.log("✅ Customizer live preview connected.");

      // Tiny helpers
      const $$ = (sel) => Array.from(document.querySelectorAll(sel));
      const set = (sel, prop, val) =>
        $$(sel).forEach((el) => el.style.setProperty(prop, String(val), "important"));
      const px = (v) =>
        v === "" || v == null ? "" : /px|rem|em|%$/.test(String(v)) ? String(v) : String(v) + "px";

      const primarySel =
        "button, .button, input[type='submit'], .wp-block-button__link";
      const secondarySel = ".secondary-button";

      // ---- PRIMARY BUTTON ----
      wp.customize("theme_button_primary_bg",   (v) => v.bind((n) => set(primarySel,  "background-color", n)));
      wp.customize("theme_button_primary_text", (v) => v.bind((n) => set(primarySel,  "color",            n)));
      wp.customize("theme_button_primary_border_color", (v) => v.bind((n) => set(primarySel, "border-color", n)));
      wp.customize("theme_button_primary_border_width", (v) => v.bind((n) => set(primarySel, "border-width", px(n))));
      wp.customize("theme_button_primary_radius",       (v) => v.bind((n) => set(primarySel, "border-radius", px(n))));
      wp.customize("theme_button_primary_padding",      (v) => v.bind((n) => set(primarySel, "padding",       n)));
      wp.customize("theme_button_primary_font_size",    (v) => v.bind((n) => set(primarySel, "font-size",     px(n))));

      // ---- SECONDARY BUTTON ----
      wp.customize("theme_button_secondary_bg",          (v) => v.bind((n) => set(secondarySel, "background-color", n)));
      wp.customize("theme_button_secondary_text",        (v) => v.bind((n) => set(secondarySel, "color",            n)));
      wp.customize("theme_button_secondary_border_color",(v) => v.bind((n) => set(secondarySel, "border-color",     n)));
      wp.customize("theme_button_secondary_border_width",(v) => v.bind((n) => set(secondarySel, "border-width",     px(n))));
      wp.customize("theme_button_secondary_radius",      (v) => v.bind((n) => set(secondarySel, "border-radius",     px(n))));
      wp.customize("theme_button_secondary_padding",     (v) => v.bind((n) => set(secondarySel, "padding",           n)));
      wp.customize("theme_button_secondary_font_size",   (v) => v.bind((n) => set(secondarySel, "font-size",         px(n))));
    });
  });
})();

console.log('Customizer Live Preview script loaded.');



(function ($) {
  wp.customize.bind('preview-ready', function () {

    console.log('✅ Customizer live preview active for Design Theme.');

    // === 🎨 Handle Color Variables ===
    const colorSettings = [
      'website-background',
      'page-background',
      'header-background',
      'footer-background',
      'mega-menu-background',
      'color-primary',
      'color-on-primary',
      'color-secondary',
      'color-on-secondary',
      'color-accent',
      'color-on-accent',
      'color-success',
      'color-error',
      'text-primary',
      'text-secondary',
      'text-muted',
      'link',
      'border',
      'divider',
      'shadow',
      'hover',
      'focus'
    ];

    colorSettings.forEach(slug => {
      wp.customize(`theme_color_${slug}`, function (value) {
        value.bind(function (newVal) {
          document.documentElement.style.setProperty(`--${slug}`, newVal);
        });
      });
    });

    // === 🔤 Handle Font Variables ===
    const fontSettings = [
      'base-font',
      'heading-font',
      'subheading-font',
      'button-font'
    ];

    fontSettings.forEach(slug => {
      wp.customize(`theme_font_${slug}`, function (value) {
        value.bind(function (newVal) {
          document.documentElement.style.setProperty(`--${slug}`, newVal);
        });
      });
    });

    // === 🔘 Handle Button Variables ===
    const buttonSettings = [
      'button-background',
      'button-text',
      'button-border-color',
      'button-border-width',
      'button-radius',
      'button-padding-top-buttom',
      'button-padding-right-left',
      'button-background-hover',
      'button-text-hover',
      'button-border-color-hover',
      'button-border-width-hover',
      'button-radius-hover',
      'button-padding-top-buttom-hover',
      'button-padding-right-left-hover'
    ];

    buttonSettings.forEach(slug => {
      wp.customize(`theme_button_${slug}`, function (value) {
        value.bind(function (newVal) {
          document.documentElement.style.setProperty(`--${slug}`, newVal);
        });
      });
    });

  });
})(jQuery);

