#!/bin/bash
# Log execution
exec > >(tee /var/log/user-data.log|logger -t user-data -s 2>/dev/console) 2>&1
echo "Starting Barberflow installation..."

# Update and install dependencies
export DEBIAN_FRONTEND=noninteractive
apt-get update
apt-get install -y nginx git unzip sqlite3 libsqlite3-dev curl software-properties-common

# Add PHP 8.4 repository
add-apt-repository -y ppa:ondrej/php
apt-get update
apt-get install -y php8.4-fpm php8.4-cli php8.4-sqlite3 php8.4-mbstring php8.4-xml php8.4-curl php8.4-zip php8.4-bcmath php8.4-intl

# Install Composer
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Node.js 22
curl -fsSL https://deb.nodesource.com/setup_22.x | bash -
apt-get install -y nodejs

# Clone the repository
mkdir -p /var/www/barberflow
git clone https://github.com/rafaelmaciels/barberflow.git /var/www/barberflow
cd /var/www/barberflow

# Configure Environment
cp .env.example .env
# Set APP_URL for Nginx (will use IP or domain later, but localhost is fine for now)
sed -i 's/APP_URL=http:\/\/localhost/APP_URL=http:\/\/*/g' .env
# Ensure SQLite is used
sed -i 's/DB_CONNECTION=mysql/DB_CONNECTION=sqlite/g' .env

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Generate Key and Migrate Database
php artisan key:generate
touch database/database.sqlite
php artisan migrate --force

# Set permissions
chown -R www-data:www-data /var/www/barberflow
chmod -R 775 /var/www/barberflow/storage
chmod -R 775 /var/www/barberflow/bootstrap/cache
chmod 664 /var/www/barberflow/database/database.sqlite
chown www-data:www-data /var/www/barberflow/database
chown www-data:www-data /var/www/barberflow/database/database.sqlite

# Configure Nginx
cat << 'EOF' > /etc/nginx/sites-available/barberflow
server {
    listen 80;
    server_name _;
    root /var/www/barberflow/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.php index.html index.htm;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

ln -s /etc/nginx/sites-available/barberflow /etc/nginx/sites-enabled/
rm /etc/nginx/sites-enabled/default

# Restart services
systemctl restart php8.4-fpm
systemctl restart nginx

echo "Installation complete!"
