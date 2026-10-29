# GarageBook 車庫手帳

完整前後端分離的愛車保養記錄平台：多車輛管理、保養與加油紀錄、自動油耗計算、花費統計圖表、保養提醒、車輛照片上傳。

- **前端**：Vue 3 + Vite + Tailwind CSS 4 + Pinia + Vue Router + Chart.js
- **後端**：Laravel 13 + MySQL 8.4 + Sanctum（Token 驗證）
- **專案位置**：`C:\Users\uwu\garagebook`

---

## 快速開始

1. 雙擊 **`start.cmd`**
   - 會自動啟動 MySQL、Laravel API、Vue 前端，並開啟瀏覽器
2. 瀏覽器網址：<http://127.0.0.1:5173>
3. 示範帳號（登入頁已自動填入）：

   | 項目 | 內容 |
   |---|---|
   | Email | `demo@garagebook.test` |
   | 密碼 | `password123` |

4. 要關閉服務，雙擊 **`stop.cmd`**

> 這台電腦原本沒有 PHP / Node / MySQL。專案已內建可攜式版本在 `tools/`，不需要另外安裝，也不會更動系統環境。

---

## 服務與連接埠

| 服務 | 位址 |
|---|---|
| 前端（Vite） | http://127.0.0.1:5173 |
| API（Laravel） | http://127.0.0.1:8000 |
| MySQL | 127.0.0.1:3306（帳號 `root`，本機開發無密碼） |
| 車輛照片 | `backend/public/uploads/cars/` |

啟動後會開兩個 PowerShell 視窗，分別是 Laravel 與 Vite 的即時日誌，**關閉視窗即停止該服務**。

---

## phpMyAdmin

- 雙擊 **`start.cmd`** 會自動啟動 phpMyAdmin 並開啟 <http://127.0.0.1:8081>。
- 只開資料庫管理工具時，雙擊 **`database-tool.cmd`** 或 **`phpmyadmin.cmd`**。
- 登入資訊：主機 `127.0.0.1`、帳號 `root`、密碼留空。
- 登入後選擇左側的 `garagebook` 資料庫，即可瀏覽與管理資料表。
- phpMyAdmin 僅綁定本機位址；執行 **`stop.cmd`** 可一併停止。

## 手動啟動

```powershell
# 1) MySQL
C:\Users\uwu\garagebook\tools\mysql\bin\mysqld.exe --basedir=C:\Users\uwu\garagebook\tools\mysql --datadir=C:\Users\uwu\garagebook\tools\mysql-data --port=3306 --bind-address=127.0.0.1

# 2) 後端
cd C:\Users\uwu\garagebook\backend
C:\Users\uwu\garagebook\tools\php\php.exe artisan serve --host=127.0.0.1 --port=8000

# 3) 前端（另開一個終端）
cd C:\Users\uwu\garagebook\frontend
$env:Path = "C:\Users\uwu\garagebook\tools\node;" + $env:Path
npm run dev
```

---

## 功能清單

- 註冊 / 登入 / 登出（Laravel Sanctum Token）
- 車庫：新增、編輯、刪除、上傳車輛照片（JPG / PNG / WebP，最大 5MB）
- 保養紀錄：項目、日期、里程、花費、下次更換日期／里程、備註
- 加油紀錄：日期、里程、公升數、花費、每公升單價
- 平均油耗：以相鄰兩次加油的里程差 ÷ 加油量自動估算（km/L）
- 總覽：總花費、本月花費、平均油耗、近六個月堆疊長條圖、逾期／即將到期提醒
- 里程同步：新增紀錄時若里程高於車輛目前里程，會自動更新車輛里程
- 權限控管：每位使用者只能看到自己的車輛與紀錄（403 保護）

---

## 專案結構

