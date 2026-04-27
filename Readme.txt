=== Weather Card Widget (JMA Edition) ===
Contributors: masato shibuya(Image-box Co., Ltd.)
Tags: weather, widget, jma, forecast, ui
Requires at least: 5.0
Tested up to: 6.9.4
Requires PHP: 8.0
Stable tag: 2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

気象庁APIを利用して、画面右下に天気カードを表示するシンプルなプラグインです。全国約150の予報細分区域に対応しました。

== Description ==

Weather Card Widget (JMA Edition) は、日本の気象庁が提供する天気予報APIを利用し、指定した地域の天気情報をカード形式で画面右下に表示するWordPressプラグインです 。

主な特徴：

・気象庁公式データを使用（無料・APIキー不要） 
・全国約150の予報細分区域（東京地方、静岡県中部など）に対応 [cite: 1, 2]
・保存ロジックの刷新により、選択した地域名が正確に表示されます 
・画面右下に固定表示（フロントページのみ） 
・×ボタンで非表示（localStorageで保持） 
・天気に応じて背景カラーとアイコンが自動変化（晴れ・曇り・雨・雪） 
・パフォーマンス最適化（30分間のキャッシュ機能） 

== Installation ==

1. プラグインフォルダを配置します
   `wp-content/plugins/wp-weather-card/` 

2. 以下のファイル・フォルダ構成を確認してください
   - `wp-weather-card.php`
   - `data/areas.json` （地域データ）
   - `icons/` （sun.png, cloud.png, rain.png, snow.png） 
   - `plugin-update-checker/` （更新確認用ライブラリ） 

3. WordPress管理画面からプラグインを有効化します 

4. 「設定 > 天気設定」から地域を選択し、「変更を保存」をクリックしてください 

== Folder Structure ==

wp-weather-card/
├─ wp-weather-card.php
├─ data/
│  └─ areas.json
├─ icons/
│  ├─ sun.png
│  ├─ cloud.png
│  ├─ rain.png
│  └─ snow.png
└─ plugin-update-checker/

== Usage ==

プラグインを有効化し、地域を設定するだけでサイトのフロントページ右下に天気カードが表示されます 。

・×ボタンで非表示にすると、ブラウザ（LocalStorage）に保存され、再訪問時も非表示が維持されます 。

== Settings ==

「設定 > 天気設定」より以下を設定可能です：

・表示地域：全国約150区域から選択 
・現在の設定値：保存されている地域名が確認できます 

== Notes ==

・天気データはWordPressのTransients APIにより30分間キャッシュされます 。
・APIの仕様変更やサーバーの通信制限により、データが取得できない場合はカードが表示されません 。
・北海道や離島など、一部の特殊なエリアコードにも対応しています [cite: 1, 2]。

== Changelog ==

= 2.0 =
・保存ロジックの刷新（JavaScriptによる確実なラベル保存）
・インラインスタイルによる表示の安定化
・全国150区域の予報細分区域に対応
・areas.jsonによる地域管理の実装
・アイコン判定ロジックの強化

= 1.3 =
・安定性向上と細かなバグ修正

== License ==

This plugin is licensed under the GPLv2 or later.