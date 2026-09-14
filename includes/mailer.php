<?php
// includes/mailer.php
//
// Sends the welcome email when a new account is created (from the
// website's register.php AND the mobile app's api/register.php both
// call this).
//
// Uses PHP's built-in mail() function, which works out of the box on
// most shared hosting (cPanel etc.) without any extra setup. If your
// host has mail() disabled, or emails land in spam, switch to SMTP -
// see the SMTP_* notes at the bottom of this file for how to do that
// with PHPMailer instead, without changing any of the calling code.

if (!defined('MAIL_FROM_ADDRESS')) {
    define('MAIL_FROM_ADDRESS', 'noreply@decomatstationers.com'); // change to your real domain
}
if (!defined('MAIL_FROM_NAME')) {
    define('MAIL_FROM_NAME', 'Deco&Mat Stationers');
}

/**
 * Sends a welcome email to a newly registered customer.
 * Never throws - if sending fails (e.g. mail() isn't configured on this
 * server yet), it fails silently so registration itself still succeeds.
 *
 * @return bool true if the mail server accepted the message
 */
function send_welcome_email(string $to_email, string $to_name): bool {
    $subject = "Welcome to Deco&Mat Stationers!";

    $safe_name = htmlspecialchars($to_name);
    $html_body = "
    <div style='font-family: Segoe UI, Arial, sans-serif; max-width: 500px; margin: 0 auto;'>
        <div style='background: linear-gradient(135deg, #1E5C3A, #2E7D4F); padding: 24px; text-align: center; border-radius: 10px 10px 0 0;'>
            <h1 style='color: #fff; margin: 0; font-size: 20px;'>Deco&amp;Mat Stationers</h1>
        </div>
        <div style='padding: 24px; background: #fff; border: 1px solid #eee; border-top: none; border-radius: 0 0 10px 10px;'>
            <h2 style='color: #1E5C3A; font-size: 17px;'>Welcome, {$safe_name}!</h2>
            <p style='color: #333; font-size: 14.5px; line-height: 1.6;'>
                Thanks for creating an account with Deco&amp;Mat Stationers. You can now
                browse our full range of stationery and school supplies, place
                orders for pickup, and pay online with mobile money or on
                pickup.
            </p>
            <p style='color: #333; font-size: 14.5px; line-height: 1.6;'>
                If you didn't create this account, please contact us at
                " . MAIL_FROM_ADDRESS . ".
            </p>
            <p style='color: #999; font-size: 12.5px; margin-top: 28px;'>
                &copy; " . date('Y') . " Deco&amp;Mat Stationers. All rights reserved.
            </p>
        </div>
    </div>";

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM_ADDRESS . ">" . "\r\n";

    try {
        return @mail($to_email, $subject, $html_body, $headers);
    } catch (\Throwable $e) {
        return false;
    }
}

/*
 * ---------------------------------------------------------------------
 * OPTIONAL: switch to SMTP for better deliverability (recommended once
 * you're live - plain mail() often lands in spam or gets silently
 * blocked by some hosts).
 *
 * 1. Download PHPMailer from https://github.com/PHPMailer/PHPMailer
 *    (Code > Download ZIP), extract the `src/` folder into
 *    includes/PHPMailer/, then require these three files at the top of
 *    this file instead of relying on mail():
 *
 *      require __DIR__ . '/PHPMailer/Exception.php';
 *      require __DIR__ . '/PHPMailer/PHPMailer.php';
 *      require __DIR__ . '/PHPMailer/SMTP.php';
 *      use PHPMailer\PHPMailer\PHPMailer;
 *
 * 2. Replace the body of send_welcome_email() with:
 *
 *      $mail = new PHPMailer(true);
 *      try {
 *          $mail->isSMTP();
 *          $mail->Host = 'smtp.yourhost.com';
 *          $mail->SMTPAuth = true;
 *          $mail->Username = 'noreply@decomatstationers.com';
 *          $mail->Password = 'your-email-account-password';
 *          $mail->SMTPSecure = 'tls';
 *          $mail->Port = 587;
 *          $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
 *          $mail->addAddress($to_email, $to_name);
 *          $mail->isHTML(true);
 *          $mail->Subject = $subject;
 *          $mail->Body = $html_body;
 *          return $mail->send();
 *      } catch (Exception $e) {
 *          return false;
 *      }
 * ---------------------------------------------------------------------
 */
