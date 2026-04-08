=== Weather Card Widget (JMA) ===
Contributors: masato shibuya(Image-box Co., Ltd.)
Tags: weather, widget, jma, forecast, ui
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 8.0
Stable tag: 1.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

気象庁APIを利用して、画面右下に天気カードを表示するシンプルなプラグインです。

== Description ==

Weather Card Widget (JMA) は、日本の気象庁が提供する天気予報APIを利用し、
指定した地域の天気情報をカード形式で画面右下に表示するWordPressプラグインです。

主な特徴：

・気象庁公式データを使用（無料・APIキー不要）
・画面右下に固定表示（常時表示）
・×ボタンで非表示（localStorageで保持）
・管理画面から都道府県選択可能
・天気に応じて背景カラー変化
・シンプルなカードUI
・画像アイコンで安定表示（文字化けなし）

== Installation ==

1. プラグインフォルダを作成します
   wp-content/plugins/wp-weather-card/

2. 以下のファイルを配置
   - wp-weather-card.php
   - iconsフォルダ

3. iconsフォルダ内に以下の画像を配置
   - sun.png
   - cloud.png
   - rain.png
   - snow.png

4. WordPress管理画面からプラグインを有効化

5. 「設定 > 天気設定」から地域を選択

== Folder Structure ==

wp-weather-card/
├─ weather-card.php
└─ icons/
   ├─ sun.png
   ├─ cloud.png
   ├─ rain.png
   └─ snow.png

== Usage ==

プラグインを有効化するだけで、
サイトのフロント画面右下に天気カードが表示されます。

・×ボタンで非表示（ブラウザに保存）
・ページリロード後も非表示を維持

== Settings ==

「設定 > 天気設定」より以下を設定可能：

・地域（都道府県）

== Notes ==

・天気データは30分キャッシュされます
・初回取得時はAPI通信が発生します
・サーバー環境によっては表示に数秒かかる場合があります

== Troubleshooting ==

■ 天気が表示されない
・APIレスポンス取得失敗の可能性
・サーバーの外部通信制限を確認

■ アイコンが表示されない
・iconsフォルダの配置を確認
・画像ファイル名の一致を確認

■ 表示が更新されない
・キャッシュ（transient）を削除
・ブラウザキャッシュをクリア

== Changelog ==

= 1.2.0 =
・テキスト修正

= 1.1.0 =
・テキスト修正
・更新確認

= 1.0.0 =
・初回リリース
・気象庁API対応
・カードUI実装
・画像アイコン対応
・背景色切替機能追加

== License ==

This plugin is licensed under the GPLv2 or later.
