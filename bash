#!/bin/bash

# This script must be executed with root privileges
# It changes the PostgreSQL password for a user schema

if [ "$EUID" -ne 0 ]; then
  echo "This script must be run as root"
  exit 1
fi

# Check if schema name is provided
if [ -z "$1" ]; then
  echo "Usage: $0 <schema_name>"
  exit 1
fi

SCHEMA_NAME=$1
# Using usr_ prefix instead of pg_ to avoid reserved name error
PG_USERNAME="usr_$(echo $SCHEMA_NAME | tr -cd 'a-z0-9_')"
NEW_PASSWORD=$(openssl rand -base64 24)

# Reset the password in PostgreSQL
sudo -u postgres psql -c "ALTER ROLE ${PG_USERNAME} WITH PASSWORD '${NEW_PASSWORD}';"

if [ $? -eq 0 ]; then
  echo "Password has been reset for ${PG_USERNAME}"
  echo "New temporary password: ${NEW_PASSWORD}"
  echo ""
  echo "IMPORTANT:"
  echo "1. Provide this temporary password to the user"
  echo "2. Instruct the user to log in and change their password immediately"
  echo "3. Once the user changes their password, the schema credentials will be regenerated securely"
else
  echo "Error resetting password"
  exit 1
fi