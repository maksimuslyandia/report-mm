
## Deployment
### Need to unzip
```
cp .env.example .env

cd public 
unzip fontawesome-free-6.7.2-web.zip
unzip bootstrap-icons-1.11.3.zip
unzip bootstrap-5.3.5-dist.zip
php artisan key:generate
```
### Create database
#### local connect
```
create database netx;
CREATE USER 'netx'@'localhost' IDENTIFIED BY 'netx_pass';
GRANT ALL PRIVILEGES ON netx.* TO netx@localhost;
FLUSH PRIVILEGES;
```
#### remote connect
```
# create database netx;
CREATE USER 'netx'@'%' IDENTIFIED BY 'netx_pass';
GRANT ALL PRIVILEGES ON netx.* TO 'netx'@'%';
FLUSH PRIVILEGES;
```

#### Permission check fix_permissions.sh
```
nano fix_permissions.sh
```
```
#!/bin/bash

# Set ownership to wb570841 and group to www-data
echo "Setting ow[README.md](README.md)nership to 'wb570841:www-data'..."
sudo chown -R www-data:www-data /var/www/html/php/ntools
# Set permissions for directories to 755
echo "Setting directory permissions to 755..."
sudo find /var/www/html/php/ntools -type d -exec chmod 755 {} \;
# Set permissions for files to 644
echo "Setting file permissions to 644..."
sudo find /var/www/html/php/ntools -type f -exec chmod 644 {} \;
# Ensure the storage and bootstrap/cache directories are writable
echo "Setting permissions for 'storage' and 'bootstrap/cache' to 775..."
sudo chmod -R 775 /var/www/html/php/reprt-mm/storage
sudo chmod -R 775 /var/www/html/php/reprt-mm/bootstrap/cache
# Find executable files (for debugging, optional)
echo "Listing executable files..."
sudo find /var/www/html/php/ntools -type f -executable
# Reapply writable permissions to storage as a safety measure
sudo chmod -R 775 /var/www/html/php/reprt-mm/storage
echo "Permissions and ownership successfully updated!"
```
#### Make it executable
```
chmod +x fix_permissions.sh
./fix_permissions.sh
```
#### Clear other
```
chmod +x fix_permissions.sh
./fix_permissions.sh
php artisan optimize
php artisan cache:clear
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan optimize

php artisan config:cache
php artisan config:clear
```
#### or
```
sudo chown -R www-data:www-data /var/www/html/php/netexplorer-lab/storage
sudo chmod -R 775 /var/www/html/php/netexplorer-lab/storage

sudo chown -R www-data:www-data /var/www/html/ntools-app/storage
sudo chmod -R 775 /var/www/html/ntools-app/storage

```



```
php artisan migrate:fresh --seed
```








