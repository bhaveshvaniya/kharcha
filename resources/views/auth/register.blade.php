<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Kharcha</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="auth-body">
    <div class="auth-container">
        <div class="auth-logo">Kharcha<span>.</span></div>
        <div class="auth-subtitle">Start tracking your finances today</div>
        <div class="auth-card">
            <h2 class="auth-title">Create Account</h2>
            @if($errors->any())
                <div class="alert alert-error">{{ $errors->first() }}</div>
            @endif
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Your name" required autofocus>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="you@example.com" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Min 8 characters" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:8px;">Create Account</button>
            </form>
            <div style="text-align:center;margin-top:20px;font-size:13px;color:var(--muted)">
                Already have an account? <a href="{{ route('login') }}" style="color:var(--accent)">Sign in</a>
            </div>
        </div>
    </div>
</body>
</html>
