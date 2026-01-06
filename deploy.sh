#!/bin/bash

# Laravel Admin Panel Deployment Script
# Run this script on your VPS server after uploading your files

set -e

echo "🚀 Starting Laravel Admin Panel Deployment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if running as root or with sudo
if [ "$EUID" -ne 0 ]; then
    echo -e "${RED}Please run as root or with sudo${NC}"
    exit 1
fi

# Get the application directory
APP_DIR="/var/www/admin-panel"
if [ ! -d "$APP_DIR" ]; then
    echo -e "${RED}Application directory not found at $APP_DIR${NC}"
    exit 1
fi

cd $APP_DIR

echo -e "${GREEN}✓${NC} Application directory found"

# Step 1: Install Composer dependencies
echo -e "${YELLOW}Installing Composer dependencies...${NC}"
if [ -f "composer.json" ]; then
    composer install --optimize-autoloader --no-dev --no-interaction
    echo -e "${GREEN}✓${NC} Composer dependencies installed"
else
    echo -e "${RED}✗ composer.json not found${NC}"
    exit 1
fi

# Step 2: Install NPM dependencies and build assets
echo -e "${YELLOW}Installing NPM dependencies and building assets...${NC}"
if [ -f "package.json" ]; then
    npm install --production
    npm run production
    echo -e "${GREEN}✓${NC} Assets built"
else
    echo -e "${YELLOW}⚠ package.json not found, skipping NPM build${NC}"
fi

# Step 3: Set up .env file if it doesn't exist
if [ ! -f ".env" ]; then
    echo -e "${YELLOW}Creating .env file from .env.example...${NC}"
    if [ -f ".env.example" ]; then
        cp .env.example .env
        echo -e "${GREEN}✓${NC} .env file created"
        echo -e "${YELLOW}⚠ Please edit .env file with your configuration${NC}"
    else
        echo -e "${RED}✗ .env.example not found${NC}"
    fi
fi

# Step 4: Generate application key if not set
echo -e "${YELLOW}Checking application key...${NC}"
if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    php artisan key:generate --force
    echo -e "${GREEN}✓${NC} Application key generated"
else
    echo -e "${GREEN}✓${NC} Application key already exists"
fi

# Step 5: Set file permissions
echo -e "${YELLOW}Setting file permissions...${NC}"
chown -R www-data:www-data $APP_DIR
find $APP_DIR -type d -exec chmod 755 {} \;
find $APP_DIR -type f -exec chmod 644 {} \;
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
echo -e "${GREEN}✓${NC} Permissions set"

# Step 6: Run database migrations
echo -e "${YELLOW}Running database migrations...${NC}"
read -p "Do you want to run migrations? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan migrate --force
    echo -e "${GREEN}✓${NC} Migrations completed"
else
    echo -e "${YELLOW}⚠ Skipping migrations${NC}"
fi

# Step 7: Clear and cache configuration
echo -e "${YELLOW}Optimizing application...${NC}"
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache
echo -e "${GREEN}✓${NC} Application optimized"

# Step 8: Check PHP version
PHP_VERSION=$(php -r 'echo PHP_VERSION;')
echo -e "${GREEN}✓${NC} PHP Version: $PHP_VERSION"

# Step 9: Verify critical files
echo -e "${YELLOW}Verifying critical files...${NC}"
if [ -f "public/index.php" ]; then
    echo -e "${GREEN}✓${NC} public/index.php exists"
else
    echo -e "${RED}✗ public/index.php not found${NC}"
fi

if [ -d "storage" ]; then
    echo -e "${GREEN}✓${NC} storage directory exists"
else
    echo -e "${RED}✗ storage directory not found${NC}"
fi

if [ -d "bootstrap/cache" ]; then
    echo -e "${GREEN}✓${NC} bootstrap/cache directory exists"
else
    echo -e "${RED}✗ bootstrap/cache directory not found${NC}"
fi

echo ""
echo -e "${GREEN}✅ Deployment completed!${NC}"
echo ""
echo "Next steps:"
echo "1. Edit .env file with your configuration"
echo "2. Configure Nginx/Apache web server"
echo "3. Set up SSL certificate (Let's Encrypt)"
echo "4. Configure firewall"
echo "5. Set up cron jobs"
echo ""
echo "For detailed instructions, see DEPLOYMENT.md"

