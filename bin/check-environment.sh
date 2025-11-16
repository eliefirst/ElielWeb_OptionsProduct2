#!/bin/bash
##
# ElielWeb OptionsProduct - Environment Check Script
#
# Vérifie que l'environnement est compatible avant installation
#
# Usage: bash check-environment.sh
##

set -e

echo "========================================================================"
echo "ElielWeb_OptionsProduct - Environment Compatibility Check"
echo "========================================================================"
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

ERRORS=0
WARNINGS=0

# Function to check command exists
check_command() {
    if command -v "$1" &> /dev/null; then
        echo -e "${GREEN}✓${NC} $1 is installed"
        return 0
    else
        echo -e "${RED}✗${NC} $1 is NOT installed"
        ((ERRORS++))
        return 1
    fi
}

# Function to check PHP version
check_php_version() {
    echo ""
    echo "Checking PHP version..."

    if ! check_command php; then
        return 1
    fi

    PHP_VERSION=$(php -r "echo PHP_VERSION;")
    PHP_MAJOR=$(php -r "echo PHP_MAJOR_VERSION;")
    PHP_MINOR=$(php -r "echo PHP_MINOR_VERSION;")

    echo "  Current PHP version: $PHP_VERSION"

    if [[ $PHP_MAJOR -ge 8 ]] && [[ $PHP_MINOR -ge 1 ]]; then
        echo -e "  ${GREEN}✓${NC} PHP version is compatible (8.1+)"
    else
        echo -e "  ${RED}✗${NC} PHP version must be 8.1 or higher"
        ((ERRORS++))
    fi
}

# Function to check Magento
check_magento() {
    echo ""
    echo "Checking Magento installation..."

    if [ ! -f "bin/magento" ]; then
        echo -e "${RED}✗${NC} bin/magento not found. Are you in the Magento root directory?"
        ((ERRORS++))
        return 1
    fi

    echo -e "${GREEN}✓${NC} Magento CLI found"

    # Get Magento version
    MAGENTO_VERSION=$(php bin/magento --version 2>/dev/null | grep -oP '\d+\.\d+\.\d+' | head -1)

    if [ -n "$MAGENTO_VERSION" ]; then
        echo "  Magento version: $MAGENTO_VERSION"

        MAJOR=$(echo $MAGENTO_VERSION | cut -d. -f1)
        MINOR=$(echo $MAGENTO_VERSION | cut -d. -f2)

        if [[ $MAJOR -eq 2 ]] && [[ $MINOR -ge 4 ]]; then
            echo -e "  ${GREEN}✓${NC} Magento version is compatible (2.4+)"
        else
            echo -e "  ${RED}✗${NC} Magento version must be 2.4 or higher"
            ((ERRORS++))
        fi
    else
        echo -e "  ${YELLOW}⚠${NC} Could not determine Magento version"
        ((WARNINGS++))
    fi
}

# Function to check MySQL/MariaDB
check_database() {
    echo ""
    echo "Checking database..."

    if ! check_command mysql; then
        return 1
    fi

    # Try to check if Aitoc tables exist
    echo "  Checking for Aitoc tables..."

    # Read database credentials from env.php
    if [ -f "app/etc/env.php" ]; then
        DB_NAME=$(php -r "include 'app/etc/env.php'; echo \$config['db']['connection']['default']['dbname'] ?? '';" 2>/dev/null)

        if [ -n "$DB_NAME" ]; then
            echo "  Database name: $DB_NAME"

            # Check for Aitoc tables
            AITOC_TABLES=$(mysql -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$DB_NAME' AND table_name LIKE 'aitoc_optionsmanagement%'" 2>/dev/null || echo "0")

            if [ "$AITOC_TABLES" -gt 0 ]; then
                echo -e "  ${GREEN}✓${NC} Found $AITOC_TABLES Aitoc tables"
            else
                echo -e "  ${YELLOW}⚠${NC} No Aitoc tables found (migration might not be needed)"
                ((WARNINGS++))
            fi
        fi
    fi
}

# Function to check required PHP extensions
check_php_extensions() {
    echo ""
    echo "Checking required PHP extensions..."

    REQUIRED_EXTENSIONS=(
        "pdo_mysql"
        "json"
        "bcmath"
        "ctype"
        "curl"
        "dom"
        "gd"
        "intl"
        "mbstring"
        "openssl"
        "simplexml"
        "soap"
        "spl"
        "zip"
    )

    for ext in "${REQUIRED_EXTENSIONS[@]}"; do
        if php -m | grep -qi "^$ext$"; then
            echo -e "  ${GREEN}✓${NC} $ext"
        else
            echo -e "  ${RED}✗${NC} $ext is missing"
            ((ERRORS++))
        fi
    done
}

# Function to check file permissions
check_permissions() {
    echo ""
    echo "Checking file permissions..."

    if [ -d "app/code" ]; then
        if [ -w "app/code" ]; then
            echo -e "${GREEN}✓${NC} app/code is writable"
        else
            echo -e "${RED}✗${NC} app/code is not writable"
            ((ERRORS++))
        fi
    else
        echo -e "${RED}✗${NC} app/code directory not found"
        ((ERRORS++))
    fi

    if [ -d "var" ]; then
        if [ -w "var" ]; then
            echo -e "${GREEN}✓${NC} var directory is writable"
        else
            echo -e "${RED}✗${NC} var directory is not writable"
            ((ERRORS++))
        fi
    fi

    if [ -d "generated" ]; then
        if [ -w "generated" ]; then
            echo -e "${GREEN}✓${NC} generated directory is writable"
        else
            echo -e "${RED}✗${NC} generated directory is not writable"
            ((ERRORS++))
        fi
    fi
}

# Function to check Hyva (optional)
check_hyva() {
    echo ""
    echo "Checking Hyva theme (optional)..."

    if [ -d "app/design/frontend/Hyva" ]; then
        echo -e "${GREEN}✓${NC} Hyva theme found"
    else
        echo -e "${YELLOW}⚠${NC} Hyva theme not found (module will work but Hyva features disabled)"
        ((WARNINGS++))
    fi
}

# Main execution
echo "Starting environment checks..."
echo ""

check_php_version
check_php_extensions
check_magento
check_database
check_permissions
check_hyva

echo ""
echo "========================================================================"
echo "Environment Check Summary"
echo "========================================================================"

if [ $ERRORS -eq 0 ]; then
    echo -e "${GREEN}✓ All critical checks passed!${NC}"
    echo ""
    echo "You can proceed with the installation."
    echo "See INSTALL.md for detailed installation instructions."

    if [ $WARNINGS -gt 0 ]; then
        echo ""
        echo -e "${YELLOW}⚠ $WARNINGS warning(s) found (non-critical)${NC}"
    fi

    exit 0
else
    echo -e "${RED}✗ $ERRORS error(s) found${NC}"

    if [ $WARNINGS -gt 0 ]; then
        echo -e "${YELLOW}⚠ $WARNINGS warning(s) found${NC}"
    fi

    echo ""
    echo "Please fix the errors above before proceeding with installation."
    exit 1
fi
