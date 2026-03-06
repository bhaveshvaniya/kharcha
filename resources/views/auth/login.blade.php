<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Kharcha</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="auth-body">
    <div class="auth-container">
        <div class="auth-logo">Kharcha<span>.</span></div>
        <div class="auth-subtitle">Your personal finance companion</div>
        <div class="auth-card">
            <h2 class="auth-title">Welcome Back</h2>
            @if($errors->any())
                <div class="alert alert-error">{{ $errors->first() }}</div>
            @endif
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="you@example.com" required autofocus>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:20px;">
                    <input type="checkbox" name="remember" id="remember" style="width:16px;height:16px;">
                    <label for="remember" style="font-size:13px;color:var(--muted)">Remember me</label>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">Sign In</button>
            </form>
            <div style="text-align:center;margin-top:20px;font-size:13px;color:var(--muted)">
                Don't have an account? <a href="{{ route('register') }}" style="color:var(--accent)">Register here</a>
            </div>
            <div style="margin-top:16px;padding:12px;background:var(--surface2);border-radius:8px;font-size:12px;color:var(--muted);text-align:center;">
                Demo: demo@kharcha.com / password
            </div>
        </div>
    </div>
</body>
</html>
