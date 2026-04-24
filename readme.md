# WP Weather Card

A minimalist WordPress plugin that displays real-time weather information using Japan Meteorological Agency (JMA) data. It features a sleek, responsive card design that appears on your front page.

---

## Key Features

- **No API Key Required**: Fetches data directly from official JMA JSON files. No need to register for external services or manage API keys.
- **Smart Caching**: Uses WordPress Transients API to cache weather data for 30 minutes, ensuring high performance and reduced server requests.
- **Responsive & Sleek Design**: A modern floating card with weather-specific background gradients and icons. (Auto-hides on small mobile screens).
- **Easy Region Selection**: Choose your preferred region (Tokyo, Osaka, or Aichi) directly from the WordPress settings page.
- **One-click Dismiss**: Users can close the card, and their preference is saved in local storage.

## Installation

1. Upload the `wp-weather-card` folder to your `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to **Settings > 天気設定** to select your region.

---

## 主な機能（日本語）

気象庁の公開データを利用して、サイトの右下にスタイリッシュな天気カードを表示する軽量プラグインです。

- **APIキー不要**: 気象庁のJSONデータを直接参照するため、外部サービスの登録やAPIキーの管理が一切不要です。
- **キャッシュ機能搭載**: Transients APIを利用し、取得したデータを30分間キャッシュします。サイトの表示速度を損ないません。
- **洗練されたデザイン**: 天候に合わせた背景グラデーションとアイコンを表示。フロントページのみに表示される控えめな設計です。
- **簡単設定**: 管理画面の「天気設定」から地域を選択するだけで、すぐに利用を開始できます。
- **閉じるボタン機能**: ユーザーが一度カードを閉じると、ブラウザのLocalStorageに保存され、再表示を抑制します。