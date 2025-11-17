<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Login - ToyShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #dc3545;
            --secondary: #c82333;
            --accent: #ff6b6b;
            --success: #1cc88a;
            --dark: #a71e2a;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
            overflow: hidden;
            width: 100%;
            max-width: 420px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            padding: 2.5rem 2rem;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .login-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        }
        
        .admin-icon {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            display: block;
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .login-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .login-subtitle {
            opacity: 0.9;
            font-size: 0.95rem;
        }
        
        .login-body {
            padding: 2.5rem 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .form-label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            z-index: 3;
        }
        
        .form-control {
            padding: 12px 15px 12px 45px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #fff;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
            transform: translateY(-1px);
        }
        
        .btn-login {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            padding: 14px 30px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 12px;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(220, 53, 69, 0.3);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(220, 53, 69, 0.4);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .back-home {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .back-home a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .back-home a:hover {
            color: var(--secondary);
        }
        
        .access-info {
            background: linear-gradient(135deg, #fdf2f2, #fde8e8);
            border: 1px solid #fecaca;
            border-radius: 15px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        
        .access-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .access-info p {
            font-size: 0.85rem;
            color: #4a5568;
            margin-bottom: 0.3rem;
        }
        
        .error-message {
            background: linear-gradient(135deg, #fed7d7, #feb2b2);
            border: 1px solid #fc8181;
            color: #c53030;
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }
        
        .floating-element {
            position: absolute;
            font-size: 1.5rem;
            opacity: 0.1;
            animation: float-around 8s ease-in-out infinite;
        }
        
        .float-1 { top: 10%; left: 10%; animation-delay: 0s; }
        .float-2 { top: 70%; left: 80%; animation-delay: 2s; }
        .float-3 { top: 40%; left: 85%; animation-delay: 4s; }
        .float-4 { top: 80%; left: 15%; animation-delay: 6s; }
        
        @keyframes float-around {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(20px, -20px) rotate(5deg); }
            50% { transform: translate(-10px, -30px) rotate(-5deg); }
            75% { transform: translate(-20px, -20px) rotate(5deg); }
        }
    </style>
</head>
<body>
    <!-- Floating Background Elements -->
    <div class="floating-elements">
        <div class="floating-element float-1">🛡️</div>
        <div class="floating-element float-2">⚙️</div>
        <div class="floating-element float-3">🔐</div>
        <div class="floating-element float-4">👑</div>
    </div>
    
    <div class="login-container">
        <!-- Header -->
        <div class="login-header">
            <i class="fas fa-user-shield admin-icon"></i>
            <h1 class="login-title">ToyShop</h1>
            <p class="login-subtitle">Super Admin Portal</p>
        </div>
        
        <!-- Login Form -->
        <div class="login-body">
            @if($errors->any())
                <div class="error-message">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ $errors->first() }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('super-admin.login') }}">
                @csrf
                
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               placeholder="Enter your email" 
                               required 
                               autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               placeholder="Enter your password" 
                               required>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>Login to Super Admin
                </button>
            </form>
            
            <div class="back-home">
                <a href="/">
                    <i class="fas fa-arrow-left me-2"></i>Back to Homepage
                </a>
            </div>
            
            <!-- Access Info -->
            <div class="access-info">
                <div class="access-title">
                    <i class="fas fa-shield-alt"></i>
                    Super Admin Access Only
                </div>
                <p class="mb-2">
                    <strong>Note:</strong> This portal is for authorized super administrators only.
                </p>
                <p class="mb-0">
                    Store administrators should use the admin portal instead.
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add focus effects
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.querySelector('.input-icon').style.color = 'var(--secondary)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.querySelector('.input-icon').style.color = 'var(--primary)';
            });
        });
        
        // Add loading state to button
        document.querySelector('form').addEventListener('submit', function() {
            const btn = this.querySelector('.btn-login');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Logging in...';
            btn.disabled = true;
        });
    </script>
</body>
</html>