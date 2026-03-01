<?php

if ( ! class_exists( 'DiviPeople_Admin_Menu' ) ) {

    class DiviPeople_Admin_Menu {

        const PARENT_SLUG = 'divipeople';
        private static $menu_added = false;
        private static $registered = [];

        public static function register( $plugin_id, $args = [] ) {
            $defaults = [
                'page_title' => 'DiviPeople',
                'menu_title' => 'DiviPeople',
                'capability' => 'manage_options',
                'menu_slug'  => $plugin_id,
                'callback'   => '',
                'position'   => 50,
            ];
            $args = wp_parse_args( $args, $defaults );
            self::$registered[ $plugin_id ] = $args;
            self::maybe_add_parent_menu( $args );
            add_submenu_page(
                self::PARENT_SLUG,
                $args['page_title'],
                $args['menu_title'],
                $args['capability'],
                $args['menu_slug'],
                $args['callback'],
                $args['position']
            );
            self::remove_duplicate_submenu();
        }

        private static function maybe_add_parent_menu( $first_plugin_args ) {
            if ( self::$menu_added ) {
                return;
            }
            global $menu;
            if ( is_array( $menu ) ) {
                foreach ( $menu as $item ) {
                    if ( isset( $item[2] ) && $item[2] === self::PARENT_SLUG ) {
                        self::$menu_added = true;
                        return;
                    }
                }
            }
            add_menu_page(
                'DiviPeople',
                'DiviPeople',
                'manage_options',
                self::PARENT_SLUG,
                '',
                self::get_menu_icon(),
                65
            );
            self::$menu_added = true;
        }

        private static function remove_duplicate_submenu() {
            add_action( 'admin_head', function () {
                remove_submenu_page( self::PARENT_SLUG, self::PARENT_SLUG );
            });
        }

        private static function get_menu_icon() {
            $svg = '<svg width="96" height="96" viewBox="0 0 96 96" fill="none" xmlns="http://www.w3.org/2000/svg">
<g clip-path="url(#clip0_3035_40)">
<path d="M48 0C74.5097 0 96 21.4903 96 48C96 74.5097 74.5097 96 48 96C21.4903 96 0 74.5097 0 48C0 21.4903 21.4903 0 48 0ZM41.9131 21.6C38.0638 21.6 34.3303 21.8364 30.713 22.3095C27.0955 22.7825 24.0578 23.2359 21.6 23.6695L23.6869 33.721C24.707 33.4056 25.8202 33.1296 27.0259 32.8932L24.4522 73.5132L36.1392 74.4C36.2321 73.8086 36.3247 73.1189 36.4174 72.3305C40.0812 73.513 43.7914 74.1046 47.5478 74.1046C53.2058 74.1046 58.0291 73.0598 62.0174 70.9706C66.0523 68.8814 69.113 65.8658 71.2001 61.9243C73.3334 57.943 74.4 53.1538 74.4 47.5565C74.4 43.5754 73.7506 40.1263 72.4522 37.2094C71.1998 34.2924 69.4608 31.8288 67.2348 29.8186C65.0551 27.8083 62.5507 26.2118 59.7218 25.0294C56.9393 23.8074 54.0173 22.9402 50.9563 22.4278C47.8956 21.876 44.8812 21.6 41.9131 21.6ZM41.4259 31.5334C41.9825 31.5334 42.887 31.5924 44.1391 31.7107C45.3912 31.7894 46.8058 31.9865 48.3826 32.3018C50.0057 32.6172 51.6521 33.1099 53.3218 33.78C54.9912 34.4107 56.5219 35.2978 57.9132 36.4409C59.3508 37.584 60.51 39.0228 61.3913 40.757C62.2723 42.4915 62.7132 44.6004 62.7132 47.0837C62.7132 50.1977 62.203 53.0947 61.1827 55.775C60.1625 58.4162 58.4926 60.5446 56.1739 62.1607C53.9014 63.7769 50.887 64.585 47.1305 64.585C45.2753 64.585 43.4897 64.4472 41.7739 64.1712C40.1042 63.8558 38.574 63.5011 37.1827 63.1068C37.4609 59.6777 37.7854 55.4006 38.1564 50.2764C38.5274 45.1128 38.945 38.8649 39.4087 31.5334H41.4259Z" fill="white"/>
</g>
<defs>
<clipPath id="clip0_3035_40">
<rect width="96" height="96" fill="white"/>
</clipPath>
</defs>
</svg>';
            return 'data:image/svg+xml;base64,' . base64_encode( $svg );
        }

        public static function get_registered_plugins() {
            return self::$registered;
        }
    }
}
