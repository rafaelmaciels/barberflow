#!/bin/bash
# Log all output
exec > /var/log/user-data.log 2>&1
set -x

# 1. Update and install basic dependencies
apt-get update -y
apt-get upgrade -y
apt-get install -y software-properties-common curl unzip git nginx mysql-server

# 2. Add PHP 8.3 repository
add-apt-repository ppa:ondrej/php -y
apt-get update -y

# 3. Install PHP 8.3 and extensions
apt-get install -y php8.3-fpm php8.3-cli php8.3-mysql php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-bcmath php8.3-intl

# 4. Install Composer
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 5. Install Node.js 20
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt-get install -y nodejs

# 6. Configure MySQL Database
systemctl start mysql
systemctl enable mysql

mysql -e "CREATE DATABASE barberflow;"
mysql -e "CREATE USER 'barberflow'@'localhost' IDENTIFIED BY 'BarberFlow123!';"
mysql -e "GRANT ALL PRIVILEGES ON barberflow.* TO 'barberflow'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

# 7. Setup Directory
mkdir -p /var/www/barberflow
chown -R ubuntu:ubuntu /var/www/barberflow
chmod -R 775 /var/www/barberflow

# 8. Configure Nginx
cat > /etc/nginx/sites-available/barberflow << 'EOF'
server {
    listen 80;
    listen [::]:80;
    server_name _;
    root /var/www/barberflow/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

ln -s /etc/nginx/sites-available/barberflow /etc/nginx/sites-enabled/
rm /etc/nginx/sites-enabled/default
systemctl restart nginx

# 9. Configure PHP settings (optional, increase upload limits if needed)
sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 50M/' /etc/php/8.3/fpm/php.ini
sed -i 's/post_max_size = 8M/post_max_size = 50M/' /etc/php/8.3/fpm/php.ini
systemctl restart php8.3-fpm

echo "Provisioning complete!"
