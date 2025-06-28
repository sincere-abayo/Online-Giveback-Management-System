<?php
require_once 'DBConnection.php';

class SimpleEmailService extends DBConnection
{

    private $smtp_host;
    private $smtp_port;
    private $smtp_username;
    private $smtp_password;
    private $from_email;
    private $from_name;
    private $enable_email;
    private $debug_mode;

    public function __construct()
    {
        parent::__construct();
        $this->loadEmailConfig();
    }

    private function loadEmailConfig()
    {
        // Load configuration from email_settings.php if it exists
        if (file_exists(__DIR__ . '/../email_settings.php')) {
            require_once __DIR__ . '/../email_settings.php';

            $this->smtp_host = defined('SMTP_HOST') ? SMTP_HOST : 'smtp.gmail.com';
            $this->smtp_port = defined('SMTP_PORT') ? SMTP_PORT : 587;
            $this->smtp_username = defined('SMTP_USERNAME') ? SMTP_USERNAME : 'infofonepo@gmail.com';
            $this->smtp_password = defined('SMTP_PASSWORD') ? SMTP_PASSWORD : 'zaoxwuezfjpglwjb';
            $this->from_email = defined('FROM_EMAIL') ? FROM_EMAIL : 'dufatanyecharity@gmail.com';
            $this->from_name = defined('FROM_NAME') ? FROM_NAME : 'Dufatanye Charity Foundation';
            $this->enable_email = defined('ENABLE_EMAIL') ? ENABLE_EMAIL : true;
            $this->debug_mode = defined('EMAIL_DEBUG') ? EMAIL_DEBUG : false;
        } else {
            // Fallback to default values
            $this->smtp_host = 'smtp.gmail.com';
            $this->smtp_port = 587;
            $this->smtp_username = 'infofonepo@gmail.com';
            $this->smtp_password = 'zaoxwuezfjpglwjb';
            $this->from_email = 'dufatanyecharity@gmail.com';
            $this->from_name = 'Dufatanye Charity Foundation';
            $this->enable_email = true;
            $this->debug_mode = false;
        }
    }

    public function sendWelcomeEmail($userEmail, $firstName, $lastName, $rollNumber)
    {
        // Check if email is enabled
        if (!$this->enable_email) {
            return ['success' => true, 'message' => 'Email sending is disabled'];
        }

        try {
            // Check if PHPMailer is available
            if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
                require_once __DIR__ . '/../vendor/autoload.php';

                if (class_exists('\PHPMailer\PHPMailer\PHPMailer')) {
                    // Use PHPMailer
                    return $this->sendPHPMailerEmail($userEmail, $firstName, $lastName, $rollNumber);
                }
            }

            // Fallback to basic PHP mail function
            return $this->sendBasicEmail($userEmail, $firstName, $lastName, $rollNumber);

        } catch (Exception $e) {
            error_log("Email Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Email could not be sent: ' . $e->getMessage()];
        }
    }

    private function sendBasicEmail($userEmail, $firstName, $lastName, $rollNumber)
    {
        $subject = 'Welcome to Dufatanye Charity Foundation - Registration Successful!';
        $message = $this->getWelcomeEmailHTML($firstName, $lastName, $rollNumber, $userEmail);

        // Headers for HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: ' . $this->from_name . ' <' . $this->from_email . '>' . "\r\n";
        $headers .= 'Reply-To: ' . $this->from_email . "\r\n";
        $headers .= 'X-Mailer: PHP/' . phpversion() . "\r\n";

        $success = mail($userEmail, $subject, $message, $headers);

        return [
            'success' => $success,
            'message' => $success ? 'Welcome email sent successfully using PHP mail()' : 'Failed to send email using PHP mail()'
        ];
    }

    private function sendPHPMailerEmail($userEmail, $firstName, $lastName, $rollNumber)
    {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = $this->smtp_host;
            $mail->SMTPAuth = true;
            $mail->Username = $this->smtp_username;
            $mail->Password = $this->smtp_password;
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $this->smtp_port;

            // Enable debug output (set to 0 for production)
            $mail->SMTPDebug = $this->debug_mode ? 2 : 0;

            // Recipients
            $mail->setFrom($this->from_email, $this->from_name);
            $mail->addAddress($userEmail, $firstName . ' ' . $lastName);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Welcome to Dufatanye Charity Foundation - Registration Successful!';
            $mail->Body = $this->getWelcomeEmailHTML($firstName, $lastName, $rollNumber, $userEmail);
            $mail->AltBody = $this->getWelcomeEmailText($firstName, $lastName, $rollNumber, $userEmail);

            $mail->send();
            return ['success' => true, 'message' => 'Welcome email sent successfully using PHPMailer'];

        } catch (\PHPMailer\PHPMailer\Exception $e) {
            error_log("PHPMailer Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'PHPMailer Error: ' . $e->getMessage()];
        }
    }

