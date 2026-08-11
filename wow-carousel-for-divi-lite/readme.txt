=== Divi Carousel – Image, Logo & Video Carousel for Divi Theme ===

Contributors: plugpressco, badhonrocks, divipeople
Tags: divi, divi theme, carousel, image carousel, logo carousel
Requires at least: 5.0
Tested up to: 7.0
Stable tag: 3.2.1
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Free carousel modules for the Divi Theme and Divi Builder. Build a Divi image carousel, logo carousel, video carousel or slider in minutes.

== Description ==

The Divi Theme ships with one slider. This plugin adds four carousel modules to the Divi Builder, so you can build sliders Divi cannot make on its own — without touching code.

Works on **Divi 4 and Divi 5**, in the Divi Theme, Extra Theme, or the Divi Builder plugin.

[**View the live demo**](https://divipeople.com/free-plugins/divi-carousel-free/demo/) | [**Divi Carousel Pro**](https://divipeople.com/divi-carousel-pro/)

= Four free Divi carousel modules =

**Divi Image Carousel**
Turn any set of images into a swipeable slider. Captions, custom links, hover effects and a built-in lightbox. Good for portfolios, galleries and product shots.

**Divi Logo Carousel**
A client wall that scrolls. Each logo keeps its own width, links where you want, and can fade from grayscale to colour on hover.

**Divi Video Carousel**
YouTube, Vimeo and self-hosted video in a slider, with lightbox playback and your own poster images.

**Divi Nested Carousel**
Put any Divi module inside a slide. Build the slide you actually want instead of filling in a fixed layout.

= What you get =

* **Quick Start presets** — pick a starting point, then adjust anything you like.
* **Linked carousels** — connect two carousels so one drives the other. That is how you build a thumbnail gallery.
* **Slide Ratio** — crop every slide to the same shape so mixed portrait and landscape images stop stretching the row.
* **Responsive by slide** — different slide counts, spacing and controls for desktop, tablet and phone.
* **Real accessibility** — keyboard-operable arrows, screen-reader labels in your language, a pause control for autoplay, and reduced-motion support.
* **Right to Left** — genuine RTL, including drag direction and keyboard order.
* **Loads nothing on pages without a carousel** — no weight added to the rest of your site.

= Built for the Divi Builder =

Every module uses the same Content / Design / Advanced tabs you already know. No new interface to learn, and no shortcodes to remember.

A built-in Module Manager lets you switch off any module you are not using, so the Divi Builder list stays short.

= Upgrade to Divi Carousel Pro =

[**Divi Carousel Pro**](https://divipeople.com/divi-carousel-pro/) adds eight more modules — Content, Card, Post, Product, Team, Testimonial, Google Reviews and Instagram Feed — plus a Dynamic Loop carousel that builds slides from any WordPress query, filtering, and WooCommerce quick view.

Your free carousels keep working exactly as they are when you upgrade.

== Installation ==

= Requirements =

* WordPress 5.0 or higher (6.0+ recommended)
* PHP 7.4 or higher (8.0+ recommended)
* Divi Theme, Extra Theme, or the Divi Builder plugin by Elegant Themes

This plugin extends the Divi Builder. It will not do anything on other WordPress themes.

= Install from WordPress =

1. Go to **Plugins → Add New**.
2. Search for **Divi Carousel**.
3. Click **Install Now**, then **Activate**.
4. Edit a page with the Divi Builder and search the module list for "Carousel".

= Install manually =

1. Download the zip from WordPress.org.
2. Go to **Plugins → Add New → Upload Plugin**.
3. Choose the zip, click **Install Now**, then **Activate**.

== Frequently Asked Questions ==

= Do I need the Divi Theme? =

You need the Divi Builder, which comes with the Divi Theme, the Extra Theme, or the standalone Divi Builder plugin. Any of the three works.

= Does it work with Divi 5? =

Yes. The same modules work on Divi 4 and Divi 5, and the right assets load automatically depending on which one you are running.

= Which modules are free? =

Four: Divi Image Carousel, Divi Logo Carousel, Divi Video Carousel and Divi Nested Carousel. All of them include autoplay, looping, arrows, dots, swipe and full responsive control.

= Is it really free? =

Yes. No trial, no locked features, no nag screens. Divi Carousel Pro is optional.

= What does Divi Carousel Pro add? =

Eight more modules — Content, Card, Post, Product, Team, Testimonial, Google Reviews and Instagram Feed — plus a Dynamic Loop carousel driven by a WordPress query, front-end filtering, and WooCommerce quick view.

= Will my carousels break if I upgrade to Pro? =

No. Every free module exists in Pro too, with the same settings.

= How do I build a thumbnail gallery? =

Add two carousels. Give both the same name under **Linked Carousels**, set one to Main Carousel and the other to Thumbnail Navigation. Clicking a thumbnail moves the main carousel, and the thumbnails work from the keyboard too.

= Can I change how many slides show at once? =

Yes, per device. Set **Slides To Show** separately for desktop, tablet and phone.

= Can I turn off the arrows or dots? =

Yes, and per device. **Navigation & Pagination** can show arrows and dots on desktop, dots only on tablet, and nothing on phone.

= Does autoplay pause? =

Yes. Autoplay stops when a visitor interacts with the carousel, and a pause button appears whenever autoplay is on. Autoplay is off by default, because motion nobody asked for is an accessibility problem.

= Will it slow my site down? =

No. Carousel CSS and JavaScript only load on pages that actually contain a carousel.

= Does it support RTL languages? =

Yes, properly. Arabic, Hebrew, Persian and other RTL languages get the correct drag direction, arrow order and keyboard order — not just mirrored text.

= Where do I get help? =

Free support is on the [WordPress.org support forum](https://wordpress.org/support/plugin/wow-carousel-for-divi-lite/). Pro customers get priority email support.

== Screenshots ==

1. Divi Image Carousel with navigation controls and lightbox
2. Divi Logo Carousel showing client brands in a continuous slider
3. Module settings inside the Divi Builder
4. Responsive carousel layout on mobile
5. Plugin dashboard and module manager

== Changelog ==

= 3.2.1 =

Fixed
* Image Carousel slides showed the text "ETmodules" instead of the image on Divi 5. The 3.2.0 responsive-image work rewired the slide markup and the `<img>` tag was dropped from it, so every published slide printed the icon font name where the picture should have been. Divi 4 sites and the Visual Builder preview were unaffected, which is why it was not caught before release.
* Image Carousel lightbox did nothing on Divi 5. It opens from the image's `data-mfp-src`, and that attribute went missing with the tag above.
* Image Carousel image design options — border, border radius, box shadow, filters and spacing — had no effect on the published page on Divi 5. They target the slide image's class, and no element carried it.

Image Carousel slides on Divi 5 now also get the responsive `srcset`, lazy loading and intrinsic width and height that 3.2.0 introduced for the other modules.

= 3.2.0 =

**Please read the behaviour changes before updating.**

Behaviour changes
* Vertical, centred and auto-height carousels may render slightly differently. The modules each carried their own copy of the Swiper configuration builder and those copies had drifted apart; they now share one implementation, so Logo and Video Carousels pick up fixes Image Carousel already had.
* Navigation arrows and pagination dots are now real buttons instead of divs, so they are focusable and announced correctly. If you styled them with CSS targeting `div.swiper-button-next`, update the selector.
* Autoplay is now off by default, and stops when a visitor interacts with a carousel. Where autoplay is on, a pause/play control appears with it.
* The Video Carousel no longer shows Ticker settings. It never had a ticker renderer — selecting Ticker did nothing. Image and Logo Carousels are unaffected.

Added
* **Nested Carousel** — place any Divi module inside a carousel slide, so you can build the slide you actually want instead of being limited to a fixed layout.
* **Quick Start presets** — a preset picker in the module settings that applies a ready-made configuration (layout, motion, navigation, density and RTL variants). Every setting it applies stays editable afterwards.
* **Linked Carousels** — give two carousels the same link group to connect them. A carousel set to Thumbnail Navigation drives the main one and highlights the current slide; two main carousels move together. This brings back the Divi 4 thumbnail gallery, and unlike the Divi 4 version the thumbnails are operable from the keyboard.
* **Slide Ratio** — crop slide images to one shape (square, 4:3, 3:2, 16:9 or portrait) so every slide is the same height. Mixed portrait and landscape images otherwise stretch the carousel to the tallest one.
* **Slide Width: Fit Content** — let each slide size itself instead of dividing the track evenly. Useful for logo walls.
* **Transition Easing** and **Dot Alignment**, two Divi 4 controls the Divi 5 port had dropped.
* **Navigation & Pagination is now per-device** — show arrows and dots on desktop, dots only on tablet, and nothing on phone where swiping is the obvious gesture.
* Accessibility: reduced-motion support, translated screen-reader announcements, and a labelled carousel region.
* Carousel Maker layouts can now be converted to Divi 5 — see the upgrade note below.
* A Roadmap screen in the plugin dashboard, where you can tell us which carousel to build next.

Fixed
* Image Lightbox did nothing in Divi 5. Magnific Popup was never loaded, so the video popup and lightbox only worked when another plugin happened to load it first — and even once loaded, the click never reached it. Both halves are fixed.
* Right to Left was cosmetic. The wrapper flipped but the carousel itself did not, so drag direction, arrow order and keyboard order all still ran left to right. RTL is now functional.
* Carousels inside tabs, toggles and modals initialise correctly instead of collapsing.
* Editing a module source and regenerating did not update what the plugin actually served, because generated files were only copied during a full webpack build.
* A duplicated 3.2.0 heading in this changelog; the second block was 3.1.1's.

Improved
* Carousel assets no longer load on pages without a carousel — previously around 198 KB on every Divi 5 page.
* Images use responsive srcset, lazy loading and intrinsic dimensions.
* Frontend scripts and styles are versioned by file, so an updated build reaches visitors immediately instead of waiting for a browser cache to expire.
* Continuous integration now runs PHP linting, PHP coding standards, JavaScript linting, generated-file and production-build checks on every change.
* Documentation corrected: this plugin ships four modules (Image, Logo, Video and Nested Carousel), not two.

= 3.1.1 =
* Fix: Critical Divi 5 conflict — plugin assets registered into Divi's Dynamic Assets pipeline could break the site-wide generated stylesheet (counters, background patterns, module stacking). Assets now enqueue normally.
* Fix: No longer loads a duplicate Magnific Popup on Divi 5 (Divi provides its own).
* Fix: Added direct-file-access protection to module files.
* New: PlugPress Suite section on the dashboard.
* Compatibility: Tested up to WordPress 7.0.

= 3.1.0 =
* New: **Video Carousel** module — a Divi 5 carousel for YouTube, Vimeo, and self-hosted videos with Magnific Popup lightbox playback and per-slide title, alt text, and play-icon controls.
* New: Module Manager toggle now gates Divi 5 module registration. Disabling a module from the admin dashboard removes it from the Visual Builder picker.
* New: Robust YouTube and Vimeo URL parsing for the popup player — accepts `youtube.com/watch?v=…`, `youtu.be/…`, `youtube.com/shorts/…`, `youtube.com/live/…`, channel/showcase Vimeo URLs, and bare video IDs.
* New: YouTube embeds now use privacy-enhanced `youtube-nocookie.com` for better compatibility with browser privacy modes and ad-blocker detection. Iframe `allow` attribute now permits `autoplay`, `encrypted-media`, and `picture-in-picture`.
* New: Plugin removal now cleans up all `dcf_*` options and transients via `uninstall.php`.
* Improve: Admin dashboard (Modules, Free vs Pro, Overview) updated to surface Video Carousel.
* Improve: REST `get_modules` response merges defaults so toggles added in newer versions appear automatically for existing installs.

= 3.0.7 =
* Fix: Removed a `LIKE` query against `wp_posts` from frontend page loads. Detection of the deprecated Carousel Maker module now runs only in the WordPress admin, with the result cached in an option. This resolves Bad Gateway / 502 errors reported on hosts that monitor slow database queries.
* Fix: Legacy detection query now uses `$wpdb->prepare` and `$wpdb->esc_like` for safer, standards-compliant query construction.

= 3.0.6 =
* Fix: Minor bug fixes and performance improvements

= 3.0.5 =
* Fix: Minor bug fixes and performance improvements

= 3.0.4 =
* Fix: Plugin no longer breaks counter modules and background patterns on Divi 5 sites
* Fix: D4 assets no longer load on Divi 5 sites preventing style conflicts
* Fix: D5 assets no longer load on Divi 4 sites
* Fix: Image carousel overlay icon now renders correctly in Divi 5
* Fix: Image carousel title and subtitle font, color, and size controls now work in Divi 5
* Fix: Title and subtitle render consistently in both Visual Builder and frontend
* Fix: Title and subtitle font settings grouped under single "Texts" tab matching Divi 4 behavior
* Fix: "Array to string conversion" PHP warning with icon picker in Divi 5
* Fix: Link child elements not working in Divi 5

= 3.0.3 =
* Fix: Minor bug fixes and performance improvements

= 3.0.2 =
* Fix: Minor bug fixes and performance improvements
* Fix: Compatibility with Divi 5

= 3.0.1 =
* Fix: Compatibility issue with Divi 5

= 3.0.0 =
* New: Divi 5 compatibility
* New: Redesigned admin dashboard
* New: Module manager to enable or disable carousel modules
* Update: Upgraded to Swiper for improved performance
* Update: Modernized codebase

= 2.1.5 - 2025-01-27 =
* Update: WordPress 6.8 compatibility
* Update: Improved performance and loading speed
* Fix: Minor bug fixes

= 2.1.4 =
* Update: Plugin renamed to Divi Carousel Free

= 2.1.3 =
* Fix: Navigation arrow icon display issue

= 2.1.2 =
* Fix: Image Carousel title and description alignment

= 2.1.1 =
* Fix: Lightbox functionality restored

= 2.1.0 =
* New: Carousel Maker module
* Update: Redesigned admin dashboard
* Fix: Plugin conflict issues resolved
* Update: WordPress 6.7.1 compatibility

= 2.0.4 =
* Fix: PHP notice resolved
* Update: Security improvements

= 2.0.0 =
* Update: Major codebase overhaul
* New: Default dummy data for quick setup
* Update: WordPress 6.4 compatibility

= 1.2.14 =
* Fix: Module naming conflicts
* Fix: JavaScript errors

= 1.2.13 =
* Fix: PHP 8 compatibility warnings

= 1.2.8 =
* Update: Full RTL support
* Fix: PHP 8 warnings resolved

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 3.2.1 =
Fixes Image Carousel slides rendering the text "ETmodules" instead of the image on Divi 5, along with the lightbox and the image design options that broke with it. Divi 4 sites are unaffected. Recommended for everyone on 3.2.0.

= 3.2.0 =
Adds the Nested Carousel, Quick Start presets, Linked Carousels (thumbnail navigation) and Slide Ratio. Fixes an Image Lightbox that never opened in Divi 5. Right to Left now flips drag and keyboard order, not just text, and arrows and dots are real buttons — update any CSS targeting `div.swiper-button-next`. If you converted a Carousel Maker layout, check slides that used a Divi Library item — the reference is kept in the slide's admin label and needs re-adding by hand.

= 3.1.0 =
Adds a brand-new Video Carousel module for Divi 5 (YouTube, Vimeo, self-hosted with lightbox), wires up the Module Manager to actually gate Divi 5 modules, and ships a proper uninstall cleanup. Recommended for all users.

= 3.0.7 =
Important fix: removes a database query that could trigger Bad Gateway errors on hosts that monitor slow queries. Recommended for all users, especially on managed WordPress hosting.

= 3.0.6 =
Routine maintenance release with minor fixes. Safe upgrade for all users.

= 3.0.0 =
Major update with Divi 5 support, redesigned admin dashboard, and module manager. Recommended for all users.
