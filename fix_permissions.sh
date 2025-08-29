
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