```
garagebook/
├─ start.cmd / stop.cmd          # 一鍵啟動 / 停止
├─ start.ps1 / stop.ps1
├─ scripts/                      # 啟動腳本
├─ backend/                      # Laravel 13 API
│  ├─ app/Models                 # User, Car, MaintenanceRecord, FuelRecord
│  ├─ app/Http/Controllers       # Auth, Car, MaintenanceRecord, FuelRecord, Dashboard
│  ├─ app/Support/CarMetrics.php # 油耗與提醒計算
│  ├─ database/migrations/       # users, cars, maintenance_records, fuel_records, tokens
│  ├─ database/seeders/          # 示範資料
│  └─ routes/api.php
├─ frontend/                     # Vue 3 SPA
│  ├─ src/views/                 # Login, Register, Dashboard, Cars, CarDetail
│  ├─ src/components/            # Modal, StatCard, SpendChart, ReminderList, CarCard, 表單
│  ├─ src/stores/auth.js         # Pinia 登入狀態
│  ├─ src/api/http.js            # Axios + Token 攔截器
│  └─ src/assets/main.css        # Tailwind + 設計樣式
└─ tools/                        # 可攜式 PHP / Composer / Node / MySQL（勿刪）
```

---

## 資料表設計

| 資料表 | 說明 |
|---|---|
| `users` | 使用者（姓名、Email、密碼雜湊） |
| `cars` | 車輛：`user_id`、暱稱、廠牌、車型、年份、車牌、目前里程、燃料種類、照片 |
| `maintenance_records` | 保養：`car_id`、項目、日期、里程、花費、下次到期日／里程、備註 |
| `fuel_records` | 加油：`car_id`、日期、里程、公升數、花費、備註 |
| `personal_access_tokens` | Sanctum API Token |

關聯：`User 1—N Car 1—N MaintenanceRecord / FuelRecord`，刪除車輛會串聯刪除紀錄。

---

## API 一覽

需要登入的端點請帶上 `Authorization: Bearer <token>`。

| Method | Endpoint | 說明 |
|---|---|---|
| POST | `/api/auth/register` | 註冊 |
| POST | `/api/auth/login` | 登入（回傳 token） |
| GET | `/api/auth/me` | 目前使用者 |
| POST | `/api/auth/logout` | 登出（撤銷 token） |
| GET | `/api/stats` | 總覽統計（花費、油耗、六個月趨勢、提醒） |
| GET / POST | `/api/cars` | 車輛列表 / 新增 |
| GET / PUT / DELETE | `/api/cars/{id}` | 車輛詳情（含紀錄與統計）/ 更新 / 刪除 |
| POST / DELETE | `/api/cars/{id}/photo` | 上傳 / 移除車輛照片 |
| GET / POST | `/api/cars/{id}/maintenance` | 保養列表 / 新增 |
| PUT / DELETE | `/api/maintenance/{id}` | 更新 / 刪除保養 |
| GET / POST | `/api/cars/{id}/fuel` | 加油列表 / 新增 |
| PUT / DELETE | `/api/fuel/{id}` | 更新 / 刪除加油 |

---

## 重置資料庫（回到示範資料）

```powershell
cd C:\Users\uwu\garagebook\backend
C:\Users\uwu\garagebook\tools\php\php.exe artisan migrate:fresh --seed
```

---

## 常見問題

- **網頁打不開**：確認 `start.cmd` 開啟的兩個 PowerShell 視窗仍在執行（Laravel 與 Vite）。
- **連接埠被占用**：先執行 `stop.cmd`，或關閉佔用 3306 / 8000 / 5173 的程式。
- **圖片無法顯示**：確認後端正在執行；照片存於 `backend/public/uploads/cars/`。
- **要搬到其他位置**：整個 `garagebook` 資料夾搬走即可，啟動腳本使用相對路徑。
- **正式上線前**：改用正式 MySQL、設定強密碼、`APP_DEBUG=false`，並執行 `npm run build` 由伺服器提供前端檔案。

---

## 技術版本

PHP 8.3.36 · Composer 2.x · Node.js 24.22.1 · MySQL 8.4.13 · Laravel 13.38 · Sanctum 4.3 · Vue 3.5 · Vite 8.3 · Tailwind CSS 4.3 · Chart.js
