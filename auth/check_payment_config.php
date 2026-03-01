<?php
/**
 * Payment Configuration Status Checker
 * =====================================
 * Run this file to check if your payment system is properly configured
 * 
 * Access: http://localhost/PSI/check_payment_config.php
 * WARNING: Delete or restrict access to this file in production!
 */

require_once('../config/payment_config.php');

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Configuration Status</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-top: 0;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .test-mode {
            background: #d4edda;
            color: #155724;
        }
        .live-mode {
            background: #f8d7da;
            color: #721c24;
        }
        .section {
            margin: 25px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }
        .check-item {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .check-item:last-child {
            border-bottom: none;
        }
        .check-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-weight: bold;
        }
        .success {
            background: #28a745;
            color: white;
        }
        .warning {
            background: #ffc107;
            color: #333;
        }
        .error {
            background: #dc3545;
            color: white;
        }
        .info {
            background: #17a2b8;
            color: white;
        }
        .check-label {
            flex: 1;
            font-weight: 500;
        }
        .check-value {
            color: #666;
            font-family: monospace;
            font-size: 13px;
            max-width: 400px;
            word-break: break-all;
        }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .alert-danger {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .alert-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>💳 Payment System Configuration Status</h1>
        
        <div class="status-badge <?php echo PAYMENT_MODE === 'production' ? 'live-mode' : 'test-mode'; ?>">
            <?php echo PAYMENT_MODE === 'production' ? '🔴 LIVE MODE' : '🟢 TEST MODE'; ?>
        </div>

        <!-- Current Configuration -->
        <div class="section">
            <h2>📋 Current Configuration</h2>
            
            <div class="check-item">
                <div class="check-icon info">ℹ️</div>
                <div class="check-label">Payment Mode:</div>
                <div class="check-value"><strong><?php echo strtoupper(PAYMENT_MODE); ?></strong></div>
            </div>
            
            <div class="check-item">
                <div class="check-icon <?php echo PAYMENT_MODE === 'production' && XENDIT_API_KEY_LIVE !== 'YOUR_PRODUCTION_API_KEY_HERE' ? 'success' : 'info'; ?>">
                    <?php echo PAYMENT_MODE === 'production' && XENDIT_API_KEY_LIVE !== 'YOUR_PRODUCTION_API_KEY_HERE' ? '✓' : 'ℹ️'; ?>
                </div>
                <div class="check-label">API Key:</div>
                <div class="check-value"><?php echo substr(XENDIT_API_KEY, 0, 20) . '...'; ?></div>
            </div>
            
            <div class="check-item">
                <div class="check-icon info">ℹ️</div>
                <div class="check-label">Success URL:</div>
                <div class="check-value"><?php echo SUCCESS_REDIRECT_URL; ?></div>
            </div>
            
            <div class="check-item">
                <div class="check-icon info">ℹ️</div>
                <div class="check-label">Failure URL:</div>
                <div class="check-value"><?php echo FAILURE_REDIRECT_URL; ?></div>
            </div>

            <div class="check-item">
                <div class="check-icon info">ℹ️</div>
                <div class="check-label">Webhook Token:</div>
                <div class="check-value"><?php echo substr(XENDIT_WEBHOOK_TOKEN, 0, 15) . '...'; ?></div>
            </div>
        </div>

        <!-- Production Readiness Check -->
        <?php
        $readiness = isProductionReady();
        if (PAYMENT_MODE === 'production'):
            if (!$readiness['ready']):
        ?>
        <div class="alert alert-danger">
            <strong>⚠️ Production Mode Issues Detected!</strong>
            <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                <?php foreach ($readiness['issues'] as $issue): ?>
                    <li><?php echo htmlspecialchars($issue); ?></li>
                <?php endforeach; ?>
            </ul>
            <p style="margin-top: 15px; margin-bottom: 0;">
                <strong>Action Required:</strong> Please update <code>config/payment_config.php</code> before accepting real payments.
            </p>
        </div>
        <?php else: ?>
        <div class="alert alert-success">
            <strong>✅ Production Configuration Valid!</strong><br>
            Your payment system is properly configured for live transactions.
        </div>
        <?php endif; ?>
        <?php else: ?>
        <div class="alert alert-warning">
            <strong>🟢 Test Mode Active</strong><br>
            You are currently in test mode. No real payments will be processed. 
            To go live, update <code>PAYMENT_MODE</code> in <code>config/payment_config.php</code>.
        </div>
        <?php endif; ?>

        <!-- Security Status -->
        <div class="section">
            <h2>🔒 Security Status</h2>
            
            <div class="check-item">
                <div class="check-icon <?php echo PAYMENT_MODE === 'production' ? 'success' : 'info'; ?>">
                    <?php echo PAYMENT_MODE === 'production' ? '✓' : 'ℹ️'; ?>
                </div>
                <div class="check-label">Webhook Verification:</div>
                <div class="check-value">
                    <?php echo PAYMENT_MODE === 'production' ? '<strong style="color: #28a745;">ENABLED</strong> (automatic in production)' : 'Disabled (test mode)'; ?>
                </div>
            </div>

            <div class="check-item">
                <div class="check-icon <?php echo isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'success' : 'warning'; ?>">
                    <?php echo isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? '✓' : '!'; ?>
                </div>
                <div class="check-label">HTTPS/SSL:</div>
                <div class="check-value">
                    <?php 
                    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                        echo '<strong style="color: #28a745;">ENABLED</strong>';
                    } else {
                        echo '<strong style="color: #ffc107;">NOT DETECTED</strong>';
                        if (PAYMENT_MODE === 'production') {
                            echo ' - Required for production!';
                        }
                    }
                    ?>
                </div>
            </div>

            <?php if (PAYMENT_MODE === 'production' && strpos(SUCCESS_REDIRECT_URL, 'localhost') !== false): ?>
            <div class="check-item">
                <div class="check-icon error">✗</div>
                <div class="check-label">Redirect URLs:</div>
                <div class="check-value" style="color: #dc3545;">
                    <strong>WARNING:</strong> URLs still contain 'localhost'
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Next Steps -->
        <div class="section">
            <h2>📝 Next Steps</h2>
            
            <?php if (PAYMENT_MODE === 'test'): ?>
            <p>You are in <strong>test mode</strong>. To prepare for production:</p>
            <ol>
                <li>Complete testing with test payments</li>
                <li>Get production API keys from Xendit Dashboard</li>
                <li>Update <code>config/payment_config.php</code> with production credentials</li>
                <li>Set <code>PAYMENT_MODE</code> to <code>'production'</code></li>
                <li>Re-run this checker to verify configuration</li>
            </ol>
            <?php elseif (!$readiness['ready']): ?>
            <p>Production mode is enabled but configuration is incomplete:</p>
            <ol>
                <li>Open <code>config/payment_config.php</code></li>
                <li>Fix the issues listed above</li>
                <li>Re-run this checker</li>
            </ol>
            <?php else: ?>
            <p>✅ Your system is ready for production! Remember to:</p>
            <ol>
                <li>Test with a small real payment first (₱1-10)</li>
                <li>Monitor error logs closely</li>
                <li>Check Xendit dashboard regularly</li>
                <li>Have support team ready for payment questions</li>
            </ol>
            <?php endif; ?>
        </div>

        <!-- Documentation Links -->
        <div class="section">
            <h2>📚 Documentation</h2>
            <ul>
                <li><a href="PRODUCTION_DEPLOYMENT_GUIDE.md" target="_blank">Full Production Deployment Guide</a></li>
                <li><a href="QUICK_REFERENCE.md" target="_blank">Quick Reference Card</a></li>
                <li><a href="https://dashboard.xendit.co/" target="_blank">Xendit Dashboard</a></li>
                <li><a href="https://developers.xendit.co/" target="_blank">Xendit Developer Documentation</a></li>
            </ul>
        </div>

        <div style="margin-top: 30px; padding: 15px; background: #fff3cd; border-radius: 8px; border: 1px solid #ffeaa7;">
            <strong>⚠️ Security Notice:</strong> 
            Delete or restrict access to this file (<code>check_payment_config.php</code>) in production to prevent exposing configuration details.
        </div>

        <div style="margin-top: 20px; text-align: center; color: #999; font-size: 12px;">
            Last checked: <?php echo date('F d, Y h:i:s A'); ?>
        </div>
    </div>
</body>
</html>
