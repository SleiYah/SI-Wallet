![SI-Wallet_ER](https://github.com/user-attachments/assets/4c91d968-55b7-4032-8088-c1647f37f08c)

Hosting Details: 
                IP: 51.44.8.213
                DNS: ec2-51-44-8-213.eu-west-3.compute.amazonaws.com
                Github Pages Urls: 
                Admin Side: https://sleiyah.github.io/si-wallet-admin/
                Client Side: https://sleiyah.github.io/si-wallet-client/

API Documentation:
SI Wallet API - Quick Reference
Authentication

Uses JWT tokens
Include in header: Authorization: Bearer <token>

Core Endpoints
Transactions
CopyPOST /transactions
Sample Request (Deposit):
jsonCopy{
  "wallet_id": 2,
  "amount": 100.00,
  "transaction_type": "deposit",
  "note": "Optional description"
}
Sample Request (P2P):
jsonCopy{
  "wallet_id": 2,
  "amount": 50.00,
  "transaction_type": "p2p",
  "to_wallet_id": 3,
  "recipient_username": "johndoe"
}
Key Rules:

Types: deposit, withdraw, p2p
P2P needs to_wallet_id and recipient_username
Scheduled P2P available with schedule_date parameter
Amount must be within user's transaction limit

Users
CopyPOST /users              # Create user
GET /users/{id}          # Get user details
PUT /users/{id}          # Update user
DELETE /users/{id}       # Delete user
Wallets
CopyPOST /wallets            # Create wallet
GET /wallets/{id}        # Get wallet
PUT /wallets/{id}        # Update wallet
DELETE /wallets/{id}     # Delete wallet
Other Resources

Verifications: Identity verification management
Tickets: Support ticket system
Scheduled Transactions: Future-dated transfers

Response Format
All responses follow this pattern:
jsonCopy{
  "success": true/false,
  "message": "Status description",

}
Common Errors

Insufficient balance
Transaction limit exceeded
Invalid recipient
Missing required fields
