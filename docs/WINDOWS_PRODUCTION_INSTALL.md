# Moto Inventory Windows 正式環境安裝手冊

## 1. 適用情境

本文件適用於以下情境：

- 正式環境為 Windows Server 或 Windows 10/11 專用主機
- 系統使用 MySQL 8
- 專案為 Laravel + Blade + Vite 架構
- 站台準備使用 Apache 或 IIS 對外提供服務

---

## 2. 版本注意事項

目前專案文件多處仍寫為 Laravel 12，但實際專案依賴請以 Composer 為準：

- `composer.json`: `laravel/framework: ^13.8`
- `composer.lock`: `laravel/framework: v13.14.0`
- `php`: `^8.3`

正式環境請依實際相依版本準備：

- PHP 8.3
- MySQL 8
- Composer 2
- Node.js 22+
- npm 10+

---

## 3. 建議正式環境架構

### 最低建議

- Windows Server 2019 / 2022，或穩定維護中的 Windows 11
- PHP 8.3 x64
- MySQL 8
- Composer 2
- Node.js 22 LTS
- Web Server：
  - Apache 2.4，或
  - IIS 10

### 建議站台目錄

假設專案放在：

```text
C:\Sites\moto-inventory
```

網站對外根目錄必須指向：

```text
C:\Sites\moto-inventory\public
```

不要把網站根目錄直接指向專案根目錄，否則 `.env`、`vendor`、`storage` 等敏感檔案可能暴露。

---

## 4. 安裝前準備

請先確認正式環境已安裝：

1. Git
2. PHP 8.3
3. Composer
4. Node.js 與 npm
5. MySQL 8
6. Apache 或 IIS

可用以下指令確認版本：

```powershell
php -v
composer -V
node -v
npm -v
git --version
```

---

## 5. 取得專案

```powershell
cd C:\Sites
git clone https://github.com/nibarsu/moto-inventory.git
cd moto-inventory
```

如果正式機不允許直接從 GitHub 拉程式，也可以先在開發機打包後再部署。

---

## 6. 安裝 PHP 套件

```powershell
composer install --no-dev --optimize-autoloader
```

正式環境建議使用：

- `--no-dev`
- `--optimize-autoloader`

這樣可避免安裝測試與開發依賴，並改善自動載入效率。

---

## 7. 建立正式環境 `.env`

請由專案根目錄複製：

```powershell
Copy-Item .env.production.example .env
```

如果沒有 `.env.production.example`，也可以用：

```powershell
Copy-Item .env.example .env
```

之後再依正式環境修改以下項目：

- `APP_NAME`
- `APP_ENV`
- `APP_DEBUG`
- `APP_URL`
- `DB_*`
- `MAIL_*`
- `SESSION_*`
- `CACHE_*`
- `QUEUE_*`

本專案已另外提供正式環境樣板：

- [/.env.production.example](/c:/laragon/www/moto-inventory/.env.production.example)

---

## 8. 產生 Laravel APP_KEY

```powershell
php artisan key:generate
```

如果 `.env` 已有正式環境固定金鑰，則不需重跑。

---

## 9. 建立資料庫

請先在 MySQL 建立正式資料庫，例如：

```sql
CREATE DATABASE moto_inventory
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

`.env` 範例：

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=moto_inventory
DB_USERNAME=moto_user
DB_PASSWORD=StrongPasswordHere
```

---

## 10. 執行資料庫 Migration

```powershell
php artisan migrate --force
```

正式環境必須加上 `--force`，避免互動式確認中斷部署。

---

## 11. 建立 Storage Link

```powershell
php artisan storage:link
```

如果系統有上傳檔、匯入匯出檔、條碼或報表檔案，這一步需要保留。

---

## 12. 安裝與編譯前端資產

如果正式環境需要自行編譯前端：

```powershell
npm install
npm run build
```

若部署包中已包含最新 `public/build`，正式機可不重新編譯，但前提是：

1. `public/build` 已完整部署
2. 編譯結果與目前程式版本一致

