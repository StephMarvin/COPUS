<?php
    require_once "variables.config.php";
    require_once "functions.config.php";
    require_once "datetime.config.php";

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require "PHPMailer/src/Exception.php";
    require "PHPMailer/src/PHPMailer.php";
    require "PHPMailer/src/SMTP.php";

    function send_otp($email, $full_name, $otp_code){
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->Host = MAILER_HOST;

            $mail->Username = MAILER_USERNAME;
            $mail->Password = MAILER_PASSWORD;
            //$mail->SMTPSecure = "tls";
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

            $mail->Port = MAILER_PORT;

            $mail->setFrom(MAILER_USERNAME, MAILER_NAME);
            $mail->addAddress($email, $full_name);

            $mail->isHTML(true);
            $mail->Subject = "Your One-Time Password (OTP) for Secure Access";
            $mail->Body = '
                <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8; padding:40px 0; font-family: Arial, Helvetica, sans-serif;">
                    <tr>
                        <td align="center">
                        
                            <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; padding:40px;">
                            
                            <tr>
                                <td align="center" style="padding-bottom:20px;">
                                
                                    <img src="https://citecopus.online/public/assets/website-logo-cite.png"
                                         alt="Logo"
                                         width="200"
                                         height="130"
                                         style="display:block; margin:0 auto 15px auto; border:0; outline:none; text-decoration:none;">
                                    
                                    <h2 style="margin:0; color:#2c3e50;">OTP Verification</h2>
                                
                                </td>
                            </tr>
                            
                            <tr>
                                <td style="color:#333333; font-size:16px; line-height:1.6;">
                                    <p style="margin:0 0 15px 0;">
                                        Greetings, <strong>' . htmlspecialchars($full_name, ENT_QUOTES, 'UTF-8') . '</strong>!
                                    </p>
                                
                                    <p style="margin:0 0 15px 0;">
                                        We received a request to access your <strong>COPUS</strong> account. 
                                        Please use the One-Time Password (OTP) below to proceed.
                                    </p>
                                </td>
                            </tr>
                            
                            <tr>
                                <td align="center" style="padding:25px 0;">
                                    <div style="background-color:#eef2ff; padding:20px 30px; border-radius:8px; display:inline-block;">
                                        <p style="margin:0; font-size:14px; color:#555;">Your One-Time Password (OTP)</p>
                                        <p style="margin:10px 0 0 0; font-size:32px; font-weight:bold; letter-spacing:6px; color:#1a237e;">
                                            ' . htmlspecialchars($otp_code, ENT_QUOTES, 'UTF-8') . '
                                        </p>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr>
                                <td style="background-color:#fff4e5; padding:15px; border-radius:6px; font-size:14px; color:#8a6d3b; line-height:1.6;">
                                    <p style="margin:0 0 8px 0;"><strong>Important:</strong></p>
                                    <ul style="padding-left:18px; margin:0;">
                                        <li style="margin-bottom:6px;">This OTP is valid only for the next <strong>5 minutes</strong>.</li>
                                        <li>If you didn\'t request this, please ignore this email or contact our support team immediately.</li>
                                    </ul>
                                </td>
                            </tr>
                            
                            <tr>
                                <td style="padding-top:30px; font-size:14px; color:#777777;">
                                    <p style="margin:0;">Regards,</p>
                                    <p style="margin:5px 0 0 0;"><strong>CITE Support Team</strong></p>
                                </td>
                            </tr>
                            
                            </table>
                        
                        </td>
                    </tr>
                </table>
            ';

            $mail->send();

            return ["success" => true, "message" => "Email sent successfully."];
        } 
        
        catch (Exception $e) {
            error_log("Error: " . $mail->ErrorInfo);
            return ["success" => false, "message" => "Error in sending email! Please try again."];
        }
    }

    function send_admin_creation_email($email, $full_name, $role, $id_number, $password) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->Host = MAILER_HOST;

            $mail->Username = MAILER_USERNAME;
            $mail->Password = MAILER_PASSWORD;
            //$mail->SMTPSecure = "tls";
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

            $mail->Port = MAILER_PORT;

            $mail->setFrom(MAILER_USERNAME, MAILER_NAME);
            $mail->addAddress($email, $full_name);

            $mail->isHTML(true);
            $mail->Subject = "Account Creation Notification";
            $mail->Body = '
                <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8; padding:40px 0; font-family: Arial, Helvetica, sans-serif;">
                    <tr>
                        <td align="center">
                        
                            <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; padding:40px;">
                            
                                <tr>
                                    <td align="center" style="padding-bottom:20px;">
                                    
                                        <img src="https://citecopus.online/public/assets/website-logo-cite.png"
                                         alt="Logo"
                                         width="200"
                                         height="130"
                                         style="display:block; margin:0 auto 15px auto; border:0; outline:none; text-decoration:none;">
                                    
                                        <h2 style="margin:0; color:#2c3e50;"> Welcome to COPUS! </h2>
                                        
                                    </td>
                                </tr>
                            
                                <tr>
                                    <td style="color:#333333; font-size:16px; line-height:1.6;">
                                    
                                        <p style="margin:0 0 15px 0;">
                                            Greetings, <strong>' . htmlspecialchars($full_name, ENT_QUOTES, 'UTF-8') . '</strong>!
                                        </p>
                                        
                                        <p style="margin:0 0 15px 0;">
                                            We are pleased to inform you that your <strong>COPUS</strong> account has been successfully created.
                                        </p>
                                        
                                        <p style="margin:0 0 15px 0;">
                                            Your assigned role: <strong>' . htmlspecialchars($role, ENT_QUOTES, 'UTF-8') . '</strong>
                                        </p>
                                        
                                        <p style="margin:0 0 10px 0;"><strong>Login Credentials:</strong></p>
                                        
                                        <div style="background-color:#f1f3f6; padding:15px; border-radius:6px; font-size:14px;">
                                            <p style="margin:5px 0;">ID Number: <strong>' . htmlspecialchars($id_number, ENT_QUOTES, 'UTF-8') . '</strong></p>
                                            <p style="margin:5px 0;">Password: <strong>' . htmlspecialchars($password, ENT_QUOTES, 'UTF-8') . '</strong></p>
                                        </div>
                                        
                                        <br>
                                        
                                        <div style="background-color:#fff4e5; padding:15px; border-radius:6px; font-size:14px; color:#8a6d3b;">
                                            <p style="margin:0 0 8px 0;"><strong>Security Reminder:</strong></p>
                                            <p style="margin:0;">For security reasons, we highly encourage you to change your password immediately after logging in. Do not share your credentials with anyone.</p>
                                        </div>
                                        
                                        <br>
                                        
                                        <p style="margin:0 0 20px 0;">
                                            Congratulations and welcome to <strong>COPUS</strong>!
                                        </p>
                                    
                                    </td>
                                </tr>
                            
                                <tr>
                                
                                    <td align="center" style="padding-top:10px;">
                                    
                                        <a href="https://citecopus.online/" target="_blank"
                                            style="background-color:#FF5C00; padding:12px 20px; color:#ffffff; text-decoration:none; font-weight:bold; border-radius:5px; font-size:14px; display:inline-block; margin-bottom:15px; line-height:1; width:180px;">
                                            Visit COPUS Main Page
                                        </a>
                                
                                        <a href="https://citecopus.online/pages/admin/login.php" target="_blank"
                                            style="background-color:#FF5C00; padding:12px 20px; color:#ffffff; text-decoration:none; font-weight:bold; border-radius:5px; font-size:14px; display:inline-block; margin-bottom:15px; line-height:1; width:180px;">
                                            Login to Your Account
                                        </a>
                                    
                                    </td>
                                
                                </tr>
                            
                                <tr>
                                    <td style="padding-top:30px; font-size:14px; color:#777777;">
                                        <p style="margin:0;">Regards,</p>
                                        <p style="margin:5px 0 0 0;"><strong>CITE Support Team</strong></p>
                                    </td>
                                </tr>
                            
                            </table>
                        
                        </td>
                    </tr>
                </table>
            ';

            $mail->send();

            return ["success" => true, "message" => "Email sent successfully."];
        } 
        
        catch (Exception $e) {
            error_log("Error: " . $mail->ErrorInfo);
            return ["success" => false, "message" => "Error in sending email! Please try again."];
        }
    }

    function send_user_creation_email($email, $full_name, $role, $id_number, $password) {
        
        $get_role = explode(' ', $role)[0];
        
        $links = [
            "Dean" => "https://citecopus.online/pages/deans/login.php",
            "Teacher" => "https://citecopus.online/pages/teacher/login.php",
            "Observer" => "https://citecopus.online/pages/observer/login.php",
        ];
        
        $login_link = $links[$get_role] ?? "https://citecopus.online/";
        
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->Host = MAILER_HOST;

            $mail->Username = MAILER_USERNAME;
            $mail->Password = MAILER_PASSWORD;
            //$mail->SMTPSecure = "tls";
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

            $mail->Port = MAILER_PORT;

            $mail->setFrom(MAILER_USERNAME, MAILER_NAME);
            $mail->addAddress($email, $full_name);

            $mail->isHTML(true);
            $mail->Subject = "Account Creation Notification";
            $mail->Body = '
                <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8; padding:40px 0; font-family: Arial, Helvetica, sans-serif;">
                    <tr>
                        <td align="center">
                        
                            <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; padding:40px;">
                            
                                <tr>
                                    <td align="center" style="padding-bottom:20px;">
                                    
                                        <img src="https://citecopus.online/public/assets/website-logo-cite.png"
                                         alt="Logo"
                                         width="200"
                                         height="130"
                                         style="display:block; margin:0 auto 15px auto; border:0; outline:none; text-decoration:none;">
                                    
                                        <h2 style="margin:0; color:#2c3e50;"> Welcome to COPUS! </h2>
                                        
                                    </td>
                                </tr>
                            
                                <tr>
                                    <td style="color:#333333; font-size:16px; line-height:1.6;">
                                    
                                        <p style="margin:0 0 15px 0;">
                                            Greetings, <strong>' . htmlspecialchars($full_name, ENT_QUOTES, 'UTF-8') . '</strong>!
                                        </p>
                                        
                                        <p style="margin:0 0 15px 0;">
                                            We are pleased to inform you that your <strong>COPUS</strong> account has been successfully created.
                                        </p>
                                        
                                        <p style="margin:0 0 15px 0;">
                                            Your assigned role: <strong>' . htmlspecialchars($role, ENT_QUOTES, 'UTF-8') . '</strong>
                                        </p>
                                        
                                        <p style="margin:0 0 10px 0;"><strong>Login Credentials:</strong></p>
                                        
                                        <div style="background-color:#f1f3f6; padding:15px; border-radius:6px; font-size:14px;">
                                            <p style="margin:5px 0;">ID Number: <strong>' . htmlspecialchars($id_number, ENT_QUOTES, 'UTF-8') . '</strong></p>
                                            <p style="margin:5px 0;">Password: <strong>' . htmlspecialchars($password, ENT_QUOTES, 'UTF-8') . '</strong></p>
                                        </div>
                                        
                                        <br>
                                        
                                        <div style="background-color:#fff4e5; padding:15px; border-radius:6px; font-size:14px; color:#8a6d3b;">
                                            <p style="margin:0 0 8px 0;"><strong>Security Reminder:</strong></p>
                                            <p style="margin:0;">For security reasons, we highly encourage you to change your password immediately after logging in. Do not share your credentials with anyone.</p>
                                        </div>
                                        
                                        <br>
                                        
                                        <p style="margin:0 0 20px 0;">
                                            Congratulations and welcome to <strong>COPUS</strong>!
                                        </p>
                                    
                                    </td>
                                </tr>
                            
                                <tr>
                                
                                    <td align="center" style="padding-top:10px;">
                                    
                                        <a href="https://citecopus.online/" target="_blank"
                                            style="background-color:#FF5C00; padding:12px 20px; color:#ffffff; text-decoration:none; font-weight:bold; border-radius:5px; font-size:14px; display:inline-block; margin-bottom:15px; line-height:1; width:180px;">
                                            Visit COPUS Main Page
                                        </a>
                                
                                        <a href="' . $login_link . '" target="_blank"
                                            style="background-color:#FF5C00; padding:12px 20px; color:#ffffff; text-decoration:none; font-weight:bold; border-radius:5px; font-size:14px; display:inline-block; margin-bottom:15px; line-height:1; width:180px;">
                                            Login to Your Account
                                        </a>
                                    
                                    </td>
                                
                                </tr>
                            
                                <tr>
                                    <td style="padding-top:30px; font-size:14px; color:#777777;">
                                        <p style="margin:0;">Regards,</p>
                                        <p style="margin:5px 0 0 0;"><strong>CITE Support Team</strong></p>
                                    </td>
                                </tr>
                            
                            </table>
                        
                        </td>
                    </tr>
                </table>
            ';

            $mail->send();

            return ["success" => true, "message" => "Email sent successfully."];
        } 
        
        catch (Exception $e) {
            error_log("Error: " . $mail->ErrorInfo);
            return ["success" => false, "message" => "Error in sending email! Please try again."];
        }
    }

    function send_reset_token($email, $full_name, $token) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->Host = MAILER_HOST;

            $mail->Username = MAILER_USERNAME;
            $mail->Password = MAILER_PASSWORD;
            //$mail->SMTPSecure = "tls";
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

            $mail->Port = MAILER_PORT;

            $mail->setFrom(MAILER_USERNAME, MAILER_NAME);
            $mail->addAddress($email, $full_name);

            $mail->isHTML(true);
            $mail->Subject = "Password Reset Token for COPUS Account";
            $mail->Body = '
                <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8; padding:40px 0; font-family: Arial, Helvetica, sans-serif;">
                    <tr>
                        <td align="center">
                    
                            <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; padding:40px;">
                        
                                <tr>
                                    <td align="center" style="padding-bottom:20px;">
                                    
                                        <img src="https://citecopus.online/public/assets/website-logo-cite.png"
                                             alt="Logo"
                                             width="200"
                                             height="130"
                                             style="display:block; margin:0 auto 15px auto; border:0; outline:none; text-decoration:none;">
                                    
                                        <h2 style="margin:0; color:#2c3e50;"> Password Reset </h2>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td style="color:#333333; font-size:16px; line-height:1.6;">
                                        <p style="margin:0 0 15px 0;">
                                            Greetings, <strong>' . htmlspecialchars($full_name, ENT_QUOTES, 'UTF-8') . '</strong>!
                                        </p>
                                    
                                        <p style="margin:0 0 15px 0;">
                                            We received a request to access your <strong>COPUS</strong> account. 
                                            Please use the reset password token below to confirm your identity.
                                        </p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td align="center" style="padding:25px 0;">
                                        <div style="background-color:#f1f3f6; padding:20px; border-radius:6px; display:inline-block;">
                                            <p style="margin:0; font-size:14px; color:#777;">Your Reset Password Token</p>
                                            <p style="margin:10px 0 0 0; font-size:24px; font-weight:bold; letter-spacing:2px; color:#2c3e50;">
                                            ' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td style="color:#333333; font-size:16px; line-height:1.6;">
                                        <p style="margin:0 0 10px 0;"><strong>Steps in resetting your password:</strong></p>
                                        <ol style="padding-left:20px; margin:0 0 20px 0;">
                                            <li style="margin-bottom:8px;">Enter your <strong>Reset Password Token</strong>.</li>
                                            <li style="margin-bottom:8px;">Enter your <strong>New Password</strong> and <strong>Confirm New Password</strong>.</li>
                                        <li style="margin-bottom:8px;">Click <strong>Reset Password</strong>.</li>
                                        </ol>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td style="background-color:#fff4e5; padding:15px; border-radius:6px; font-size:14px; color:#8a6d3b; line-height:1.6;">
                                        <p style="margin:0 0 8px 0;"><strong>Important Notes:</strong></p>
                                        <ul style="padding-left:18px; margin:0;">
                                            <li style="margin-bottom:6px;">This password reset token is valid only for the next <strong>5 minutes</strong>.</li>
                                            <li style="margin-bottom:6px;">If you didn\'t request this, please ignore this email or contact our support team immediately.</li>
                                        <li>We will notify you once your password has been successfully reset.</li>
                                        </ul>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td style="padding-top:30px; font-size:14px; color:#777777;">
                                        <p style="margin:0;">Regards,</p>
                                        <p style="margin:5px 0 0 0;"><strong>CITE Support Team</strong></p>
                                    </td>
                                </tr>
                        
                            </table>
                    
                        </td>
                    </tr>
                </table>
            ';

            $mail->send();

            return ["success" => true, "message" => "Email sent successfully."];
        } 
        
        catch (Exception $e) {
            error_log("Error: " . $mail->ErrorInfo);
            return ["success" => false, "message" => "Error in sending email! Please try again."];
        }
    }

    function send_reset_password_notification($email, $full_name) {
        $current_date_time = get_current_timestamp();
        $get_device = get_device_data();
        $ip_address = get_ip_address();
        $get_browser = get_browser_data(); 
        
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->Host = MAILER_HOST;

            $mail->Username = MAILER_USERNAME;
            $mail->Password = MAILER_PASSWORD;
            //$mail->SMTPSecure = "tls";
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

            $mail->Port = MAILER_PORT;

            $mail->setFrom(MAILER_USERNAME, MAILER_NAME);
            $mail->addAddress($email, $full_name);

            $mail->isHTML(true);
            $mail->Subject = "Reset Password Notification";
            $mail->Body = '
                <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8; padding:40px 0; font-family: Arial, Helvetica, sans-serif;">
                    <tr>
                        <td align="center">
                        
                            <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; padding:40px;">
                        
                                <tr>
                                    <td align="center" style="padding-bottom:20px;">
                                    
                                        <img src="https://citecopus.online/public/assets/website-logo-cite.png"
                                             alt="Logo"
                                             width="200"
                                             height="130"
                                             style="display:block; margin:0 auto 15px auto; border:0; outline:none; text-decoration:none;">
                                    
                                        <h2 style="margin:0; color:#2c3e50;"> Password Reset Confirmation </h2>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td style="color:#333333; font-size:16px; line-height:1.6;">
                                        <p style="margin:0 0 15px 0;">
                                            Greetings, <strong>' . htmlspecialchars($full_name, ENT_QUOTES, 'UTF-8') . '</strong>!
                                        </p>
                                    
                                        <p style="margin:0 0 15px 0;">
                                            We have noticed that you attempted to change your password via the reset password page.
                                        </p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td style="padding:20px 0;">
                                        <div style="background-color:#f1f3f6; padding:20px; border-radius:6px; font-size:14px; color:#333; line-height:1.6;">
                                            <p style="margin:0 0 10px 0;"><strong>Request Details:</strong></p>
                                            <p style="margin:5px 0;"><strong>Date and Time:</strong> ' . htmlspecialchars($current_date_time, ENT_QUOTES, 'UTF-8') . '</p>
                                            <p style="margin:5px 0;"><strong>IP Address:</strong> ' . htmlspecialchars($ip_address, ENT_QUOTES, 'UTF-8') . '</p>
                                            <p style="margin:5px 0;"><strong>Device:</strong> ' . htmlspecialchars($get_device, ENT_QUOTES, 'UTF-8') . '</p>
                                            <p style="margin:5px 0;"><strong>Browser:</strong> ' . htmlspecialchars($get_browser, ENT_QUOTES, 'UTF-8') . '</p>
                                        </div>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td style="padding:10px 0 20px 0; color:#2e7d32; font-size:16px; line-height:1.6;">
                                        <p style="margin:0;">
                                            <strong>Confirmation:</strong> Your password has been successfully reset. 
                                            You can now log in using your new credentials.
                                        </p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td style="background-color:#fff4e5; padding:15px; border-radius:6px; font-size:14px; color:#8a6d3b; line-height:1.6;">
                                        <p style="margin:0 0 8px 0;"><strong>Security Notice:</strong></p>
                                        <p style="margin:0;">
                                            If you did not request this change or suspect unauthorized activity, 
                                            please reset your password immediately or contact our support team for assistance.
                                        </p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td style="padding-top:30px; font-size:14px; color:#777777;">
                                        <p style="margin:0;">Regards,</p>
                                        <p style="margin:5px 0 0 0;"><strong>CITE Support Team</strong></p>
                                    </td>
                                </tr>
                        
                            </table>
                        
                        </td>
                    </tr>
                </table>
            ';

            $mail->send();

            return ["success" => true, "message" => "Email sent successfully."];
        }

        catch (Exception $e) {
            error_log("Error: " . $mail->ErrorInfo);
            return ["success" => false, "message" => "Error in sending email! Please try again."];
        }
    }
?>