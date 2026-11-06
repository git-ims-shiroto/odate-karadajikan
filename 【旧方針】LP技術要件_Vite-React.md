# LP制作 技術要件定義

> **廃止済みの旧方針です。** 現行の要件は `【本番開発時に確認】LP技術要件.md` を参照してください。

作成日: 2026-09-02
案件: 生徒募集LP（クライアント納品）

---

## 1. 技術スタック

| 区分 | 採用技術 | 補足 |
|---|---|---|
| ビルドツール | Vite | 開発サーバー + 本番ビルド |
| UIライブラリ | React | |
| CSS | Tailwind CSS v4 | `@tailwindcss/vite` プラグイン使用 |
| コンポーネント | shadcn/ui（部分導入） | 必要になった時点で個別追加 |
| ホスティング | Cloudflare Pages | 無料プラン |
| 出力形式 | 静的サイト | SSR/ISRは使用しない |

---

## 2. ホスティング方針

### Cloudflare Pages を採用

Vercel無料枠（Hobbyプラン）は**非商用の個人プロジェクト限定**であり、今回の案件は以下の2点で商用に該当するため使用不可。

- 生徒募集＝収益に直結する集客サイト
- 制作費を受領する受注案件

Cloudflare Pagesは無料プランで商用利用が明示的に許可されている。

### 無料プランの制限

| 項目 | 上限 |
|---|---|
| 帯域幅 | 無制限 |
| リクエスト数 | 無制限 |
| 独自ドメイン | 無制限 |
| SSL証明書 | 自動発行・自動更新（無料） |
| ビルド回数 | 月500回 |
| ファイル数 | 20,000 / サイト |
| ファイルサイズ | 25MB / ファイル |
| 同時ビルド | 1 |

LP1枚の規模では制限に抵触しない。

### ビルド設定

| 項目 | 値 |
|---|---|
| ビルドコマンド | `npm run build` |
| 出力ディレクトリ | `dist` |

### デプロイ方式

以下のいずれか。

- **Git連携** — GitHubにpushで自動デプロイ。ブランチごとにプレビューURLが自動生成されるため、クライアント確認に有用
- **Direct Upload** — `dist` フォルダをドラッグ&ドロップ、または `wrangler pages deploy ./dist`

過去のデプロイ履歴から管理画面でロールバック可能。

---

## 3. アカウント・所有権の方針

**すべてクライアント名義で取得する。**

| 対象 | 名義 | 理由 |
|---|---|---|
| Cloudflareアカウント | クライアント | 納品後の支配権をクライアントに残す |
| 独自ドメイン | クライアント | トラブル時に最もこじれるポイント。クライアントのメールアドレス・支払い方法で取得 |
| Gitリポジトリ | 要相談 | 保守契約の有無で判断 |

### 継続保守を請け負う場合

クライアントのCloudflareアカウントに制作者をメンバーとして招待してもらう。
名義はクライアント / 作業は制作者、という形で両立する。

### 避けるべき構成

- 制作者アカウントに複数クライアントの案件を同居させる（アカウント停止時に全案件が巻き添え）
- 制作者名義のドメイン取得
- Manus等のエージェントサービス上での公開（`*.manus.space` に紐づき、所有権移転・永続性・独自ドメインすべてに問題あり）

---

## 4. shadcn/ui の運用方針

### 基本方針: 最初は入れない

まずTailwindを直接記述して着手し、自前実装が煩雑な要素が出た時点で、**そのコンポーネントのみ個別追加**する。

```bash
npx shadcn@latest add accordion
```

コンポーネントは `components/ui/` 配下に**ソースコードとしてコピーされる**ため、依存パッケージが増えず、自由に改変できる。1個単位で追加可能。

### 導入を検討する要素

