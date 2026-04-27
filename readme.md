# WP Weather Card (JMA Edition)

A minimalist WordPress plugin that displays real-time weather information using official Japan Meteorological Agency (JMA) data. It features a sleek, floating card design that appears automatically on your site's front page, now supporting over 150 regions across Japan.

[日本語の解説は英語の後にあります]

---

## Key Features

- **Broad Region Support**: Updated to support approximately 150 detailed weather forecast areas across Japan, from Hokkaido to Okinawa.
- **Robust Data Handling**: Enhanced saving logic using JavaScript to ensure region names are correctly stored and displayed.
- **No API Key Required**: Fetches data directly from official JMA JSON files. No registration or external API keys are needed.
- **Zero-Config Display**: No shortcodes or widgets required. Once activated and configured, the weather card appears automatically in the bottom-right corner of your front page.
- **Performance Optimized**: Uses the WordPress Transients API to cache weather data for 30 minutes, ensuring minimal server impact.
- **Dynamic UI**: Includes weather-specific background gradients and icons that change based on the current forecast.
- **User-Friendly Dismissal**: Includes a "close" button that saves the user's preference in LocalStorage to prevent the card from reappearing unnecessarily.

## Installation

1. Upload the `wp-weather-card` folder to your `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to **Settings > 天気設定 (Weather Settings)** to select your specific region from the dropdown menu.

---

## 主な機能（日本語）

気象庁の公開データを活用し、サイトのフロントページにスタイリッシュな天気カードを自動表示する軽量プラグインです。全国約150の予報細分区域に対応しています。

- **全国150区域に対応**: 北海道から沖縄まで、気象庁が定義する詳細な予報区を選択可能です。
- **確実な保存ロジック**: 保存処理を刷新し、選択した地域名がデータベースに正確に記録・表示されるよう安定性を向上させました。
- **APIキー不要**: 気象庁のJSONデータを直接参照するため、外部サービスへの登録や複雑なAPIキー管理が一切不要です。
- **自動フローティング表示**: ショートコードやウィジェットの設定は不要。有効化して地域を選ぶだけで、サイトの右下に洗練された天気カードが表示されます。
- **パフォーマンス重視**: Transients APIによる30分間のキャッシュ機能を搭載。サーバーへの負荷を最小限に抑えつつ、最新の情報を維持します。
- **動的なデザイン変更**: 天候（晴れ・曇り・雨・雪）に合わせて、背景のグラデーションやアイコンが自動的に切り替わります。
- **ユーザー配慮設計**: 閉じるボタンを搭載。ユーザーが一度閉じればLocalStorageに保存され、再表示を抑制するスマートな挙動です。

## インストール・設定

1. `wp-weather-card` フォルダを `/wp-content/plugins/` にアップロードします。
2. 管理画面の「プラグイン」から有効化してください。
3. 「設定」 > 「天気設定」から、表示したい地域を選択して「変更を保存」をクリックしてください。
    - ※設定画面に「現在の設定値」として地域名が表示されることを確認してください。

## 開発者情報
- **Author**: masato shibuya (Image-box Co., Ltd.)
- **Version**: 2.0
- **Update**: https://github.com/ms13th-cyber/wp-weather-card/