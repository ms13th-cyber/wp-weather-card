<?php
/*
Plugin Name: Weather Card Widget
Description: 気象庁APIで右下に天気カード表示（画像アイコン・背景色対応）
Version: 1.1
Tested up to: 6.9.4
Requires PHP: 8.3.23
Author: masato shibuya(Image-box Co., Ltd.)
*/

if (!defined('ABSPATH')) exit;

/* =========================
   管理画面
========================= */
add_action('admin_menu', function () {
    add_options_page('天気設定', '天気設定', 'manage_options', 'weather-card', function () {
?>
<div class="wrap">
    <h1>天気設定</h1>
    <form method="post" action="options.php">
        <?php
        settings_fields('wc_settings');
        do_settings_sections('wc_settings');
        submit_button();
        ?>
    </form>
</div>
<?php
    });
});

add_action('admin_init', function () {
    register_setting('wc_settings', 'wc_area');

    add_settings_section('wc_section', '', null, 'wc_settings');

    add_settings_field('wc_area', '地域', function () {
        $areas = [
            '010000'=>'北海道','020000'=>'青森県','030000'=>'岩手県','040000'=>'宮城県','050000'=>'秋田県','060000'=>'山形県','070000'=>'福島県',
            '080000'=>'茨城県','090000'=>'栃木県','100000'=>'群馬県','110000'=>'埼玉県','120000'=>'千葉県','130000'=>'東京都','140000'=>'神奈川県',
            '150000'=>'新潟県','160000'=>'富山県','170000'=>'石川県','180000'=>'福井県','190000'=>'山梨県','200000'=>'長野県',
            '210000'=>'岐阜県','220000'=>'静岡県','230000'=>'愛知県','240000'=>'三重県',
            '250000'=>'滋賀県','260000'=>'京都府','270000'=>'大阪府','280000'=>'兵庫県','290000'=>'奈良県','300000'=>'和歌山県',
            '310000'=>'鳥取県','320000'=>'島根県','330000'=>'岡山県','340000'=>'広島県','350000'=>'山口県',
            '360000'=>'徳島県','370000'=>'香川県','380000'=>'愛媛県','390000'=>'高知県',
            '400000'=>'福岡県','410000'=>'佐賀県','420000'=>'長崎県','430000'=>'熊本県','440000'=>'大分県','450000'=>'宮崎県','460000'=>'鹿児島県','470000'=>'沖縄県'
        ];

        $current = get_option('wc_area', '130000');

        echo '<select name="wc_area">';
        foreach ($areas as $code => $name) {
            echo '<option value="'.$code.'" '.selected($current, $code, false).'>'.$name.'</option>';
        }
        echo '</select>';
    }, 'wc_settings', 'wc_section');
});

/* =========================
   天気取得
========================= */
function wc_get_weather() {

    delete_transient('wc_weather'); // デバッグ用（本番は消してOK）

    $cache = get_transient('wc_weather');
    if ($cache && !empty($cache['weather'])) return $cache;

    $area_code = get_option('wc_area', '130000');
    $url = "https://www.jma.go.jp/bosai/forecast/data/forecast/{$area_code}.json";

    $res = wp_remote_get($url);
    if (is_wp_error($res)) return null;

    $body = json_decode(wp_remote_retrieve_body($res), true);
    if (!isset($body[0]['timeSeries'][0]['areas'])) return null;

    foreach ($body[0]['timeSeries'][0]['areas'] as $a) {
        if (!empty($a['weathers'][0])) {

            $weather = $a['weathers'][0];

            // ===== 整形 =====
            $weather = preg_replace('/\s+/u', ' ', $weather);
            $weather = str_replace('　', ' ', $weather);
            $weather = preg_replace('/\s+/', '、', $weather);
            $weather = str_replace('所により', '一部で', $weather);
            $weather = str_replace('、から', 'から', $weather);
            $weather = str_replace('、まで', 'まで', $weather);

            $area_name = $a['area']['name'];

            break;
        }
    }

    if (empty($weather)) return null;

    // ===== アイコン画像 =====
    $base = plugin_dir_url(__FILE__) . 'icons/';
    $icon = $base . 'sun.png';

    if (strpos($weather, '雨') !== false) {
        $icon = $base . 'rain.png';
    }
    elseif (strpos($weather, '曇') !== false) {
        $icon = $base . 'cloud.png';
    }
    elseif (strpos($weather, '雪') !== false) {
        $icon = $base . 'snow.png';
    }

    // ===== 背景 =====
    $bg = '#fff';

    if (strpos($weather, '雨') !== false) {
        $bg = 'linear-gradient(135deg,#d0e7ff,#a6d4ff)';
    }
    elseif (strpos($weather, '曇') !== false) {
        $bg = 'linear-gradient(135deg,#eee,#ddd)';
    }
    elseif (strpos($weather, '晴') !== false) {
        $bg = 'linear-gradient(135deg,#fff7cc,#ffe58a)';
    }

    $data = [
        'weather' => $weather,
        'icon' => $icon,
        'area' => $area_name,
        'bg' => $bg
    ];

    set_transient('wc_weather', $data, 1800);

    return $data;
}

/* =========================
   表示
========================= */
add_action('wp_footer', function () {

    $weather = wc_get_weather();
    if (!$weather) return;
?>
<div id="weather-card" style="background:<?php echo esc_attr($weather['bg']); ?>">
    <button id="wc-close">×</button>

    <div class="wc-icon">
        <img src="<?php echo esc_url($weather['icon']); ?>" alt="">
    </div>

    <div>
        <div class="wc-area"><?php echo esc_html($weather['area']); ?></div>
        <div class="wc-desc"><?php echo esc_html($weather['weather']); ?></div>
    </div>
</div>

<style>
#index #weather-card {
    position: fixed;
    bottom: 40px;
    right: 20px;
    padding: 16px 20px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    z-index: 9999;
    animation: slideIn .4s ease;
}
@media screen and (max-width: 640px) {
	#index #weather-card {
		display: none;
	}
}
@keyframes slideIn {
    from {opacity:0; transform:translateY(20px);}
    to {opacity:1; transform:translateY(0);}
}
.wc-icon img {
    width: 32px;
    height: 32px;
    margin-right: 12px;
}
#wc-close {
    position: absolute;
    top: 5px;
    right: 8px;
    background: none;
    border: none;
    cursor: pointer;
}
.wc-area {
    font-size: 12px;
    color: #666;
}
.wc-desc {
    font-size: 14px;
}
</style>

<script>
(function(){
    const card = document.getElementById('weather-card');
    const close = document.getElementById('wc-close');

    if(localStorage.getItem('wc_hidden')){
        card.style.display='none';
    }

    close.onclick = () => {
        card.style.display='none';
        localStorage.setItem('wc_hidden','1');
    };
})();
</script>
<?php
});


require_once __DIR__ . '/plugin-update-checker/plugin-update-checker.php';

$updateChecker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
    'https://github.com/ms13th-cyber/wp-weather-card/',
    __FILE__,
    'wp-weather-card'
);

$updateChecker->setBranch('main');