| 要素 | コンポーネント | 判断 |
|---|---|---|
| FAQアコーディオン | `accordion` | キーボード操作・開閉制御が面倒なため導入価値が高い |
| 資料請求モーダル | `dialog` | フォーカストラップの自前実装が煩雑 |
| 申込フォーム | `form` `input` `select` | バリデーション表示込みなら有用 |
| 画像カルーセル | `carousel` | 実装次第 |

### 導入不要な要素

ヒーロー、コース紹介、料金表、講師プロフィール、生徒の声、アクセス、CTAボタン。
静的なテキスト・画像の配置のみで状態を持たないため、Tailwind直書きで足りる。

### 導入時の追加要件

shadcn/uiを入れる場合は以下が必要になる。

- TypeScript前提の構成（テンプレートを `react-ts` にする）
- パスエイリアス設定（`vite.config.ts` / `tsconfig.json`）
- `components.json` の生成
- Radix UI の依存追加

---

## 5. セットアップ手順

```bash
# プロジェクト作成
npm create vite@latest [project-name] -- --template react
# shadcn/ui を使う可能性があるなら react-ts を選択
# npm create vite@latest [project-name] -- --template react-ts

cd [project-name]
npm install

# Tailwind CSS v4
npm install tailwindcss @tailwindcss/vite
```

`vite.config.js` にプラグインを追加:

```js
import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
  plugins: [react(), tailwindcss()],
})
```

CSSファイルの先頭に追記:

```css
@import "tailwindcss";
```

> **注意**: Tailwind v3とv4で設定方法が異なる。`tailwind.config.js` の作成を指示している記事はv3向けなので参照しないこと。v4ではPostCSS設定・初期configファイルは不要。

---

## 6. 開発コマンド

| コマンド | 用途 |
|---|---|
| `npm run dev` | 開発サーバー起動（localhost:5173） |
| `npm run build` | `dist/` に本番ファイル出力 |
| `npm run preview` | ビルド結果をローカルで確認 |

---

## 7. 未確定事項

デプロイ前に決定が必要な項目。

- [ ] 問い合わせ・体験申込フォームの有無と実装方式
  - サーバー処理が必要な場合は **Pages Functions**（`functions/` ディレクトリ、無料枠 10万リクエスト/日）
  - 実行環境はNode.jsではなくWorkers runtime。ファイルシステムなし、一部npmパッケージが使用不可
  - メール送信はSendGrid / Resend等の外部API経由
  - 外部フォームサービス（Googleフォーム等）で代替する選択肢もあり
- [ ] TypeScriptの採用可否
- [ ] FAQアコーディオン・モーダルの有無（shadcn/ui導入判断に直結）
- [ ] 独自ドメインの取得先とレジストラ
- [ ] 納品後の保守契約の有無
- [ ] 想定PV・アクセス規模

---

## 8. 納品フロー（想定）

1. ローカルで開発・完成
2. クライアントにCloudflareアカウントを作成してもらう（無料・クレカ不要）
3. クライアント名義で独自ドメインを取得してもらう
4. Cloudflare管理画面: Workers & Pages → Create → Git接続 または Direct Upload
5. ビルド設定を入力（`npm run build` / `dist`）
6. Custom domains タブから独自ドメインを追加
   - ドメインがCloudflare管理なら数クリック
   - 他社管理ならCNAMEレコードを1本追加
7. 保守を継続する場合、クライアントアカウントに制作者を招待してもらう

---

## 9. 注意事項

- 本ドキュメントの各種数値・仕様は2026年5月時点の情報に基づく。契約前に以下の現行版を確認すること。
  - Cloudflare Self-Serve Subscription Agreement
  - Cloudflare Pages の無料枠制限（公式ドキュメント）
  - Tailwind CSS / shadcn/ui の公式セットアップ手順
- Next.jsのSSR/ISRを使う構成に変更する場合、Cloudflare Pagesでは `@cloudflare/next-on-pages` アダプタが必要になり、対応機能に制約が出る。今回は静的サイトのため該当しない。
