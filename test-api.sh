#!/bin/bash
# Quick API Testing Script
# This script tests the registration and user endpoints

API_BASE="http://127.0.0.1:8000/api"
EMAIL="testuser$(date +%s)@example.com"
PASSWORD="password123"

echo "🧪 E-Library BRIDA - API Testing"
echo "=================================="
echo ""

# Test 1: Register new user
echo "1️⃣ Testing Registration Endpoint"
echo "Request: POST $API_BASE/register"
echo ""

REGISTER_RESPONSE=$(curl -s -X POST "$API_BASE/register" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{
    \"name\": \"Test User $(date +%s)\",
    \"email\": \"$EMAIL\",
    \"institution\": \"Test Institution\",
    \"password\": \"$PASSWORD\",
    \"password_confirmation\": \"$PASSWORD\"
  }")

echo "Response:"
echo "$REGISTER_RESPONSE" | jq '.' 2>/dev/null || echo "$REGISTER_RESPONSE"
echo ""

# Extract token from response
TOKEN=$(echo "$REGISTER_RESPONSE" | jq -r '.token' 2>/dev/null)

if [ "$TOKEN" != "null" ] && [ -n "$TOKEN" ]; then
    echo "✅ Registration successful!"
    echo "Token: $TOKEN"
    echo "User Email: $EMAIL"
    echo ""
    
    # Test 2: Get all users (requires authentication)
    echo "2️⃣ Testing Get Users Endpoint"
    echo "Request: GET $API_BASE/users"
    echo ""
    
    USERS_RESPONSE=$(curl -s -X GET "$API_BASE/users" \
      -H "Authorization: Bearer $TOKEN" \
      -H "Accept: application/json")
    
    echo "Response:"
    echo "$USERS_RESPONSE" | jq '.' 2>/dev/null || echo "$USERS_RESPONSE"
    echo ""
    
    # Check if new user is in the list
    NEW_USER_FOUND=$(echo "$USERS_RESPONSE" | jq --arg email "$EMAIL" '.data[] | select(.email == $email)' 2>/dev/null)
    
    if [ -n "$NEW_USER_FOUND" ]; then
        echo "✅ New user found in users list!"
        echo "$NEW_USER_FOUND" | jq '.'
    else
        echo "❌ New user NOT found in users list"
    fi
else
    echo "❌ Registration failed!"
fi

echo ""
echo "=================================="
echo "Testing Complete!"
