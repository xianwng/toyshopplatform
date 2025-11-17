@extends('Security.app')

@section('content')
<div class="landscape-container">
    <!-- Left Brand Section -->
    <div class="brand-section">
        <div class="logo">LOGIN TO TOYSPACE</div>
        <div class="brand-author">Welcome, dear customer!</div>
    </div>
    
    <!-- Right Form Section -->
    <div class="form-section">
        <div class="form-container">
            <h1 class="form-title">Welcome Back</h1>
            <p class="form-subtitle">Sign in to your account</p>
            
            <!-- Success Message -->
            @if(session('success'))
                <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Error Message -->
            @if(session('error'))
                <div style="background-color: #fef2f2; border: 1px solid #fecaca; color: #ef4444; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Display validation errors -->
            @if($errors->any())
                <div style="background-color: #fef2f2; border: 1px solid #fecaca; color: #ef4444; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf
                
                <div class="form-group">
                    <label for="login" class="form-label">Email or Username<span class="required">*</span></label>
                    <input type="text" class="form-control" id="login" name="login" placeholder="Enter email or username" required value="{{ old('login') }}">
                    <div class="error-message" id="login-error">Email or username is required</div>
                    <div class="error-message" id="login-not-found" style="display: none; color: #ef4444;">
                        <i class="fas fa-exclamation-circle"></i> This email or username is not registered
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password<span class="required">*</span></label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                    <div class="error-message" id="password-error">Password is required</div>
                    <div class="error-message" id="password-incorrect" style="display: none; color: #ef4444;">
                        <i class="fas fa-exclamation-circle"></i> Incorrect password
                    </div>
                </div>
                
                {{-- Forgot password link removed --}}
                
                <button type="submit" class="btn btn-primary" id="submit-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Sign In
                </button>
                
                <div class="divider">Or continue with</div>
                
                <a href="{{ route('auth.google') }}" class="btn btn-google">
                    <i class="fab fa-google"></i>
                    Google
                </a>
                
                <div class="auth-footer">
                    No account? <a href="{{ route('register') }}">Sign up here</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('loginForm');
        const loginInput = document.getElementById('login');
        const passwordInput = document.getElementById('password');
        const loginError = document.getElementById('login-error');
        const loginNotFound = document.getElementById('login-not-found');
        const passwordError = document.getElementById('password-error');
        const passwordIncorrect = document.getElementById('password-incorrect');
        const submitBtn = document.getElementById('submit-btn');
        
        let checkTimeout;
        let currentLogin = '';
        let userExists = false;

        // Real-time validation for login field
        loginInput.addEventListener('input', function() {
            clearTimeout(checkTimeout);
            const value = this.value.trim();
            
            if (value.length === 0) {
                resetLoginValidation();
                updateSubmitButton();
                return;
            }

            // Wait for user to stop typing (500ms delay)
            checkTimeout = setTimeout(() => {
                checkUserExists(value);
            }, 500);
        });

        // Real-time validation for password field
        passwordInput.addEventListener('input', function() {
            const value = this.value.trim();
            
            if (value.length === 0) {
                resetPasswordValidation();
            } else if (userExists && currentLogin) {
                // If user exists and password is entered, check credentials
                clearTimeout(checkTimeout);
                checkTimeout = setTimeout(() => {
                    checkCredentials(currentLogin, value);
                }, 800);
            }
            
            updateSubmitButton();
        });

        // Real-time validation on blur
        const inputs = form.querySelectorAll('input[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (!this.value.trim()) {
                    this.style.borderColor = 'var(--error)';
                    this.style.backgroundColor = '#fef2f2';
                    if (this.id === 'login') {
                        loginError.style.display = 'block';
                        loginNotFound.style.display = 'none';
                    } else if (this.id === 'password') {
                        passwordError.style.display = 'block';
                        passwordIncorrect.style.display = 'none';
                    }
                } else {
                    this.style.borderColor = '#10b981';
                    this.style.backgroundColor = '#f0fdf4';
                    if (this.id === 'login') {
                        loginError.style.display = 'none';
                    } else if (this.id === 'password') {
                        passwordError.style.display = 'none';
                    }
                }
                updateSubmitButton();
            });

            // Clear validation styles on focus
            input.addEventListener('focus', function() {
                this.style.borderColor = 'var(--border)';
                this.style.backgroundColor = 'white';
                if (this.id === 'login') {
                    loginError.style.display = 'none';
                    loginNotFound.style.display = 'none';
                } else if (this.id === 'password') {
                    passwordError.style.display = 'none';
                    passwordIncorrect.style.display = 'none';
                }
            });
        });

        // Check if user exists via AJAX
        function checkUserExists(login) {
            if (!login) return;

            fetch(`/check-user-exists?login=${encodeURIComponent(login)}`)
                .then(response => response.json())
                .then(data => {
                    currentLogin = login;
                    userExists = data.exists;
                    
                    if (data.exists) {
                        // User exists - show green validation
                        loginInput.style.borderColor = '#10b981';
                        loginInput.style.backgroundColor = '#f0fdf4';
                        loginNotFound.style.display = 'none';
                        loginError.style.display = 'none';
                        
                        // If password is already entered, check credentials
                        if (passwordInput.value.trim()) {
                            checkCredentials(login, passwordInput.value.trim());
                        }
                    } else {
                        // User doesn't exist - show red validation
                        loginInput.style.borderColor = 'var(--error)';
                        loginInput.style.backgroundColor = '#fef2f2';
                        loginNotFound.style.display = 'block';
                        loginError.style.display = 'none';
                        resetPasswordValidation();
                    }
                    updateSubmitButton();
                })
                .catch(error => {
                    console.error('Error checking user:', error);
                });
        }

        // Check credentials via AJAX
        function checkCredentials(login, password) {
            if (!login || !password) return;

            const formData = new FormData();
            formData.append('login', login);
            formData.append('password', password);
            formData.append('_token', document.querySelector('input[name="_token"]').value);

            fetch('/check-credentials', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.valid) {
                    // Credentials are valid - show green validation
                    passwordInput.style.borderColor = '#10b981';
                    passwordInput.style.backgroundColor = '#f0fdf4';
                    passwordIncorrect.style.display = 'none';
                    passwordError.style.display = 'none';
                } else {
                    // Password is incorrect - show red validation
                    passwordInput.style.borderColor = 'var(--error)';
                    passwordInput.style.backgroundColor = '#fef2f2';
                    passwordIncorrect.style.display = 'block';
                    passwordError.style.display = 'none';
                }
                updateSubmitButton();
            })
            .catch(error => {
                console.error('Error checking credentials:', error);
            });
        }

        function resetLoginValidation() {
            loginInput.style.borderColor = 'var(--border)';
            loginInput.style.backgroundColor = 'white';
            loginError.style.display = 'block';
            loginNotFound.style.display = 'none';
            userExists = false;
            currentLogin = '';
            resetPasswordValidation();
        }

        function resetPasswordValidation() {
            passwordInput.style.borderColor = 'var(--border)';
            passwordInput.style.backgroundColor = 'white';
            passwordError.style.display = 'block';
            passwordIncorrect.style.display = 'none';
        }

        function updateSubmitButton() {
            const loginValid = loginInput.value.trim() && userExists;
            const passwordValid = passwordInput.value.trim();
            const passwordCorrect = !passwordIncorrect.style.display || passwordIncorrect.style.display === 'none';
            
            if (loginValid && passwordValid && passwordCorrect) {
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
                submitBtn.style.cursor = 'pointer';
            } else {
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.6';
                submitBtn.style.cursor = 'not-allowed';
            }
        }

        // Form submission validation
        form.addEventListener('submit', function(e) {
            let isValid = true;

            // Check required fields
            const requiredFields = form.querySelectorAll('input[required]');
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = 'var(--error)';
                    field.style.backgroundColor = '#fef2f2';
                    if (field.id === 'login') {
                        loginError.style.display = 'block';
                        loginNotFound.style.display = 'none';
                    } else if (field.id === 'password') {
                        passwordError.style.display = 'block';
                        passwordIncorrect.style.display = 'none';
                    }
                }
            });

            if (!isValid) {
                e.preventDefault();
                // Scroll to first error
                const firstError = form.querySelector('input:invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });

        // Initialize button state
        updateSubmitButton();
    });
</script>
@endsection