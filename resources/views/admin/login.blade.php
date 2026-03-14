<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Readiwork</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="icon" type="image/png" href="{{ asset('icon.png') }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #071629 0%, #0d2649 55%, #091e3a 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .login-box { background: #fff; border-radius: 20px; padding: 40px 36px; width: 100%; max-width: 420px; box-shadow: 0 32px 80px rgba(0,0,0,.35); }
        .login-logo { text-align: center; margin-bottom: 28px; }
        .login-logo .logo-mark { width: 56px; height: 56px; background: linear-gradient(135deg,#0FA958,#16a34a); border-radius: 16px; display: inline-flex; align-items: center; justify-content: center; color: #fff; font-size: 1.5rem; margin-bottom: 12px; }
        .login-logo h1 { font-size: 1.4rem; font-weight: 800; color: #0B1F3B; }
        .login-logo p  { font-size: .85rem; color: #6b7280; margin-top: 3px; }
        .alert { border-radius: 10px; padding: 11px 14px; font-size: .86rem; margin-bottom: 18px; display: flex; gap: 10px; align-items: flex-start; }
        .alert-red    { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; }
        .alert-yellow { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: .85rem; font-weight: 600; color: #0B1F3B; margin-bottom: 7px; }
        .input-wrap { position: relative; }
        .input-wrap i { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: .9rem; }
        input { width: 100%; padding: 12px 14px 12px 40px; border: 1.5px solid #e5e7eb; border-radius: 10px; font-size: .95rem; color: #0B1F3B; font-family: inherit; outline: none; transition: border-color .2s; }
        input:focus { border-color: #0FA958; }
        .login-btn { width: 100%; background: #0FA958; color: #fff; border: none; border-radius: 10px; padding: 13px; font-size: 1rem; font-weight: 700; cursor: pointer; font-family: inherit; transition: background .2s; margin-top: 4px; }
        .login-btn:hover { background: #0d9048; }
        .back-link { text-align: center; margin-top: 18px; font-size: .82rem; color: #6b7280; }
        .back-link a { color: #0FA958; font-weight: 600; text-decoration: none; }
    </style>
</head>
<body>
<div class="login-box">
    <div class="login-logo">
        <div class="logo-mark"><i class="fa-solid fa-shield-halved"></i></div>
        <h1>Readiwork Admin</h1>
        <p>Sign in to manage your dashboard</p>
    </div>

    @if(request()->query('timeout'))
    <div class="alert alert-yellow"><i class="fa-solid fa-clock"></i> Your session expired. Please sign in again.</div>
    @endif

    @if($errors->has('email'))
    <div class="alert alert-red"><i class="fa-solid fa-circle-xmark"></i> {{ $errors->first('email') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.login.post') }}">
        @csrf
        <div class="form-group">
            <label for="email">Email Address</label>
            <div class="input-wrap">
                <i class="fa-solid fa-envelope"></i>
                <input type="email" id="email" name="email" required autocomplete="email" placeholder="admin@readi.work" value="{{ old('email') }}">
            </div>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <div class="input-wrap">
                <i class="fa-solid fa-lock"></i>
                <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="••••••••">
            </div>
        </div>
        <button type="submit" class="login-btn"><i class="fa-solid fa-arrow-right-to-bracket"></i> Sign In</button>
    </form>

    <p class="back-link"><a href="{{ route('home') }}"><i class="fa-solid fa-arrow-left"></i> Back to Readiwork</a></p>
</div>
</body>
</html>