只要有改 Blade、Tailwind class、`resources/css`、`resources/js`、Vite 設定，正式版部署前都應重新確認 build 結果。

---

## 13. Laravel 正式化最佳化

```powershell
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

如果之後修改 `.env`、route 或 Blade，需重新整理快取：

```powershell
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 14. Windows 權限設定

Laravel 必須可寫入以下目錄：

- `storage`
- `bootstrap\cache`

如果執行身分是 IIS App Pool、Apache Service 或自訂 Windows 使用者，需授與修改權限。

範例：

```powershell
icacls C:\Sites\moto-inventory\storage /grant Everyone:(OI)(CI)M /T
icacls C:\Sites\moto-inventory\bootstrap\cache /grant Everyone:(OI)(CI)M /T
```

正式環境不建議長期使用 `Everyone`。較佳做法是授權給實際執行網站服務的帳號，例如：

- `IIS AppPool\YourSiteAppPool`
- `NETWORK SERVICE`
- Apache Windows Service 使用帳號

---

## 15. Apache 設定重點

### VirtualHost 範例

```apache
<VirtualHost *:80>
    ServerName moto-inventory.example.com
    DocumentRoot "C:/Sites/moto-inventory/public"

    <Directory "C:/Sites/moto-inventory/public">
        AllowOverride All
        Require all granted
        Options Indexes FollowSymLinks
    </Directory>

    ErrorLog "logs/moto-inventory-error.log"
    CustomLog "logs/moto-inventory-access.log" common
</VirtualHost>
```

### Apache 必要模組

請確認至少啟用：

- `mod_rewrite`
- `mod_headers`

Laravel 需要 URL Rewrite，否則路由可能全部變成 404。

---

## 16. IIS 詳細設定

如果正式環境使用 IIS，建議依下列步驟設定。

### 16.1 安裝必要元件

請確認 IIS 已安裝以下功能：

1. Web Server (IIS)
2. CGI
3. URL Rewrite Module
4. FastCGI

若未安裝 URL Rewrite，Laravel 的路由通常無法正常運作。

### 16.2 建立網站

在 IIS Manager：

1. 新增 Site
2. Site name：`Moto Inventory`
3. Physical path 指向：

```text
C:\Sites\moto-inventory\public
```

4. Binding 設定正式網址，例如：
   - Type: `http` 或 `https`
   - Host name: `moto-inventory.example.com`

### 16.3 設定 PHP FastCGI

若尚未設定 PHP：

1. 打開 IIS Manager
2. 點選伺服器層級的 `Handler Mappings`
3. 確認已有 `.php` 對應到 FastCGI
4. PHP 執行檔例如：

```text
C:\php\php-cgi.exe
```

### 16.4 設定預設首頁

Laravel 實際入口是 `public\index.php`，網站根目錄只要正確指向 `public`，通常不需額外修改 Default Document。

### 16.5 建立 `web.config`

`public\web.config` 可使用以下內容：

```xml
<?xml version="1.0" encoding="UTF-8"?>
<configuration>
    <system.webServer>
        <rewrite>
            <rules>
                <rule name="Laravel" stopProcessing="true">
                    <match url="^(.*)$" ignoreCase="false" />
                    <conditions logicalGrouping="MatchAll">
                        <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
                        <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />
                    </conditions>
                    <action type="Rewrite" url="index.php/{R:1}" appendQueryString="true" />
                </rule>
            </rules>
        </rewrite>
        <defaultDocument>
            <files>
                <clear />
                <add value="index.php" />
            </files>
        </defaultDocument>
    </system.webServer>
</configuration>
```

### 16.6 App Pool 建議

IIS Application Pool 建議：

- `.NET CLR version`: `No Managed Code`
- `Managed pipeline mode`: `Integrated`
- `Enable 32-Bit Applications`: `False`（若 PHP 為 x64）

### 16.7 權限設定

請將以下目錄的寫入權限授與 App Pool 帳號：

