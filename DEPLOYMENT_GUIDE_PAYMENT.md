# 🚀 Production Deployment Guide
## Moving Your Payment System from Test to Live Mode

---

## ⚠️ PREREQUISITES CHECKLIST

Before going live, ensure you have:

- [ ] ✅ Completed Xendit business verification
- [ ] ✅ Valid SSL certificate installed (HTTPS)
- [ ] ✅ Production domain name ready
- [ ] ✅ Database backup completed
- [ ] ✅ Tested thoroughly in test mode
- [ ] ✅ Support system ready for payment issues

---

## 📋 STEP-BY-STEP DEPLOYMENT INSTRUCTIONS

### **Step 1: Get Xendit Production Credentials**

1. Log in to [Xendit Dashboard](https://dashboard.xendit.co/)
2. Switch from **Test Mode** to **Live Mode** (toggle in top-right corner)
3. Go to **Settings → Developers → API Keys**
4. Copy your **Live Secret API Key** (starts with `xnd_production_...`)
   - ⚠️ Keep this secure! Never share or commit to Git
5. Go to **Settings → Developers → Callbacks**
   - ⚠️ Xendit calls these "Callbacks", not "Webhooks" — it's the same thing
6. Find the **Invoice paid** section and click **Edit**
7. Enter your production callback URL: `https://yourdomain.com/webhook.php`
   - Replace `yourdomain.com` with your actual Hostinger domain
8. Click **Save**
9. Copy the **Callback Verification Token** shown on this page



---

### **Step 2: Update Configuration File**

Open `config/payment_config.php` and update the following:

#### 2.1 Set Payment Mode to Production
```php
// Line 15: Change from 'test' to 'production'
define('PAYMENT_MODE', 'production');  // ✅ Change this
```

#### 2.2 Add Production API Key
```php
// Line 21: Replace with your actual production key
define('XENDIT_API_KEY_LIVE', 'xnd_production_YOUR_ACTUAL_KEY_HERE');
```

#### 2.3 Add Production Webhook Token
```php
// Line 29: Replace with your actual webhook token
define('XENDIT_WEBHOOK_TOKEN_LIVE', 'your_actual_webhook_token_from_xendit');
```

#### 2.4 Update Redirect URLs
```php
// Lines 38-39: Replace with your actual domain
define('SUCCESS_URL_LIVE', 'https://yourdomain.com/payment_success.html');
define('FAILURE_URL_LIVE', 'https://yourdomain.com/payment_failed.html');
```

**Example of completed configuration:**
```php
// define('PAYMENT_MODE', 'production');
define('XENDIT_API_KEY_LIVE', 'xnd_production_ABC123XYZ789...');
// define('XENDIT_WEBHOOK_TOKEN_LIVE', 'webhook_token_ABC123XYZ789...');
// define('SUCCESS_URL_LIVE', 'https://psiexam.com/payment_success.html');
// define('FAILURE_URL_LIVE', 'https://psiexam.com/payment_failed.html');
```

---

### **Step 3: Update Database Configuration (if needed)**

If you're deploying to a different server, update `config/db.php`:

```php
$host = 'your_production_host';      // e.g., 'localhost' or '127.0.0.1'
$db   = 'your_production_database';  // e.g., 'psi_production'
$user = 'your_production_user';      // Database username
$pass = 'your_production_password';  // Database password
```

---

### **Step 4: Security Setup (Hostinger)**

> ⚠️ **Why this matters:** Without these steps, someone could visit `yourdomain.com/config/db.php` in a browser and potentially see your database password or API keys. These steps prevent that.

---

#### 4.1 Upload the `.htaccess` Files

Two `.htaccess` files have already been created for you in this project:

- **`.htaccess`** (in the root folder) — blocks access to sensitive file types
- **`config/.htaccess`** (inside the config folder) — blocks ALL direct access to config files

**What you need to do:**
1. When uploading your project to Hostinger, make sure **both** `.htaccess` files are included
2. `.htaccess` files start with a dot (`.`) — your file manager or FTP client may hide them by default
3. In Hostinger's File Manager: click **Settings** or **Show hidden files** to see them
4. Verify they uploaded correctly — you should see:
   - `public_html/.htaccess`
   - `public_html/config/.htaccess`

> 💡 **How to check if it's working:** After uploading, open your browser and go to `https://yourdomain.com/config/db.php` — you should see a **403 Forbidden** error. That means it's working correctly.

---

#### 4.2 Set File Permissions on Hostinger

File permissions control who can read/write your files. On Hostinger:

1. Log in to **Hostinger hPanel**
2. Go to **Files → File Manager**
3. Navigate to `public_html/config/`
4. Right-click `payment_config.php` → **Change Permissions**
5. Set permissions to **640** (owner can read/write, no one else can)
6. Repeat for `db.php`

**What the numbers mean (simplified):**
| Permission | Meaning |
|---|---|
| `640` | Only the server (owner) can read it. Visitors cannot. ✅ |
| `644` | Everyone can read it. Avoid for config files. ❌ |
| `777` | Anyone can read AND write. Never use this. ❌ |

---

#### 4.3 Disable Error Display (Hostinger)

By default, PHP errors can show on-screen and reveal file paths or code to visitors.

**Option A — Via Hostinger hPanel (easiest):**
1. Go to **hPanel → Advanced → PHP Configuration**
2. Find `display_errors` and set it to **Off**
3. Find `log_errors` and set it to **On**
4. Click **Save**

**Option B — Already included in `.htaccess`:**
The `.htaccess` file created for you already contains:
```apache
php_flag display_errors off
php_flag log_errors on
```
This is automatically applied when you upload the `.htaccess` file.

---

#### 4.4 Verify Your Setup

After uploading everything, test these URLs in your browser — all should return **403 Forbidden**:

- `https://yourdomain.com/config/db.php` → ❌ 403 Forbidden ✅
- `https://yourdomain.com/config/payment_config.php` → ❌ 403 Forbidden ✅
- `https://yourdomain.com/config/` → ❌ 403 Forbidden ✅

If any of these loads without a 403 error, the `.htaccess` file is not in the right place or didn't upload correctly.

---

### **Step 5: Test Production Configuration**

Before going fully live, test with small amounts:

1. **Create a test registration** with a real email
2. **Process payment** using real payment method (₱1-10 test)
3. **Verify webhook triggers** - Check error logs:
   ```
   Should see: [PAYMENT CONFIG] Running in 🔴 LIVE MODE mode
   ```
4. **Check database updates:**
   - `payments` table: status should be 'PAID'
   - `examinees` table: status should be 'Scheduled'
   - `users` table: status should be 'active'
5. **Verify email notifications** are sent
6. **Test complete flow** from registration to successful payment

---

### **Step 6: Monitor Initial Transactions**

For the first 24-48 hours after going live:

1. **Monitor error logs regularly:**
   ```bash
   tail -f /path/to/error.log
   ```

2. **Check Xendit Dashboard:**
   - Go to **Transactions** → **Invoices**
   - Verify payments appear correctly
   - Check for any failed webhooks

3. **Verify Database Consistency:**
   ```sql
   -- Check if all PAID payments have corresponding Scheduled examinees
   SELECT p.payment_id, p.status, e.status 
   FROM payments p
   JOIN examinees e ON p.examinee_id = e.examinee_id
   WHERE p.status = 'PAID';
   ```

---

### **Step 7: Rollback Plan (If Issues Occur)**

If you encounter critical issues:

#### Quick Rollback Steps:
1. Open `config/payment_config.php`
2. Change line 15 back to:
   ```php
   define('PAYMENT_MODE', 'test');
   ```
3. Save and upload
4. System will immediately revert to test mode

---

## 🔒 SECURITY BEST PRACTICES

### Never Commit Sensitive Data
Add to `.gitignore`:
```
config/payment_config.php
config/db.php
.env
*.log
```

### Use Environment Variables (Recommended)
For enhanced security, consider using environment variables:

1. Install phpdotenv:
   ```bash
   composer require vlucas/phpdotenv
   ```

2. Create `.env` file (never commit this):
   ```
   XENDIT_API_KEY=xnd_production_YOUR_KEY
   XENDIT_WEBHOOK_TOKEN=your_webhook_token
   ```

3. Load in payment_config.php:
   ```php
   require_once __DIR__ . '/../vendor/autoload.php';
   $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
   $dotenv->load();
   
   define('XENDIT_API_KEY_LIVE', $_ENV['XENDIT_API_KEY']);
   ```

---

## 📊 POST-DEPLOYMENT MONITORING

### Daily Checks (First Week)
- [ ] Review error logs for webhook failures
- [ ] Check Xendit dashboard for payment status
- [ ] Verify all PAID payments updated database correctly
- [ ] Monitor customer support emails for payment issues

### Weekly Tasks
- [ ] Reconcile Xendit settlements with database records
- [ ] Review failed payment reasons
- [ ] Update documentation based on issues found

---

## 🆘 TROUBLESHOOTING COMMON ISSUES

### Issue: "Invalid callback token" in webhook logs
**Solution:** Verify `XENDIT_WEBHOOK_TOKEN_LIVE` in config matches Xendit dashboard

### Issue: Payments succeed but database not updating
**Solution:** 
1. Check webhook URL in Xendit is correct
2. Verify webhook.php is accessible (test: curl your webhook URL)
3. Check error logs for database errors

### Issue: SSL certificate errors
**Solution:** Ensure your domain has valid SSL certificate
```bash
# Test SSL
curl -I https://yourdomain.com/webhook.php
```

---

## 📞 SUPPORT CONTACTS

- **Xendit Support:** support@xendit.co
- **Developer Docs:** https://developers.xendit.co/
- **Dashboard:** https://dashboard.xendit.co/

---

## ✅ FINAL PRODUCTION CHECKLIST

Before announcing "go-live":

- [ ] PAYMENT_MODE set to 'production'
- [ ] Production API keys configured
- [ ] Webhook URL registered in Xendit
- [ ] Webhook verification enabled (automatic)
- [ ] SSL certificate active
- [ ] Test transaction completed successfully
- [ ] Redirect URLs updated to production domain
- [ ] Database backup completed
- [ ] Error logging configured
- [ ] Support team briefed and ready
- [ ] Rollback procedure tested
- [ ] Monitoring system in place

---

## 🎉 YOU'RE READY TO GO LIVE!

Once all items above are checked:

1. Make final announcement to team
2. Conduct one final test transaction
3. Monitor closely for first 2 hours
4. Celebrate! 🎊

**Remember:** You can always switch back to test mode instantly if needed by changing one line in `payment_config.php`.

---

**Last Updated:** February 28, 2026
**Configuration File Location:** `config/payment_config.php`
**Webhook Handler:** `webhook.php`
**Payment Creation:** `php/create_payment.php`
