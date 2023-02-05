<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class viewDomain{
    public static function get_domain_page_html(){
        global $wpdb;
        ob_start();
        ?>
            <div class="header-bar">
                <h1>登録済みドメイン一覧</h1>
            </div>
            <div class="container">
                <div class="wrap">
                    <?php
                        if(isset($_POST['delete_id'])){
                            $wpdb->delete('wp_plugin_list_domain',['id' => $_POST['delete_id']], ['%d']);
                            echo '<div id="settings_updated" class="updated notice is-dismissible"><p><strong>ドメインを削除しました。</strong></p></div>';
                        }
                    ?>
                    <div class="nav-bar">
                        <form action="" method="POST">
                            <div class="search-box">
                                <input type="text" name="search_domain" placeholder="キーワードを入力してください" value="<?php if($_POST['search_domain']){ echo $_POST['search_domain']; } ?>">
                                <input type="submit" value="ドメインを検索する">
                            </div>
                        </form>
                    </div>
                    <div class="table domain-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>登録済みのドメイン</th>
                                    <th>追加日</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    if($_POST['search_domain']){
                                        $query = "SELECT * FROM wp_plugin_list_domain WHERE domain LIKE '%%%s%%' ORDER BY created_at DESC";
                                        $query = $wpdb->prepare($query, $_POST['search_domain']);
                                        $results = $wpdb->get_results($query);
                                    }else{
                                        $query = "SELECT * FROM wp_plugin_list_domain ORDER BY created_at DESC";
                                        $results = $wpdb->get_results($query);
                                    }
                                    if(!empty($results)): foreach($results as $result):
                                ?>
                                    <tr>
                                        <td><a href="http://<?php echo $result->domain; ?>" target="_blank"><?php echo $result->domain; ?></a></td>
                                        <td><?php echo $result->created_at; ?></td>
                                        <td class="right">
                                            <form action="" method="POST">
                                                <input type="hidden" name="delete_id" value="<?php echo $result->id; ?>">
                                                <input type="submit" class="delete-btn" value="削除">
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr>
                                        <td>登録されているドメインがありません。</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }
}