<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #3b82f6;
            --primary-dark: #2563eb;
            --error: #ef4444;
            --success: #10b981;
            --warning: #f59e0b;
            --border: #d1d5db;
            --text: #374151;
            --text-light: #6b7280;
            --bg-light: #f9fafb;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f3f4f6;
            color: var(--text);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .landscape-container {
            display: flex;
            max-width: 1200px;
            width: 100%;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .brand-section {
            flex: 1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .brand-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0,0 L100,0 L100,100 Z" fill="rgba(255,255,255,0.1)"/></svg>');
            background-size: cover;
        }

        .logo {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 30px;
            position: relative;
        }

        .brand-quote {
            font-size: 26px;
            font-style: italic;
            line-height: 1.4;
            margin-bottom: 25px;
            position: relative;
            font-weight: 300;
        }

        .brand-author {
            font-size: 16px;
            opacity: 0.9;
            position: relative;
            font-weight: 500;
        }

        .form-section {
            flex: 1.3;
            padding: 50px;
            display: flex;
            align-items: center;
            background: white;
        }

        .form-container {
            width: 100%;
            max-width: 500px;
        }

        .form-title {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 8px;
            color: #1f2937;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .form-subtitle {
            color: var(--text-light);
            margin-bottom: 35px;
            font-size: 16px;
        }

        .name-row {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 14px;
            color: #374151;
        }

        .required {
            color: var(--error);
        }

        .optional {
            color: var(--text-light);
            font-size: 0.75em;
            font-weight: normal;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--border);
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: white;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            background: white;
        }

        .error-message {
            color: var(--error);
            font-size: 12px;
            margin-top: 6px;
            display: none;
            min-height: 18px;
            font-weight: 500;
        }

        .success-message {
            color: var(--success);
            font-size: 12px;
            margin-top: 6px;
            display: none;
            min-height: 18px;
            font-weight: 500;
        }

        .password-strength {
            margin-top: 8px;
            font-size: 12px;
            font-weight: 500;
        }

        .strength-weak { color: var(--error); }
        .strength-fair { color: var(--warning); }
        .strength-good { color: #10b981; }
        .strength-strong { color: var(--success); }

        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .form-col {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            gap: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 8px -1px rgba(59, 130, 246, 0.4);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .auth-footer {
            text-align: center;
            margin-top: 25px;
            color: var(--text-light);
            font-size: 15px;
        }

        .auth-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        #password-mismatch {
            display: none;
            background-color: #fef2f2;
            border: 2px solid #fecaca;
            color: var(--error);
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            align-items: center;
            gap: 10px;
            font-weight: 500;
        }

        @media (max-width: 968px) {
            .landscape-container {
                flex-direction: column;
                max-width: 500px;
            }
            
            .brand-section {
                padding: 40px 30px;
                text-align: center;
            }
            
            .form-section {
                padding: 40px 30px;
            }
            
            .name-row {
                flex-direction: column;
                gap: 15px;
            }
            
            .form-row {
                flex-direction: column;
                gap: 15px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 15px;
            }
            
            .brand-section {
                padding: 30px 20px;
            }
            
            .form-section {
                padding: 30px 20px;
            }
            
            .form-title {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="landscape-container">
        <!-- Left Brand Section -->
        <div class="brand-section">
            <div class="logo">ToyCollectible</div>
            <div class="brand-quote">
                "Start your collecting journey with us. Create memories, build your legacy."
            </div>
            <div class="brand-author">- Join Our Community</div>
        </div>
        
        <!-- Right Form Section -->
        <div class="form-section">
            <div class="form-container">
                <h1 class="form-title">Create Account</h1>
                <p class="form-subtitle">Join our platform and start your collection journey</p>
                
                <form method="POST" action="{{ route('register') }}" id="registerForm">
                    @csrf
                    
                    <!-- Name Fields -->
                    <div class="name-row">
                        <div class="form-group">
                            <label for="first_name" class="form-label">First Name<span class="required">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First name" required minlength="3" value="{{ old('first_name') }}">
                            <div class="error-message" id="first-name-error">First name must be at least 3 characters</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="middle_name" class="form-label">Middle Initial<span class="optional">(Optional)</span></label>
                            <input type="text" class="form-control" id="middle_name" name="middle_name" placeholder="M" maxlength="1" value="{{ old('middle_name') }}">
                            <div class="error-message" id="middle-name-error">Middle initial must be 1 character only</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="last_name" class="form-label">Last Name<span class="required">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last name" required minlength="3" value="{{ old('last_name') }}">
                            <div class="error-message" id="last-name-error">Last name must be at least 3 characters</div>
                        </div>
                    </div>
                    
                    <!-- Account Info -->
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="username" class="form-label">Username<span class="required">*</span></label>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Username" required value="{{ old('username') }}">
                                <div class="error-message" id="username-error">Username is required</div>
                                <div class="error-message" id="username-taken" style="display: none; color: #ef4444;">
                                    <i class="fas fa-exclamation-circle"></i> Username already taken
                                </div>
                                <div class="success-message" id="username-available" style="display: none; color: #10b981;">
                                    <i class="fas fa-check-circle"></i> Username available
                                </div>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="email" class="form-label">Email<span class="required">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="email@example.com" required value="{{ old('email') }}">
                                <div class="error-message" id="email-error">Valid email is required</div>
                                <div class="error-message" id="email-taken" style="display: none; color: #ef4444;">
                                    <i class="fas fa-exclamation-circle"></i> Email already registered
                                </div>
                                <div class="success-message" id="email-available" style="display: none; color: #10b981;">
                                    <i class="fas fa-check-circle"></i> Email available
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label for="password" class="form-label">Password<span class="required">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Password (8-20 characters)" required minlength="8" maxlength="20">
                                <div class="error-message" id="password-error">Password must be 8-20 characters</div>
                                <div class="password-strength" id="password-strength"></div>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label for="password_confirmation" class="form-label">Confirm Password<span class="required">*</span></label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" required>
                                <div class="error-message" id="password-confirm-error">Please confirm your password</div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="password-mismatch">
                        <i class="fas fa-exclamation-circle"></i> Passwords don't match!
                    </div>

                    <!-- Display validation errors -->
                    @if($errors->any())
                        <div style="background-color: #fef2f2; border: 2px solid #fecaca; color: #ef4444; padding: 16px; border-radius: 10px; margin-bottom: 20px;">
                            <ul style="margin: 0; padding-left: 20px;">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        <i class="fas fa-user-plus"></i>
                        Create Account
                    </button>
                    
                    <div class="auth-footer">
                        Have an account? <a href="{{ route('login') }}">Sign in</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registerForm');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('password_confirmation');
            const mismatchElement = document.getElementById('password-mismatch');
            const submitBtn = document.getElementById('submit-btn');
            
            let usernameCheckTimeout;
            let emailCheckTimeout;

            // Real-time validation functions
            const validateFirstName = () => {
                const value = document.getElementById('first_name').value.trim();
                const errorElement = document.getElementById('first-name-error');
                
                if (value.length < 3 && value.length > 0) {
                    document.getElementById('first_name').style.borderColor = 'var(--error)';
                    document.getElementById('first_name').style.backgroundColor = '#fef2f2';
                    errorElement.style.display = 'block';
                    return false;
                } else if (value.length >= 3) {
                    document.getElementById('first_name').style.borderColor = '#10b981';
                    document.getElementById('first_name').style.backgroundColor = '#f0fdf4';
                    errorElement.style.display = 'none';
                    return true;
                } else {
                    document.getElementById('first_name').style.borderColor = 'var(--border)';
                    document.getElementById('first_name').style.backgroundColor = 'white';
                    errorElement.style.display = 'none';
                    return false;
                }
            };

            const validateLastName = () => {
                const value = document.getElementById('last_name').value.trim();
                const errorElement = document.getElementById('last-name-error');
                
                if (value.length < 3 && value.length > 0) {
                    document.getElementById('last_name').style.borderColor = 'var(--error)';
                    document.getElementById('last_name').style.backgroundColor = '#fef2f2';
                    errorElement.style.display = 'block';
                    return false;
                } else if (value.length >= 3) {
                    document.getElementById('last_name').style.borderColor = '#10b981';
                    document.getElementById('last_name').style.backgroundColor = '#f0fdf4';
                    errorElement.style.display = 'none';
                    return true;
                } else {
                    document.getElementById('last_name').style.borderColor = 'var(--border)';
                    document.getElementById('last_name').style.backgroundColor = 'white';
                    errorElement.style.display = 'none';
                    return false;
                }
            };

            const validateMiddleName = () => {
                const value = document.getElementById('middle_name').value.trim();
                const errorElement = document.getElementById('middle-name-error');
                
                if (value.length > 1) {
                    document.getElementById('middle_name').style.borderColor = 'var(--error)';
                    document.getElementById('middle_name').style.backgroundColor = '#fef2f2';
                    errorElement.style.display = 'block';
                    document.getElementById('middle_name').value = value.charAt(0);
                } else if (value.length === 1) {
                    document.getElementById('middle_name').style.borderColor = '#10b981';
                    document.getElementById('middle_name').style.backgroundColor = '#f0fdf4';
                    errorElement.style.display = 'none';
                } else {
                    document.getElementById('middle_name').style.borderColor = 'var(--border)';
                    document.getElementById('middle_name').style.backgroundColor = 'white';
                    errorElement.style.display = 'none';
                }
            };

            const validatePassword = () => {
                const value = password.value;
                const errorElement = document.getElementById('password-error');
                const strengthElement = document.getElementById('password-strength');
                
                if (value.length < 8 && value.length > 0) {
                    password.style.borderColor = 'var(--error)';
                    password.style.backgroundColor = '#fef2f2';
                    errorElement.style.display = 'block';
                    strengthElement.textContent = '';
                    return false;
                } else if (value.length > 20) {
                    password.style.borderColor = 'var(--error)';
                    password.style.backgroundColor = '#fef2f2';
                    errorElement.style.display = 'block';
                    strengthElement.textContent = '';
                    return false;
                } else if (value.length >= 8 && value.length <= 20) {
                    password.style.borderColor = '#10b981';
                    password.style.backgroundColor = '#f0fdf4';
                    errorElement.style.display = 'none';
                    
                    // Check password strength
                    const strength = checkPasswordStrength(value);
                    strengthElement.textContent = `Password strength: ${strength.text}`;
                    strengthElement.className = `password-strength strength-${strength.level}`;
                    
                    return true;
                } else {
                    password.style.borderColor = 'var(--border)';
                    password.style.backgroundColor = 'white';
                    errorElement.style.display = 'none';
                    strengthElement.textContent = '';
                    return false;
                }
            };

            const checkPasswordStrength = (password) => {
                let score = 0;
                
                // Length check (8-20 characters)
                if (password.length >= 8) score++;
                if (password.length >= 12) score++;
                if (password.length >= 16) score++;
                
                // Contains numbers
                if (/\d/.test(password)) score++;
                
                // Contains lowercase and uppercase
                if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
                
                // Contains special characters
                if (/[^a-zA-Z0-9]/.test(password)) score++;
                
                if (score <= 2) return { level: 'weak', text: 'Weak' };
                if (score <= 3) return { level: 'fair', text: 'Fair' };
                if (score <= 4) return { level: 'good', text: 'Good' };
                return { level: 'strong', text: 'Strong' };
            };

            const validatePasswords = () => {
                const passwordValue = password.value;
                const confirmValue = confirmPassword.value;

                if (passwordValue && confirmValue) {
                    if (passwordValue !== confirmValue) {
                        mismatchElement.style.display = 'flex';
                        confirmPassword.style.borderColor = 'var(--error)';
                        confirmPassword.style.backgroundColor = '#fef2f2';
                    } else {
                        mismatchElement.style.display = 'none';
                        confirmPassword.style.borderColor = '#10b981';
                        confirmPassword.style.backgroundColor = '#f0fdf4';
                    }
                } else {
                    mismatchElement.style.display = 'none';
                }
                updateSubmitButton();
            };

            const checkUsernameExists = (username) => {
                if (!username) return;

                fetch(`/check-username-available?username=${encodeURIComponent(username)}`)
                    .then(response => response.json())
                    .then(data => {
                        const takenElement = document.getElementById('username-taken');
                        const availableElement = document.getElementById('username-available');
                        
                        if (data.exists) {
                            document.getElementById('username').style.borderColor = 'var(--error)';
                            document.getElementById('username').style.backgroundColor = '#fef2f2';
                            takenElement.style.display = 'block';
                            availableElement.style.display = 'none';
                        } else {
                            document.getElementById('username').style.borderColor = '#10b981';
                            document.getElementById('username').style.backgroundColor = '#f0fdf4';
                            takenElement.style.display = 'none';
                            availableElement.style.display = 'block';
                        }
                        updateSubmitButton();
                    })
                    .catch(error => {
                        console.error('Error checking username:', error);
                    });
            };

            const checkEmailExists = (email) => {
                if (!email) return;

                fetch(`/check-email-available?email=${encodeURIComponent(email)}`)
                    .then(response => response.json())
                    .then(data => {
                        const takenElement = document.getElementById('email-taken');
                        const availableElement = document.getElementById('email-available');
                        
                        if (data.exists) {
                            document.getElementById('email').style.borderColor = 'var(--error)';
                            document.getElementById('email').style.backgroundColor = '#fef2f2';
                            takenElement.style.display = 'block';
                            availableElement.style.display = 'none';
                        } else {
                            document.getElementById('email').style.borderColor = '#10b981';
                            document.getElementById('email').style.backgroundColor = '#f0fdf4';
                            takenElement.style.display = 'none';
                            availableElement.style.display = 'block';
                        }
                        updateSubmitButton();
                    })
                    .catch(error => {
                        console.error('Error checking email:', error);
                    });
            };

            const updateSubmitButton = () => {
                const firstNameValid = validateFirstName();
                const lastNameValid = validateLastName();
                const usernameValid = document.getElementById('username').value.trim() && !document.getElementById('username-taken').style.display.includes('block');
                const emailValid = document.getElementById('email').value.trim() && !document.getElementById('email-taken').style.display.includes('block');
                const passwordValid = validatePassword();
                const passwordsMatch = password.value === confirmPassword.value && password.value.length >= 8 && password.value.length <= 20;
                
                if (firstNameValid && lastNameValid && usernameValid && emailValid && passwordValid && passwordsMatch) {
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '1';
                    submitBtn.style.cursor = 'pointer';
                } else {
                    submitBtn.disabled = true;
                    submitBtn.style.opacity = '0.6';
                    submitBtn.style.cursor = 'not-allowed';
                }
            };

            // Event listeners
            document.getElementById('first_name').addEventListener('input', () => {
                validateFirstName();
                updateSubmitButton();
            });

            document.getElementById('last_name').addEventListener('input', () => {
                validateLastName();
                updateSubmitButton();
            });

            document.getElementById('middle_name').addEventListener('input', validateMiddleName);

            document.getElementById('username').addEventListener('input', function() {
                clearTimeout(usernameCheckTimeout);
                const value = this.value.trim();
                
                if (value.length === 0) {
                    document.getElementById('username').style.borderColor = 'var(--border)';
                    document.getElementById('username').style.backgroundColor = 'white';
                    document.getElementById('username-taken').style.display = 'none';
                    document.getElementById('username-available').style.display = 'none';
                    updateSubmitButton();
                    return;
                }

                usernameCheckTimeout = setTimeout(() => {
                    checkUsernameExists(value);
                }, 500);
            });

            document.getElementById('email').addEventListener('input', function() {
                clearTimeout(emailCheckTimeout);
                const value = this.value.trim();
                
                if (value.length === 0) {
                    document.getElementById('email').style.borderColor = 'var(--border)';
                    document.getElementById('email').style.backgroundColor = 'white';
                    document.getElementById('email-taken').style.display = 'none';
                    document.getElementById('email-available').style.display = 'none';
                    updateSubmitButton();
                    return;
                }

                emailCheckTimeout = setTimeout(() => {
                    checkEmailExists(value);
                }, 500);
            });

            password.addEventListener('input', function() {
                validatePassword();
                validatePasswords();
                updateSubmitButton();
            });

            confirmPassword.addEventListener('input', validatePasswords);

            // Initialize button state
            updateSubmitButton();
        });
    </script>
</body>
</html>