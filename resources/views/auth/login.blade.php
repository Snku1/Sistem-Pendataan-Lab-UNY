<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistem Laboratorium UNY</title>
    
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

        /* Tambahan style untuk link WA */
        .wa-help {
            margin-top: 25px;
            text-align: center;
            font-size: 13px;
            border-top: 1px solid #e4e6eb;
            padding-top: 20px;
        }
        .wa-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: #25D366;
            color: white;
            padding: 8px 16px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.2s;
            margin-top: 8px;
        }
        .wa-link:hover {
            background-color: #128C7E;
        }
        .wa-icon {
            width: 18px;
            height: 18px;
            fill: white;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h1>Sistem Laboratorium UNY</h1>

        @if ($errors->any())
            <div class="error-message">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

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

            <div class="form-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <input type="password" name="password" required
                           placeholder="Enter your password" id="password-input" class="password-input">
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
                <!-- Link Forgot password telah dihapus -->
            </div>

            <button type="submit">Sign in</button>
        </form>

        <!-- Tambahan: Chat WhatsApp untuk bantuan -->
        <div class="wa-help">
            <p>Lupa password atau belum punya akun?</p>
            <a href="https://wa.me/6281384241171?text=Halo%20admin%2C%20saya%20membutuhkan%20bantuan%20untuk%20login%20ke%20sistem%20laboratorium." 
               target="_blank" class="wa-link">
                <svg class="wa-icon" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19.077 4.928C17.191 3.041 14.683 2 12.006 2 6.798 2 2.552 6.245 2.55 11.453c0 1.665.435 3.297 1.263 4.73L2 22l5.826-1.796c1.38.754 2.94 1.153 4.53 1.153h.004c5.206 0 9.454-4.246 9.456-9.454.001-2.527-.982-4.902-2.766-6.684zM12.006 20.393c-1.414 0-2.8-.38-4.005-1.102l-.287-.17-3.457 1.066 1.102-3.37-.187-.297c-.79-1.258-1.207-2.692-1.206-4.168.002-4.248 3.456-7.702 7.704-7.702 2.058 0 3.992.802 5.447 2.257 1.454 1.455 2.255 3.389 2.254 5.448-.003 4.247-3.458 7.702-7.705 7.702z"/>
                    <path d="M16.946 14.32c-.208-.104-1.226-.605-1.416-.674-.19-.07-.328-.104-.466.104-.138.208-.534.674-.654.812-.12.138-.24.156-.447.052s-.873-.322-1.663-1.025c-.615-.547-1.03-1.223-1.15-1.43-.12-.208-.013-.32.09-.423.092-.092.207-.24.31-.36.104-.12.138-.208.208-.347.07-.138.034-.26-.017-.364-.052-.104-.466-1.123-.638-1.538-.168-.4-.338-.346-.466-.353-.12-.007-.258-.007-.396-.007-.138 0-.362.052-.552.26-.19.208-.724.707-.724 1.727s.742 2.003.846 2.142c.104.138 1.46 2.23 3.536 3.127.494.214.88.342 1.18.438.497.16.95.137 1.307.083.4-.06 1.226-.5 1.4-.984.173-.483.173-.897.12-.983-.052-.086-.19-.138-.398-.242z"/>
                </svg>
                Hubungi Admin via WhatsApp
            </a>
        </div>
    </div>

    <script>
        // ========== FITUR SHOW/HIDE PASSWORD ==========
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password-input');

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                if (type === 'text') {
                    togglePassword.innerHTML = `
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                    `;
                } else {
                    togglePassword.innerHTML = `
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    `;
                }
            });
        }

        // ========== PLACEHOLDER HILANG SAAT DIKLIK ==========
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
        
        // Alternatif sederhana
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