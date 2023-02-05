<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class viewPlugin{
    public static function get_search_plugin_page_html(){
        ob_start();
        ?>
            <div class="header-bar">
                <h1>プラグイン検索</h1>
            </div>
            <div class="container">
                <div class="wrap">
                    <div class="nav-bar">
                        <p class="text lh2 mb10 text-black">絞り込みたいプラグイン名を選択してください（複数選択可）</p>
                        <form action="" method="POST">
                            <div class="select-list">
                                <?php
                                    global $wpdb;
                                    $query = "SELECT * FROM wp_plugin_list_domain ORDER BY created_at DESC";
                                    $results = $wpdb->get_results($query);
                                    $datas = array();
                                    foreach($results as $result){
                                        $args = [
                                            'headers' => [
                                                'Authorization' => 'Basic '.base64_encode("user:user01")
                                            ]
                                        ];
                                        $response = wp_remote_get(''.$result->domain.'/wp-json/wp/custom/get_plugins',$args);
                                        if(wp_remote_retrieve_response_code($response) == 200){
                                            $response = json_decode(wp_remote_retrieve_body($response),true);
                                            if(!empty($response)){
                                                $datas[] = $response;
                                            }
                                        }
                                    }
                                ?>
                                <?php
                                    $plugins = array();
                                    foreach($datas as $data){
                                        foreach($data as $plugin){
                                            if(!in_array($plugin['name'],$plugins)){
                                                $plugins[] = $plugin['name'];
                                            }
                                        }
                                    }
                                    sort($plugins);
                                    $i = 0; foreach($plugins as $plugin_name):
                                ?>
                                    <div class="item">
                                        <input type="checkbox" name="check_plugin[]" id="p<?php echo $i; ?>" value="<?php echo $plugin_name; ?>" <?php if( isset($_POST['check_plugin']) && in_array($plugin_name,$_POST['check_plugin'])){ echo 'checked'; } ?>>
                                        <label for="p<?php echo $i; ?>"><?php echo $plugin_name; ?></label>
                                    </div>
                                <?php $i++; endforeach; ?>
                            </div>
                            <p class="text lh2 mb10 text-black mt30">プラグインのステータスを選択してください（複数選択可）</p>
                            <div class="select-list">
                                <div class="item">
                                    <input type="checkbox" name="status_plugin[]" id="s1" value="active" <?php if(isset($_POST['status_plugin']) && in_array('active',$_POST['status_plugin'])){ echo 'checked'; } ?>>
                                    <label for="s1">有効プラグイン</label>
                                </div>
                                <div class="item">
                                    <input type="checkbox" name="status_plugin[]" id="s2" value="deactive" <?php if(isset($_POST['status_plugin']) && in_array('deactive',$_POST['status_plugin'])){ echo 'checked'; }?>>
                                    <label for="s2">無効プラグイン</label>
                                </div>
                            </div>
                            <div class="filter-btn mt30">
                                <input type="submit" value="絞り込む">
                            </div>
                        </form>
                    </div>
                    <?php if(isset($_POST['check_plugin']) && isset($_POST['status_plugin'])): ?>
                        <div class="table vat plugin-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>WordPressアドレス</th>
                                        <th>選択中のプラグイン</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        foreach($datas as $data):
                                            $exists_plugins = array();
                                            foreach($_POST['check_plugin'] as $check_plugin){
                                                $exists_index = array_search($check_plugin, array_column($data,'name'),true);
                                                if($exists_index !== false){
                                                    $exists_plugins[] = $data[$exists_index];
                                                }
                                            }
                                            if(in_array('active',$_POST['status_plugin']) && in_array('deactive',$_POST['status_plugin'])){
                                                $delete_index = false;
                                            }elseif(in_array('active',$_POST['status_plugin'])){
                                                $delete_index = array_keys(array_column($exists_plugins,'status'), false, true);
                                            }elseif(in_array('deactive',$_POST['status_plugin'])){
                                                $delete_index = array_keys(array_column($exists_plugins,'status'), true, true);
                                            }
                                            if($delete_index !== false){
                                                foreach($delete_index as $index){
                                                    unset($exists_plugins[$index]);
                                                }
                                            }
                                            if(!empty($exists_plugins)):
                                    ?>
                                        <tr>
                                            <td><a href="<?php echo $exists_plugins[0]['site']; ?>" target="_blank"><?php echo $exists_plugins[0]['site']; ?></a></td>
                                            <td>
                                                <?php
                                                    foreach($exists_plugins as $plugin){
                                                        if($plugin['status']){
                                                            echo '<div><span class="active">有効</span>'.$plugin['name'].'</div>';
                                                        }else{
                                                            echo '<div><span class="deactive">無効</span>'.$plugin['name'].'</div>';
                                                        }
                                                    }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endif; endforeach; ?>
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