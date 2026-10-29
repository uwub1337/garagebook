# GarageBook 車庫手帳

GarageBook 是一個前後端分離的車輛管理平台，用來記錄車輛、保養、加油、花費與保養提醒。

## 技術架構

- 前端：Vue 3、Vite、Tailwind CSS、Pinia、Vue Router、Chart.js
- 後端：Laravel 13、PHP 8.3+、Laravel Sanctum
- 開發資料庫：MySQL 8.4
- 部署：Docker、FrankenPHP、Caddy、Render

## 功能

- 使用者註冊、登入、登出與 Token 驗證
- 多車輛管理與車輛照片上傳
- 保養紀錄：項目、日期、里程、費用、下次到期日／里程
- 加油紀錄與平均油耗計算
- 總花費、本月花費、油耗與近六個月統計圖表
- 逾期／即將到期的保養提醒
- 使用者資料隔離，每位使用者只能存取自己的車輛與紀錄

## 本機快速開始

### 環境需求

- PHP 8.3 或以上
- Composer 2
- Node.js 20 或以上與 npm
- MySQL 8

### Windows 一鍵啟動

若已準備好本機依賴，可執行：

```text
start.cmd
```

前端網址：<http://127.0.0.1:5173>

停止所有服務：

```text
stop.cmd
```

### 手動啟動

先安裝依賴並建立環境設定：

```powershell
cd backend
composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate --seed
```

設定 `backend/.env` 的 MySQL 連線資訊後，分別啟動後端與前端：

```powershell
# 終端機 1：後端 API
cd backend
php artisan serve --host=127.0.0.1 --port=8000

# 終端機 2：前端
cd frontend
npm ci
npm run dev
```

API 預設網址：<http://127.0.0.1:8000>

## phpMyAdmin

本機開發可使用：

```text
database-tool.cmd
```

預設網址為 <http://127.0.0.1:8081>。請使用本機 MySQL 的帳號與密碼登入。

## 測試與建置

後端測試：

```powershell
cd backend
php artisan test --compact
```

前端 production build：

```powershell
cd frontend
npm ci
npm run build
```

GitHub Actions 會在 push 到 `main` 或建立 Pull Request 時，自動執行後端測試與前端建置。

## Docker／Render 部署

專案包含 `Dockerfile`、`docker/Caddyfile` 與 `render.yaml`，可在 Render 使用 Docker Web Service 部署。

部署前請確認：

1. GitHub repository 已連結到 Render。
2. `APP_KEY`、`APP_ENV`、`APP_DEBUG` 等環境變數已正確設定。
3. 正式環境應使用持久化資料庫。現有 `render.yaml` 預設使用 SQLite；Render 的本地檔案不適合保存重要正式資料，建議改用 MySQL 或 PostgreSQL。
4. `APP_DEBUG` 在正式環境必須設為 `false`。

Render 建置完成後，FrankenPHP 會以環境變數 `PORT` 啟動服務，並在啟動時執行資料庫 migration。

## 專案結構

```text
garagebook/
├── backend/                  # Laravel API
│   ├── app/                  # Models、Controllers、Services
│   ├── database/             # migrations、seeders
│   ├── routes/api.php        # API 路由
│   └── tests/                # PHPUnit 測試
├── frontend/                 # Vue SPA
│   └── src/                  # views、components、stores、API client
├── docker/                   # Caddy 與容器啟動腳本
├── scripts/                  # Windows 本機啟動腳本
├── .github/workflows/        # GitHub Actions CI
├── Dockerfile
└── render.yaml
```

## API 摘要

需要登入的 API 請帶上：

```http
Authorization: Bearer <token>
```

| Method | Endpoint | 說明 |
|---|---|---|
| POST | `/api/auth/register` | 註冊 |
| POST | `/api/auth/login` | 登入並取得 Token |
| GET | `/api/auth/me` | 取得目前使用者 |
| POST | `/api/auth/logout` | 登出並撤銷 Token |
| GET | `/api/stats` | 取得總覽統計 |
| GET / POST | `/api/cars` | 車輛列表／新增 |
| GET / PUT / DELETE | `/api/cars/{id}` | 車輛查詢／更新／刪除 |
| POST / DELETE | `/api/cars/{id}/photo` | 上傳／刪除車輛照片 |
| GET / POST | `/api/cars/{id}/maintenance` | 保養列表／新增 |
| PUT / DELETE | `/api/maintenance/{id}` | 更新／刪除保養紀錄 |
| GET / POST | `/api/cars/{id}/fuel` | 加油列表／新增 |
| PUT / DELETE | `/api/fuel/{id}` | 更新／刪除加油紀錄 |

## 重置資料庫

以下指令會刪除現有資料並重新建立示範資料，請勿在正式環境執行：

```powershell
cd backend
php artisan migrate:fresh --seed
```

## 常見問題

- 網頁打不開：確認 API 與 Vite 開發伺服器都正在執行。
- Port 被占用：停止使用 3306、8000 或 5173 的程序，或修改啟動設定。
- 圖片無法顯示：確認後端已執行 `php artisan storage:link)，並檢查上傳目錄權限。
- 新環境無法使用 `start.cmd`：確認 PHP、Composer、Node.js、npm 與 MySQL 已加入 PATH。

## 授權

本專案目前未另行指定公開授權條款。
