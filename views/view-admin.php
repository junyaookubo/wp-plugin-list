<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class viewAdmin{
    public static function get_dashboard_page_html(){
        ob_start();
        ?>
            <div class="header-bar">
                <h1>インストール済みのプラグイン</h1>
            </div>
            <div class="container">
                <div class="wrap">
                    <p class="text lh2">
                        登録済みのWordPressアドレスからインストール済みのプラグイン一覧を表示します。<br />
                        プラグインの一覧を取得したいWordPressアドレスには、別途「WP Plugin List API」をインストールしてください。
                    </p>
                    <?php
                            global $wpdb;
                            $query = "SELECT * FROM wp_plugin_list_domain ORDER BY created_at DESC";
                            $results = $wpdb->get_results($query);
                            if(!empty($results)):
                    ?>
                        <div class="nav-bar">
                            <form action="" method="POST">
                                <div class="search-box">
                                    <select name="select_domain">
                                        <?php foreach($results as $result): ?>
                                            <option value="<?php echo $result->domain; ?>" <?php if(isset($_POST['select_domain']) && $_POST['select_domain'] == $result->domain){echo 'selected';} ?>><?php echo $result->domain; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="submit" value="適用">
                                </div>
                            </form>
                        </div>
                        <div class="table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>インストール済みのプラグイン</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        if($_POST['select_domain']){
                                            $domain = $_POST['select_domain'];
                                        }else{
                                            $domain = $results[0]->domain;
                                        }
                                        $args = [
                                            'headers' => [
                                                'Authorization' => 'Basic '.base64_encode("user:user01")
                                            ]
                                        ];
                                        $response = wp_remote_get(''.$domain.'/wp-json/wp/custom/get_plugins',$args);
                                        if(wp_remote_retrieve_response_code($response) == 200):
                                            $response = json_decode(wp_remote_retrieve_body($response),true);
                                            if(!empty($response)): foreach($response as $plugin):
                                    ?>
                                        <tr>
                                            <td><?php if($plugin['status']){ echo '<span class="active">有効</span>'; }else{ echo '<span class="deactive">無効</span>'; } ?><a href=""><?php echo $plugin['name']; ?></a></td>
                                        </tr>
                                    <?php endforeach; else: ?>
                                        <tr>
                                            <td>インストールされているプラグインがありません。</td>
                                        </tr>
                                    <?php endif; else: ?>
                                        <tr>
                                            <td>WordPressに「WP Plugin List API」がインストールされていません。</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }
}