# Design Theme - AI Coding Instructions

This is a **WordPress block theme** (Gutenberg-focused) with advanced customization features. It's organized around modular systems for dynamic headers, footers, mega-menus, and per-page assets.

## Architecture Overview

### Core Structure
- **`functions.php`**: Entry point requiring all subsystems via `Theme_dir` constant (defined in `inc/theme-setup/paths.php`)
- **`theme.json`**: Block editor settings, color palette, typography, patterns
- **`inc/`**: Modular subsystems (footer, headers, mega-menu, customizer, page-assets, widgets)
- **`custom-blocks/`**: Custom Gutenberg blocks using `@wordpress/scripts` build system
- **`assets/`**: CSS/JS organized by feature (main, site-header, site-footer, woocommerce, per-page scripts)

### Custom Post Types (CPTs)
- **`footer_section`** (`inc/footer/footer-init.php`): Elementor-enabled footer templates
- **`mega_menu`** (`inc/mega-menu/mega-menu-init.php`): Editable mega menu structures with Elementor support
- **`builder_page`** (`inc/theme-setup/custom-post-types.php`): Page builder interface

### Key Modules & Their Responsibilities

| Module | Location | Purpose |
|--------|----------|---------|
| **Customizer** | `inc/customizer/global/` | Global settings (colors, typography, layout) synced to CSS variables |
| **Footer System** | `inc/footer/` | Dynamic footer template selection + per-template CSS/JS |
| **Page Assets** | `inc/page-assets/` | Auto-creates slug-based CSS/JS files; metabox editors for per-page custom code |
| **Mega Menu** | `inc/mega-menu/` | CPT + display logic + metabox styling |
| **Admin** | `inc/admin/` | Dashboard, enqueue controls, fonts, global JS |
| **Widgets** | `inc/widgets/` | Widget area registration + per-widget custom CSS |

## Important Patterns & Conventions

### 1. **Asset Enqueuing**
Use `filemtime()` for cache-busting instead of hardcoded versions:
```php
wp_enqueue_style('theme-main-style', get_template_directory_uri() . '/assets/css/main.css', 
    array(), filemtime(get_template_directory() . '/assets/css/main.css'));
```
**Both frontend and editor** need assets via separate hooks:
- `wp_enqueue_scripts` → Frontend
- `enqueue_block_editor_assets` → Block editor
- `wp_enqueue_scripts` with conditional checks for footer/page/widget CSS

### 2. **Theme Mods & Global Settings**
Settings stored via `get_theme_mod()` / `set_theme_mod()`:
- Primary color: `theme_primary_color` (default: `#0073aa`)
- Secondary color: `theme_secondary_color` (default: `#111`)
- Base font: `theme_font_base-font` (default: `Inter, sans-serif`)
- Heading font: `theme_font_heading-font` (default: `Poppins, sans-serif`)
- Footer template: `footer_template` (slug-based)

Template CSS/JS files loaded dynamically based on these mods in `inc/footer/footer-enqueue.php`.

### 3. **Meta Boxes for Content Control**
- **Page assets** (`page-assets-meta.php`): Adds textarea metaboxes for custom CSS/JS; saves to `assets/css/pages/{slug}.css` and `assets/js/pages/{slug}.js`
- **Mega menu styling** (`mega-menu-metabox.php`): Custom styling per menu item
- Uses `add_meta_box()` hook; saving writes to both post meta AND files

### 4. **Per-Page/Per-Template Automatic File Generation**
When a new page is created (`wp_insert_post` hook), automatically generates:
```
assets/css/pages/{page-slug}.css
assets/js/pages/{page-slug}.js
```
Similarly for footers & mega-menus. Check file existence before enqueuing.

### 5. **Custom Block Categories**
Defined in `custom-blocks/block-categories-list.php` using `block_categories_all` filter:
- **mega-menu**: Blocks used in mega menu CPT
- **footer-blocks**: Blocks for footer sections
- **main-blocks**: General page blocks
- **theme-blocks**: Theme-specific components

### 6. **Customizer Live Preview**
- `customizer_preview_assets()` enqueues `customizer-live.js` + AJAX nonce
- AJAX syncs `theme.json` CSS variables to live preview
- Customizer sections organized under **Global Settings Panel** (priority 5)

### 7. **Early Output & Security Check**
`early-output-check.php` validates theme integrity before rendering. Always require it in `functions.php` near the top.

## Development Workflows

### Building Custom Blocks
```sh
cd custom-blocks
npm install  # Uses @wordpress/scripts
npm start    # Development watch mode (outputs to build/)
npm run build # Production build
```
Then import block in `custom-blocks/src/index.js` and register in `block-categories-list.php`.

### Adding a New Footer Template
1. Create template file: `inc/footer/footer-templates/footer-{slug}.php`
2. Create CSS: `inc/footer/footer-templates/footer-{slug}.css`
3. Create JS: `inc/footer/footer-templates/footer-{slug}.js` (optional)
4. Register in footer customizer settings
5. CSS/JS auto-enqueued by `footer-enqueue.php` on `get_theme_mod('footer_template')`

### Adding Global Customizer Settings
1. Create file in `inc/customizer/global/global-{feature}.php`
2. Register sections/controls using `$wp_customize->add_section()` and `add_control()`
3. Require file in `global-init.php` and assign section to `global_settings_panel`
4. Use `get_theme_mod()` in frontend/editor code

### Debugging
- Check `early-output-check.php` for theme integrity issues
- Verify `Theme_dir` constant set correctly in `paths.php`
- Use `filemtime()` timestamps to detect stale assets
- Block editor CSS/JS must enqueue separately from frontend

## Key File References

- **Paths & Setup**: `inc/theme-setup/paths.php`, `inc/theme-setup/theme-options.php`
- **Main Enqueue**: `inc/theme-setup/enqueue-scripts.php`
- **Template Switching**: `inc/footer/footer-enqueue.php`, `inc/page-assets/page-assets-enqueue.php`
- **Customizer Hub**: `inc/customizer/global/global-init.php`
- **Block Setup**: `custom-blocks/block-categories-list.php`, `custom-blocks/src/index.js`

## Naming Conventions

- **CPT slugs**: lowercase, hyphen-separated (`footer_section`, `mega_menu`, `builder_page`)
- **Theme mods**: `theme_{feature}_{setting}` (e.g., `theme_primary_color`)
- **CSS/JS handles**: `{feature}-{type}` (e.g., `footer-classic-css`, `theme-main-js`)
- **Meta keys**: `_{post-type}_{field}` (e.g., `_page_css`, `_page_js`)
- **Block slugs**: lowercase (e.g., `mega-menu`, `footer-blocks`)

## When Modifying Key Components

**Customizer Settings**: Update `inc/customizer/global/` files, then reload customizer in admin. Settings sync via AJAX in preview.

**Footer/Page/Widget CSS**: Modify template files directly; they're auto-enqueued. Use `get_theme_mod()` fallbacks for dynamic selection.

**Page Assets**: Page metaboxes save to disk files. Don't edit files directly—use metabox textareas for persistence.

**Custom Blocks**: After editing `custom-blocks/src/index.js`, rebuild with `npm run build`.
