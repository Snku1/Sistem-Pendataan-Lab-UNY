<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistem Laboratorium TV AV</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #e8ecf0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-box {
            background: #f8f9fc;
            width: 420px;
            padding: 40px 35px 35px 35px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        h1 {
            font-size: 24px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 30px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #555;
            margin-bottom: 6px;
        }

        .input-wrapper {
            position: relative;
            width: 100%;
        }

        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            color: #999;
        }

        /* Eye icon untuk show/hide password */
        .eye-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            cursor: pointer;
            color: #999;
            transition: color 0.2s;
        }

        .eye-icon:hover {
            color: #1877f2;
        }

        input {
            width: 100%;
            padding: 12px 12px 12px 42px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            transition: all 0.2s;
        }

        /* Password input dengan ruang untuk eye icon */
        .password-input {
            padding-right: 40px;
        }

        input:focus {
            outline: none;
            border-color: #1877f2;
            box-shadow: 0 0 0 2px rgba(24, 119, 242, 0.1);
        }

        .options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            font-size: 13px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #606770;
            font-weight: normal;
            cursor: pointer;
        }

        .checkbox-label input {
            width: 16px;
            height: 16px;
            margin: 0;
            padding: 0;
            cursor: pointer;
        }

        .forgot-link {
            color: #1877f2;
            text-decoration: none;
            font-size: 13px;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #1877f2;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        button:hover {
            background-color: #166fe5;
        }

        .error-message {
            background-color: #fce4e4;
            border: 1px solid #f5c2c2;
            color: #c33;
            padding: 10px 12px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .error-message p {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h1>Sistem Laboratorium TV AV</h1>

        @if ($errors->any())
            <div class="error-message">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Field dengan Icon -->
            <div class="form-group">
                <label>Email</label>
                <div class="input-wrapper">
                    <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                    </svg>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           placeholder="Enter your email" id="email-input">
                </div>
            </div>

            <!-- Password Field dengan Icon dan Eye -->
            <div class="form-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <input type="password" name="password" required
                           placeholder="Enter your password" id="password-input" class="password-input">
                    <!-- Eye icon untuk show/hide password -->
                    <svg class="eye-icon" id="togglePassword" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </div>
            </div>

            <div class="options">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember">
                    Keep me logged in
                </label>
                <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
            </div>

            <button type="submit">Sign in</button>
        </form>
    </div>

    <script>
        // ========== FITUR SHOW/HIDE PASSWORD ==========
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password-input');

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                // Toggle tipe input antara 'password' dan 'text'
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Ganti icon mata (opsional: ubah icon saat password terlihat)
                if (type === 'text') {
                    // Icon mata terbuka (tampilkan silang/mata terbuka)
                    togglePassword.innerHTML = `
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                    `;
                } else {
                    // Icon mata tertutup (kembali ke icon normal)
                    togglePassword.innerHTML = `
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    `;
                }
            });
        }

        // ========== FITUR PLACEHOLDER HILANG SAAT INPUT DIKLIK ==========
        // Cara 1: Menggunakan atribut placeholder biasa (otomatis hilang saat mengetik)
        // Tapi jika ingin hilang saat diklik (sebelum mengetik), tambahkan kode ini:
        
        const emailInput = document.getElementById('email-input');
        const passwordInputField = document.getElementById('password-input');
        
        function clearPlaceholderOnFocus(inputElement) {
            if (inputElement) {
                inputElement.addEventListener('click', function() {
                    if (this.hasAttribute('data-placeholder')) return;
                    this.setAttribute('data-placeholder', this.getAttribute('placeholder'));
                    this.setAttribute('placeholder', '');
                });
                
                inputElement.addEventListener('blur', function() {
                    if (this.value === '') {
                        this.setAttribute('placeholder', this.getAttribute('data-placeholder'));
                    }
                });
            }
        }
        
        clearPlaceholderOnFocus(emailInput);
        clearPlaceholderOnFocus(passwordInputField);
        
        // Alternatif: Jika hanya ingin placeholder hilang saat diklik (tanpa menyimpan data)
        // Cukup gunakan kode sederhana ini:
        if (emailInput) {
             emailInput.addEventListener('click', function() {
                 this.placeholder = '';
             });
             emailInput.addEventListener('blur', function() {
                 if (this.value === '') {
                     this.placeholder = 'Enter your email';
                 }
             });
         }
        if (passwordInputField) {
             passwordInputField.addEventListener('click', function() {
                 this.placeholder = '';
             });
             passwordInputField.addEventListener('blur', function() {
                 if (this.value === '') {
                     this.placeholder = 'Enter your email';
                 }
             });
         }
    </script>
</body>
</html>