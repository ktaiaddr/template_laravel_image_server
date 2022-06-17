# Laravel9の開発用docker-composeリポジトリ

### docker環境立ち上げ
```bash
docker-compose up -d
 ```

### laravelインストール
```bash
docker exec -w /var/www/html $(docker ps -a | awk '{if($NF~/_laravel_/){print $NF}}') composer create-project --prefer-dist laravel/laravel:^9.0 laravel9
docker exec -w /var/www/html/laravel9 $(docker ps -a | awk '{if($NF~/_laravel_/){print $NF}}') composer require league/flysystem-sftp-v3 "^3.0"
docker exec $(docker ps -a | awk '{if($NF~/_laravel_/){print $NF}}') bash -c  "chmod -R 777 /var/www/html/laravel9"
```
### 画像アップロードテスト
```bash
echo $(node image.js) |  curl -i --insecure -XPOST -H "Content-Type: application/json; charset=sjis" -d @- http://localhost/api/image
```