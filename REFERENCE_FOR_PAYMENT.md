# 🚀 QUICK REFERENCE: Test Mode → Production Mode

## Current Status: 🟢 TEST MODE

---

## TO GO LIVE - Update These 3 Things:

### 1️⃣ Open: `config/payment_config.php`

**Line 15:** Change mode
```php
define('PAYMENT_MODE', 'production');  // Change 'test' to 'production'
```

**Line 21:** Add production API key from Xendit Dashboard
```php
define('XENDIT_API_KEY_LIVE', 'xnd_production_YOUR_ACTUAL_KEY');
```

**Line 29:** Add webhook token from Xendit Dashboard
```php
define('XENDIT_WEBHOOK_TOKEN_LIVE', 'your_webhook_token_here');
```

**Lines 38-39:** Update URLs to your domain
```php
define('SUCCESS_URL_LIVE', 'https://yourdomain.com/auth/payment_success.html');
define('FAILURE_URL_LIVE', 'https://yourdomain.com/auth/payment_failed.html');
```

### 2️⃣ In Xendit Dashboard:

- Switch to **Live Mode** (toggle in dashboard)
- Copy **Production API Key** → Put in payment_config.php Line 21
- Add webhook URL: `https://yourdomain.com/webhook.php`
- Copy **Webhook Token** → Put in payment_config.php Line 29

### 3️⃣ Test First:

- Make a ₱1-10 test transaction
- Verify webhook fires (check logs)
- Confirm database updates correctly

---

## TO ROLLBACK - Emergency Revert:

Open `config/payment_config.php` → Line 15:
```php
define('PAYMENT_MODE', 'test');  // Switch back to test
```
Save and upload. Done! ✅

---

## Where Are My Keys?

**Xendit Dashboard:** https://dashboard.xendit.co/
- Settings → Developers → API Keys
- Settings → Developers → Webhooks

---

## What Happens Automatically:

 Webhook security automatically ENABLED in production  
 Correct API keys automatically selected  
 Correct redirect URLs automatically used  
 Warning logs if production misconfigured  

---

## Files Modified:

-  `config/payment_config.php` - Main configuration
-  `webhook.php` - Auto-enabled security
-  `php/create_payment.php` - Uses config
- `.gitignore` - Protects sensitive files

---

## Need Help?

📖 Full Guide: `PRODUCTION_DEPLOYMENT_GUIDE.md`  
🆘 Xendit Support: support@xendit.co  
📊 Dashboard: https://dashboard.xendit.co/

---

**Pro Tip:** Keep this file bookmarked for quick reference!
