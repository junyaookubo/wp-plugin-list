<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class addViewDomain{
    public static function get_domain_page_html(){
        ob_start();
        ?>
            <div class="header-bar">
                <h1>新規WordPressアドレス追加</h1>
            </div>
            <div class="container">
                <div class="wrap">
                    <?php
                        if($_POST['domain']){
                            global $wpdb;
                            $insert = $wpdb->insert(
                                'wp_plugin_list_domain',
                                [
                                    'domain' => untrailingslashit($_POST['domain'])
                                ]
                            );
                            if($insert){
                                echo '<div id="settings_updated" class="updated notice is-dismissible"><p><strong>WordPressアドレスを追加しました。</strong></p></div>';
                            }else{
                                echo '<div id="settings_updated" class="updated notice is-dismissible"><p><strong>WordPressアドレスの追加に失敗しました。<br>すでに追加してあるWordPressアドレスか確認してください。</strong></p></div>';
                            }
                        }
                    ?>
                    <div class="nav-bar">
                        <form action="" method="POST">
                            <div class="search-box">
                                <input type="text" name="domain" placeholder="WordPressアドレスを入力してください" required>
                                <input type="submit" value="WordPressアドレスを追加する">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }
}