    private function getWelcomeEmailText($firstName, $lastName, $rollNumber, $userEmail)
    {
        $currentDate = date('F j, Y');
        $baseUrl = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/";

        return "
Welcome to Dufatanye Charity Foundation!

Dear $firstName $lastName,

Thank you for joining our volunteer community! We are excited to have you as part of our mission to create positive change in communities.

Your Registration Details:
- Full Name: $firstName $lastName
- Volunteer ID: $rollNumber
- Email: $userEmail
- Registration Date: $currentDate

What You Can Do Now:
✓ Join community volunteer activities and events
✓ Track your contributions and impact
✓ Connect with other volunteers
✓ Contribute to transparent charitable giving

Ready to start? Access your volunteer dashboard at: {$baseUrl}volunteer_login.php

Our Mission: To promote responsible resource use by helping institutions and individuals efficiently manage charitable giving, donations, and community support.

Contact Us:
Phone: +250 XXX XXX XXX
Email: info@dufatanye.org

Best regards,
Dufatanye Charity Foundation Team";
    }

    private function getWelcomeEmailHTML($firstName, $lastName, $rollNumber, $userEmail)
    {
        $baseUrl = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/";
        $currentDate = date('F j, Y');

        return "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Welcome to Dufatanye Charity Foundation</title>
            <style>
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    background-color: #f8f9fa;
                    margin: 0;
                    padding: 20px;
                }
                
                .email-container {
                    max-width: 600px;
                    margin: 0 auto;
                    background: white;
                    border-radius: 20px;
                    overflow: hidden;
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
                }
                
                .email-header {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    padding: 40px 30px;
                    text-align: center;
                }
                
                .organization-badge {
                    display: inline-block;
                    background: rgba(255, 255, 255, 0.2);
                    color: white;
                    padding: 8px 20px;
                    border-radius: 25px;
                    margin-bottom: 20px;
                    font-size: 14px;
                    font-weight: 600;
                    letter-spacing: 0.5px;
                }
                
                .email-title {
                    font-size: 2.5rem;
                    font-weight: 700;
                    margin-bottom: 15px;
                    line-height: 1.2;
                }
                
                .email-subtitle {
                    font-size: 1.1rem;
                    opacity: 0.95;
                    font-weight: 400;
                }
                
                .email-body {
                    padding: 40px 30px;
                }
                
                .welcome-section {
                    text-align: center;
                    margin-bottom: 40px;
                }
                
                .welcome-message {
                    font-size: 1.3rem;
                    color: #667eea;
                    font-weight: 600;
                    margin-bottom: 20px;
                }
                
                .user-info {
                    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
                    padding: 25px;
                    border-radius: 15px;
                    margin: 30px 0;
                    border: 2px solid rgba(102, 126, 234, 0.1);
                }
                
                .info-item {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 10px 0;
                    border-bottom: 1px solid #e9ecef;
                }
                
                .info-item:last-child {
                    border-bottom: none;
                }
                
                .info-label {
                    font-weight: 600;
                    color: #555;
                }
                
                .info-value {
                    color: #667eea;
                    font-weight: 600;
                }
                
                .features-section {
                    margin: 40px 0;
                }
                
                .section-title {
                    font-size: 1.5rem;
                    font-weight: 700;
                    color: #333;
                    margin-bottom: 25px;
                    text-align: center;
                }
                
