<?php
/*
Plugin Name: Weather Card Widget
Description: 気象庁APIで右下に天気カード表示（画像アイコン・背景色対応）
Version: 1.3
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
            '270000'=>'大阪府','130000'=>'東京都','230000'=>'愛知県'
        ];

        $current = get_option('wc_area', '270000');

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

    $cache = get_transient('wc_weather');
    if ($cache && !empty($cache['weather'])) return $cache;

    $area_code = get_option('wc_area', '270000');
    $url = "https://www.jma.go.jp/bosai/forecast/data/forecast/{$area_code}.json";

    $res = wp_remote_get($url);
    if (is_wp_error($res)) return null;

    $body = json_decode(wp_remote_retrieve_body($res), true);
    if (!isset($body[0]['timeSeries'][0]['areas'])) return null;

    foreach ($body[0]['timeSeries'][0]['areas'] as $a) {
        if (!empty($a['weathers'][0])) {
            $weather = $a['weathers'][0];

            // 整形
            $weather = preg_replace('/\s+/u', ' ', $weather);
            $weather = str_replace('　', ' ', $weather);
            $weather = preg_replace('/\s+/', '、', $weather);

            $area_name = $a['area']['name'];
            break;
        }
    }

    if (empty($weather)) return null;

    // アイコン
    $base = plugin_dir_url(__FILE__) . 'icons/';
    $icon = $base . 'sun.png';

    if (strpos($weather, '雨') !== false) $icon = $base . 'rain.png';
    elseif (strpos($weather, '曇') !== false) $icon = $base . 'cloud.png';
    elseif (strpos($weather, '雪') !== false) $icon = $base . 'snow.png';

    // 背景
    $bg = '#fff';
    if (strpos($weather, '雨') !== false) $bg = 'linear-gradient(135deg,#d0e7ff,#a6d4ff)';
    elseif (strpos($weather, '曇') !== false) $bg = 'linear-gradient(135deg,#eee,#ddd)';
    elseif (strpos($weather, '晴') !== false) $bg = 'linear-gradient(135deg,#fff7cc,#ffe58a)';

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

    // トップページのみ表示
    if (!is_front_page()) return;

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
#weather-card {
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
    #weather-card {
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

    if(!card) return;

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