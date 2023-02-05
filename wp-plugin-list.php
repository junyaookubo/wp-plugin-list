<?php
/*
Plugin Name: WP Plugin List
Description: 登録ドメインのインストール済みプラグイン一覧を表示するプラグインです。
Version: 1.0
Author: World Utility Co., Ltd.
*/

if ( ! defined( 'ABSPATH' ) ) exit;

require_once __DIR__.'/views/view-admin.php';
require_once __DIR__.'/views/view-add-domain.php';
require_once __DIR__.'/views/view-domain.php';
require_once __DIR__.'/views/view-plugin.php';

class adminController{
    private $table_name;

    public function __construct(){

        global $wpdb;
        $this->table_name = $wpdb->prefix.'plugin_list_domain';
        register_activation_hook(__FILE__, [$this, 'activate']);

        add_action('admin_menu', function(){
            add_menu_page(
                'インストール済みのプラグイン',
                'WP Plugin List',
                'manage_options',
                'wp-plugin-list',
                [$this, 'dashboard_page']
            );
        });
        add_action('admin_menu', function(){
            add_submenu_page(
                'wp-plugin-list',
                '登録ドメイン一覧',
                '登録ドメイン一覧',
                'manage_options',
                'wp-plugin-list-domain',
                [$this, 'domain_page']
            );
        });
        add_action('admin_menu', function(){
            add_submenu_page(
                'wp-plugin-list',
                '新規ドメイン追加',
                '新規ドメイン追加',
                'manage_options',
                'wp-plugin-list-add-domain',
                [$this, 'add_domain_page']
            );
        });
        add_action('admin_menu', function(){
            add_submenu_page(
                'wp-plugin-list',
                'プラグイン検索',
                'プラグイン検索',
                'manage_options',
                'wp-plugin-list-search',
                [$this, 'search_plugin_page']
            );
        });
    }

    function activate(){
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $this->table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            domain varchar(255) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY id (id),
            UNIQUE KEY domain (domain)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
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

function adminStyle(){
    wp_enqueue_style('admin-style', plugins_url('/', __FILE__).'admin.css');
}
add_action('admin_head','adminStyle');