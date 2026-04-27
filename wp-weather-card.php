<?php
/*
Plugin Name: Weather Card Widget
Description: 気象庁APIで右下に天気カード表示（全国150区域対応版）
Version: 2.0.0
Tested up to: 6.9.4
Requires PHP: 8.0
Author: masato shibuya(Image-box Co., Ltd.)
*/

if (!defined('ABSPATH')) exit;

/* =========================
   管理画面：設定
========================= */
add_action('admin_menu', function () {
    add_options_page('天気設定', '天気設定', 'manage_options', 'weather-card', function () {
?>
<div class="wrap">
    <h1>天気設定</h1>
    <form method="post" action="options.php" id="wc-settings-form">
        <?php
        settings_fields('wc_settings');
        do_settings_sections('wc_settings');
        // 保存時に選択されたテキストを取得するための隠しフィールド
        ?>
        <input type="hidden" name="wc_area_label" id="wc_area_label" value="<?php echo esc_attr(get_option('wc_area_label')); ?>">
        <?php submit_button(); ?>
    </form>
    <script>
    // フォーム送信時に、選択されている項目の「テキスト」を隠しフィールドにコピーする
    document.getElementById('wc-settings-form').onsubmit = function() {
        const select = document.querySelector('select[name="wc_area"]');
        const labelInput = document.getElementById('wc_area_label');
        if (select && labelInput) {
            labelInput.value = select.options[select.selectedIndex].text;
        }
    };
    </script>
</div>
<?php
    });
});

add_action('admin_init', function () {
    register_setting('wc_settings', 'wc_area');
    register_setting('wc_settings', 'wc_area_label'); // JavaScript経由で保存

    add_settings_section('wc_section', '地域設定', null, 'wc_settings');

    add_settings_field(
        'wc_area',
        '表示地域',
        function () {
            $json_path = plugin_dir_path(__FILE__) . 'data/areas.json';
            if (!file_exists($json_path)) {
                echo '<span style="color:red;">areas.jsonが見つかりません。パスを確認してください: ' . esc_html($json_path) . '</span>';
                return;
            }
            $areas = json_decode(file_get_contents($json_path), true);
            $current = get_option('wc_area', '');

            echo '<select name="wc_area" style="width:300px;">';
            echo '<option value="">地域を選択してください</option>';
            foreach ($areas as $pref => $list) {
                echo '<optgroup label="'.esc_attr($pref).'">';
                foreach ($list as $code => $label) {
                    echo '<option value="'.esc_attr($code).'" '.selected($current, $code, false).'>'.esc_html($label).'</option>';
                }
                echo '</optgroup>';
            }
            echo '</select>';

            $label = get_option('wc_area_label', '');
            echo '<p class="description">現在の設定値: <strong>' . esc_html($label ?: '（未設定）') . '</strong></p>';
        },
        'wc_settings',
        'wc_section'
    );
});

/* =========================
   データ取得
========================= */
function wc_get_weather() {
    $area_code = get_option('wc_area', '');
    $area_label = get_option('wc_area_label', '');

    if (!$area_code) return null;

    $cache_key = 'wc_weather_v3_' . $area_code;
    $cache = get_transient($cache_key);
    if ($cache) return $cache;

    // API用コードの正規化
    $api_code = substr($area_code, 0, 2) . '0000';
    if (substr($area_code, 0, 2) === '01') {
        $api_code = substr($area_code, 0, 3) . '000';
    }

    $url = "https://www.jma.go.jp/bosai/forecast/data/forecast/{$api_code}.json";
    $res = wp_remote_get($url);
    if (is_wp_error($res)) return null;

    $body = json_decode(wp_remote_retrieve_body($res), true);
    if (!$body || !isset($body[0]['timeSeries'])) return null;

    $weather = null;
    foreach ($body[0]['timeSeries'] as $ts) {
        if (!isset($ts['areas'])) continue;
        foreach ($ts['areas'] as $a) {
            // API側の名前、またはコード、または保存したラベル名で照合
            if ($a['area']['code'] === $area_code || $a['area']['name'] === $area_label) {
                if (!empty($a['weathers'][0])) {
                    $weather = $a['weathers'][0];
                    break 2;
                }
            }
        }
    }

    // 最終フォールバック
    if (!$weather && isset($body[0]['timeSeries'][0]['areas'][0]['weathers'][0])) {
        $weather = $body[0]['timeSeries'][0]['areas'][0]['weathers'][0];
    }

    if (!$weather) return null;

    $display_weather = str_replace(['　', '  '], ' ', $weather);
    $display_weather = mb_ereg_replace('\s+', '、', $display_weather);

    $base_url = plugin_dir_url(__FILE__);
    $data = [
        'weather' => $display_weather,
        'icon'    => (mb_strpos($display_weather, '雨') !== false) ? $base_url . 'icons/rain.png' :
                     ((mb_strpos($display_weather, '曇') !== false) ? $base_url . 'icons/cloud.png' : $base_url . 'icons/sun.png'),
        'area'    => $area_label,
        'bg'      => (mb_strpos($display_weather, '雨') !== false) ? 'linear-gradient(135deg,#d0e7ff,#a6d4ff)' :
                     ((mb_strpos($display_weather, '曇') !== false) ? 'linear-gradient(135deg,#eee,#ddd)' : 'linear-gradient(135deg,#fff7cc,#ffe58a)')
    ];

    set_transient($cache_key, $data, 1800);
    return $data;
}

/* =========================
   表示
========================= */
add_action('wp_footer', function () {
    if (!is_front_page()) return;
    $weather = wc_get_weather();
    if (!$weather) return;
?>
<div id="weather-card" style="background:<?php echo esc_attr($weather['bg']); ?>; position:fixed; bottom:30px; right:20px; padding:14px 18px; border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,0.15); z-index:9999; display:flex; align-items:center;">
    <button id="wc-close" style="position:absolute; top:5px; right:8px; background:none; border:none; cursor:pointer; font-size:16px; color:#888;">×</button>
    <div style="display:flex; align-items:center;">
        <img src="<?php echo esc_url($weather['icon']); ?>" style="width:36px; height:36px; margin-right:12px;">
        <div>
            <div style="font-size:11px; color:#555; font-weight:bold;"><?php echo esc_html($weather['area']); ?></div>
            <div style="font-size:13px; line-height:1.3; color:#222;"><?php echo esc_html($weather['weather']); ?></div>
        </div>
    </div>
</div>
<script>
(function(){
    const card = document.getElementById('weather-card');
    if(!card) return;
    if(localStorage.getItem('wc_hidden_v3')){ card.style.display = 'none'; }
    document.getElementById('wc-close').onclick = () => {
        card.style.display = 'none';
        localStorage.setItem('wc_hidden_v3', '1');
    };
})();
</script>
<style>@media screen and (max-width: 640px) { #weather-card { display: none; } }</style>
<?php
});

/* =========================
   アップデートチェッカー
========================= */
require_once __DIR__ . '/plugin-update-checker/plugin-update-checker.php';
$updateChecker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
    'https://github.com/ms13th-cyber/wp-weather-card/',
    __FILE__,
    'wp-weather-card'
);
$updateChecker->setBranch('main');