                .feature-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 20px;
                    margin-bottom: 30px;
                }
                
                .feature-item {
                    background: white;
                    padding: 20px;
                    border-radius: 10px;
                    border: 2px solid #f8f9fa;
                    text-align: center;
                }
                
                .feature-title {
                    font-size: 1rem;
                    font-weight: 600;
                    color: #333;
                    margin-bottom: 8px;
                }
                
                .feature-desc {
                    font-size: 0.9rem;
                    color: #666;
                    line-height: 1.4;
                }
                
                .cta-section {
                    background: linear-gradient(135deg, #667eea, #764ba2);
                    color: white;
                    padding: 30px;
                    border-radius: 15px;
                    text-align: center;
                    margin: 30px 0;
                }
                
                .cta-title {
                    font-size: 1.5rem;
                    font-weight: 700;
                    margin-bottom: 15px;
                }
                
                .cta-text {
                    margin-bottom: 25px;
                    opacity: 0.95;
                }
                
                .cta-button {
                    display: inline-block;
                    background: white;
                    color: #667eea;
                    padding: 12px 30px;
                    border-radius: 25px;
                    text-decoration: none;
                    font-weight: 600;
                    margin: 0 10px 10px 0;
                }
                
                .email-footer {
                    background: #f8f9fa;
                    padding: 30px;
                    text-align: center;
                    border-top: 1px solid #e9ecef;
                }
                
                .footer-text {
                    color: #666;
                    font-size: 0.9rem;
                    margin-bottom: 15px;
                }
                
                .contact-info {
                    font-size: 0.85rem;
                    color: #777;
                    margin-top: 20px;
                }
                
                @media (max-width: 600px) {
                    .feature-grid {
                        grid-template-columns: 1fr;
                    }
                    
                    .email-title {
                        font-size: 2rem;
                    }
                    
                    .email-header, .email-body, .email-footer {
                        padding: 25px 20px;
                    }
                }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <!-- Header -->
                <div class='email-header'>
                    <div class='organization-badge'>
                        Dufatanye Charity Foundation
                    </div>
                    <h1 class='email-title'>Welcome to Our Community!</h1>
                    <p class='email-subtitle'>Your journey of impact and giving begins now</p>
                </div>
                
                <!-- Body -->
                <div class='email-body'>
                    <!-- Welcome Section -->
                    <div class='welcome-section'>
                        <h2 class='welcome-message'>🎉 Registration Successful!</h2>
                        <p>Dear <strong>" . htmlentities($firstName . ' ' . $lastName) . "</strong>,</p>
                        <p>Thank you for joining the Dufatanye Charity Foundation volunteer community! We are excited to have you as part of our mission to create positive change in communities.</p>
                    </div>
                    
                    <!-- User Information -->
                    <div class='user-info'>
                        <div class='info-item'>
                            <span class='info-label'>📝 Full Name:</span>
                            <span class='info-value'>" . htmlentities($firstName . ' ' . $lastName) . "</span>
                        </div>
                        <div class='info-item'>
                            <span class='info-label'>🆔 Volunteer ID:</span>
                            <span class='info-value'>" . htmlentities($rollNumber) . "</span>
                        </div>
                        <div class='info-item'>
                            <span class='info-label'>📧 Email:</span>
                            <span class='info-value'>" . htmlentities($userEmail) . "</span>
                        </div>
                        <div class='info-item'>
                            <span class='info-label'>📅 Registration Date:</span>
                            <span class='info-value'>$currentDate</span>
                        </div>
                    </div>
                    
                    <!-- What You Can Do -->
                    <div class='features-section'>
                        <h3 class='section-title'>🚀 What You Can Do Now</h3>
                        <div class='feature-grid'>
                            <div class='feature-item'>
                                <h4 class='feature-title'>👥 Join Activities</h4>
                                <p class='feature-desc'>Participate in community volunteer activities and events</p>
                            </div>
                            <div class='feature-item'>
                                <h4 class='feature-title'>📊 Track Impact</h4>
                                <p class='feature-desc'>Monitor your contributions and see the difference you make</p>
                            </div>
                            <div class='feature-item'>
                                <h4 class='feature-title'>🤝 Connect</h4>
                                <p class='feature-desc'>Network with other volunteers and community members</p>
                            </div>
                            <div class='feature-item'>
                                <h4 class='feature-title'>🎯 Make Impact</h4>
                                <p class='feature-desc'>Contribute to transparent and accountable charitable giving</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Call to Action -->
                    <div class='cta-section'>
                        <h3 class='cta-title'>Ready to Start Making a Difference?</h3>
                        <p class='cta-text'>Access your volunteer dashboard and explore available opportunities to contribute to our community initiatives.</p>
                        <a href='{$baseUrl}volunteer_login.php' class='cta-button'>🔐 Access Dashboard</a>
                        <a href='{$baseUrl}index.php' class='cta-button'>🏠 Visit Homepage</a>
                    </div>
                    
                    <!-- Mission Reminder -->
                    <div style='text-align: center; margin: 30px 0; padding: 20px; background: #f8f9fa; border-radius: 10px;'>
                        <h4 style='color: #667eea; margin-bottom: 15px;'>💝 Our Mission</h4>
                        <p style='color: #555; font-style: italic;'>To promote responsible resource use by helping institutions and individuals efficiently manage charitable giving, donations, and community support — fostering a culture of accountability, sustainability, and social impact.</p>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class='email-footer'>
                    <p class='footer-text'>
                        <strong>Dufatanye Charity Foundation</strong><br>
                        Empowering communities through transparent giving and meaningful volunteer opportunities
                    </p>
                    
                    <div class='contact-info'>
                        <p>📞 Contact: +250 788445566 | 📧 Email: info@dufatanye.org</p>
                        <p>🌐 Website: <a href='$baseUrl' style='color: #667eea;'>Visit Our Platform</a></p>
                        <p style='margin-top: 15px; font-size: 0.8rem; color: #999;'>
                            This email was sent to " . htmlentities($userEmail) . " because you registered as a volunteer.<br>
                            If you have any questions, please contact our support team.
                        </p>
                    </div>
                </div>
            </div>
        </body>
        </html>";
    }
}
?>