- `storage`
- `bootstrap\cache`

例如 App Pool 名稱為 `MotoInventoryPool`：

```powershell
icacls C:\Sites\moto-inventory\storage /grant "IIS AppPool\MotoInventoryPool":(OI)(CI)M /T
icacls C:\Sites\moto-inventory\bootstrap\cache /grant "IIS AppPool\MotoInventoryPool":(OI)(CI)M /T
```

### 16.8 IIS 常見問題

#### 404

通常原因：

- Site path 沒有指向 `public`
- URL Rewrite 未安裝
- `web.config` 缺失或規則錯誤

#### 500.19

通常原因：

- `web.config` 格式錯誤
- IIS 未安裝 Rewrite Module

#### 502.3 / FastCGI 錯誤

通常原因：

- PHP FastCGI 路徑錯誤
- PHP extension 缺漏
- PHP 權限不足

---

## 17. 首次管理員帳號處理

本專案已有角色與權限模組。

請注意：

- migration 會建立 `admin` 角色
- migration 會把「當下已存在的使用者」指派給 `admin`
- 如果你是在 migration 完成後才註冊第一位使用者，該使用者不會自動取得 `admin`

### 方式一：直接用 SQL 指派

先查詢角色：

```sql
SELECT id, code, name FROM roles;
```

再查詢使用者：

```sql
SELECT id, name, email FROM users;
```

假設：

- `admin` 角色 id = 1
- 使用者 id = 1

則可執行：

```sql
INSERT INTO role_user (role_id, user_id, created_at, updated_at)
VALUES (1, 1, NOW(), NOW());
```

### 方式二：使用 Tinker

```powershell
php artisan tinker
```

```php
$user = App\Models\User::find(1);
$role = App\Models\Role::where('code', 'admin')->first();
$user->roles()->syncWithoutDetaching([$role->id]);
```

---

## 18. 正式環境驗證清單

部署完成後，請至少驗證以下項目：

1. 首頁可開啟
2. 可以登入
3. Dashboard 可正常顯示
4. 選單權限正確
5. `storage` 可寫入
6. 資料可新增 / 編輯 / 刪除
7. 上傳、匯入、匯出、條碼功能可正常執行
8. `APP_DEBUG=false`
9. 正式網址與 `APP_URL` 一致
10. `php artisan route:list` 在正式環境可正常執行

---

## 19. 更新部署流程

正式環境更新建議流程：

```powershell
cd C:\Sites\moto-inventory
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
npm run build
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

如果正式機不編譯前端，則需確認新版 `public/build` 已一併部署。

---

## 20. 備份建議

正式環境至少備份以下項目：

1. MySQL 資料庫
2. `.env`
3. `storage\app`
4. 匯入 / 匯出 / 條碼或其他上傳檔

部署前建議先做：

- 資料庫備份
- 專案目錄快照

---

## 21. 常見問題

### Q1. 可以直接把 Laragon 專案資料夾整包複製到正式機嗎？

可以，但正式機仍需重新確認：

- `.env`
- 網站根目錄是否指向 `public`
- `storage` / `bootstrap\cache` 權限
- Composer 套件
- 前端 build 結果

### Q2. 正式環境一定要安裝 Node.js 嗎？

不一定。

如果你從開發機一起部署最新 `public/build`，正式機可以不編譯前端。但只要正式機要自行執行 `npm run build`，就必須安裝 Node.js。

### Q3. 為什麼登入後看不到功能選單？

通常是因為使用者尚未被指派角色或權限。請先確認是否已分配 `admin` 或其他對應角色。

---

## 22. 建議後續改善

為了讓正式環境部署更穩定，建議後續可補強：

1. 建立正式環境專用 deployment checklist
2. 建立資料庫定期備份腳本
3. 建立 Windows Task Scheduler 來執行：
   - `php artisan schedule:run`
4. 若未來有 queue，建立常駐 worker 或排程監控
5. 補一份 IIS 專用部署截圖版文件，讓非工程人員也能依步驟操作
