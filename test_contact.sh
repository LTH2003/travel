#!/bin/bash
# Test contact API with curl

echo "=== Testing Contact API ==="
echo ""

# Get token from first user
TOKEN="7|TOheeyfQAgpmp5eIFGiTTbUYl1yiIjlg7v757nJad6c90966"
USER_NAME="Admin User"

echo "User: $USER_NAME"
echo "Token: ${TOKEN:0:30}..."
echo ""

# Test with subject and message only (as required by new API)
echo "Sending test contact..."
curl -X POST http://127.0.0.1:8000/api/contacts \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "subject": "Test from Bash Script",
    "message": "This is a test message sent from bash curl command to verify API is working"
  }' \
  -w "\n\nStatus: %{http_code}\n"

echo ""
echo "=== Checking database for all contacts ==="
php -r "
require 'client/backend/vendor/autoload.php';
require 'client/backend/bootstrap/app.php';
\$app = require 'client/backend/bootstrap/app.php';
"
