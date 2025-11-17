<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Authentication')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #64748b;
            --light: #f8fafc;
            --dark: #1e293b;
            --border: #e2e8f0;
            --error: #dc2626;
            --radius: 12px;
            --shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .landscape-container {
            display: flex;
            max-width: 1000px;
            width: 100%;
            min-height: 580px;
            background: white;
            border-radius: 20px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }
        
        .brand-section {
            flex: 1;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
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
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(-45deg);
        }
        
        .logo {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            position: relative;
            z-index: 2;
        }
        
        .brand-quote {
            font-size: 1.3rem;
            font-weight: 300;
            line-height: 1.6;
            margin-bottom: 30px;
            position: relative;
            z-index: 2;
            opacity: 0.95;
        }
        
        .brand-author {
            font-size: 1rem;
            font-weight: 500;
            position: relative;
            z-index: 2;
            opacity: 0.9;
        }
        
        .form-section {
            flex: 1.2;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .form-container {
            width: 100%;
        }
        
        .form-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 8px;
        }
        
        .form-subtitle {
            color: var(--secondary);
            margin-bottom: 25px;
            font-size: 0.95rem;
        }
        
        .form-group {
            margin-bottom: 1.2rem;
        }
        
        .form-label {
            display: block;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 6px;
            font-size: 0.85rem;
        }
        
        .form-label .required {
            color: var(--error);
            margin-left: 2px;
        }
        
        .optional {
            color: var(--secondary);
            font-weight: 400;
            font-size: 0.75rem;
            margin-left: 4px;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid var(--border);
            border-radius: var(--radius);
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background: white;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .form-control::placeholder {
            color: #94a3b8;
        }
        
        /* Validation Styles */
        .form-control:invalid:not(:focus):not(:placeholder-shown) {
            border-color: var(--error);
            background-color: #fef2f2;
        }
        
        .form-control:invalid:not(:focus):not(:placeholder-shown) + .error-message {
            display: block;
        }
        
        .error-message {
            display: none;
            color: var(--error);
            font-size: 0.75rem;
            margin-top: 5px;
            font-weight: 500;
        }
        
        .form-control:required:valid {
            border-color: #10b981;
            background-color: #f0fdf4;
        }
        
        /* Alert Styles */
        .alert {
            padding: 12px 16px;
            border-radius: var(--radius);
            border: 1px solid transparent;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }
        
        .alert-success {
            background-color: #f0fdf4;
            border-color: #bbf7d0;
            color: #166534;
        }
        
        .alert-danger {
            background-color: #fef2f2;
            border-color: #fecaca;
            color: #dc2626;
        }
        
        .alert-warning {
            background-color: #fffbeb;
            border-color: #fed7aa;
            color: #92400e;
        }
        
        .alert i {
            margin-right: 8px;
        }
        
        /* Name Row Alignment Fixes */
        .name-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 12px;
            margin-bottom: 1.2rem;
        }
        
        .name-row .form-group {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .name-row .form-label {
            height: 32px;
            display: flex;
            align-items: flex-end;
            margin-bottom: 6px;
        }
        
        .name-row .form-control {
            flex: 1;
        }
        
        .name-row > div {
            display: flex;
            flex-direction: column;
        }
        
        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: var(--radius);
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
            width: 100%;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }
        
        .btn-google {
            background: white;
            color: var(--dark);
            border: 2px solid var(--border);
            width: 100%;
        }
        
        .btn-google:hover {
            border-color: var(--primary);
            transform: translateY(-1px);
        }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
            color: var(--secondary);
            font-size: 0.85rem;
        }
        
        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: var(--border);
        }
        
        .divider::before {
            margin-right: 1rem;
        }
        
        .divider::after {
            margin-left: 1rem;
        }
        
        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.2rem;
            border-top: 1px solid var(--border);
            color: var(--secondary);
            font-size: 0.85rem;
        }
        
        .auth-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
        
        .auth-footer a:hover {
            text-decoration: underline;
        }
        
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.2rem;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            border: 2px solid var(--border);
        }
        
        .checkbox-group label {
            font-size: 0.85rem;
            color: var(--dark);
            font-weight: 500;
        }
        
        .forgot-link {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .forgot-link:hover {
            text-decoration: underline;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .landscape-container {
                flex-direction: column;
                max-width: 450px;
                min-height: auto;
            }
            
            .brand-section {
                padding: 30px 25px;
                text-align: center;
            }
            
            .form-section {
                padding: 30px 25px;
            }
            
            .logo {
                font-size: 2rem;
            }
            
            .brand-quote {
                font-size: 1.1rem;
            }
            
            .name-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
            
            .name-row .form-label {
                height: auto;
                align-items: flex-start;
            }
        }
        
        @media (max-width: 480px) {
            .landscape-container {
                border-radius: 15px;
            }
            
            .brand-section {
                padding: 30px 25px;
            }
            
            .form-section {
                padding: 30px 25px;
            }
            
            .form-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    @yield('content')
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>