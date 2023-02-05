<?php

if ( ! defined( 'ABSPATH' ) ) exit;
require_once __DIR__.'/view-admin.php';
require_once __DIR__.'/view-add-domain.php';
require_once __DIR__.'/view-domain.php';
require_once __DIR__.'/view-plugin.php';

class adminController{
    private $table_name;

    public function __construct(){

        global $wpdb;
        $this->table_name = $wpdb->prefix.'wp-plugin-list-domain';
        register_activation_hook(plugins_url().'/wp-plugin-list/wp-plugin-list.php', [$this, 'activate']);

        add_action('admin_menu', function(){
            add_menu_page(
                'インストール済みのプラグイン',
                'WP Plugin List',
                'manage_options',
                dirname(__DIR__),
                [$this, 'dashboard_page']
            );
        });
        add_action('admin_menu', function(){
            add_submenu_page(
                dirname(__DIR__),
                '登録ドメイン一覧',
                '登録ドメイン一覧',
                'manage_options',
                dirname(__DIR__).'-domain',
                [$this, 'domain_page']
            );
        });
        add_action('admin_menu', function(){
            add_submenu_page(
                dirname(__DIR__),
                '新規ドメイン追加',
                '新規ドメイン追加',
                'manage_options',
                dirname(__DIR__).'-add-domain',
                [$this, 'add_domain_page']
            );
        });
        add_action('admin_menu', function(){
            add_submenu_page(
                dirname(__DIR__),
                'プラグイン検索',
                'プラグイン検索',
                'manage_options',
                dirname(__DIR__).'-search',
                [$this, 'search_plugin_page']
            );
        });
    }

    function activate(){
        global $wpdb;
        echo $this->table_name;
        $charset_collate = $wpdb->get_charset_collate();
        $sql = 'CREATE TALBE'.$this->table_name.'(
            id int UNSIGNED NOT NULL AUTO_INCREMENT,
            domain varchar(255),
            created_at datetime default current_timestamp,
            updated_at datetime default current_timestamp on update current_timestamp,
            UNIQUE KEY domain (domain)
        )'.$charset_collate.'';

        require_once ABSPATH.'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    public function dashboard_page(){
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }
        $html = viewAdmin::get_dashboard_page_html();
        echo $html;
    }

    public function domain_page(){
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }
        $html = viewDomain::get_domain_page_html();
        echo $html;
    }

    public function add_domain_page(){
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }
        $html = addViewDomain::get_domain_page_html();
        echo $html;
    }

    public function search_plugin_page(){
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }
        $html = viewPlugin::get_search_plugin_page_html();
        echo $html;
    }
}

new adminController();