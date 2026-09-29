<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Ingresar - ASEFYL</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#f1f0eb;color:#1a1a18;min-height:100vh;display:flex;align-items:center;justify-content:center}
.box{max-width:360px;width:100%;background:#fff;padding:28px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.08)}
h1{font-size:20px;margin-bottom:18px;text-align:center}
label{font-size:12px;color:#666;display:block;margin-bottom:4px}
input{width:100%;padding:10px;margin-bottom:14px;border:1px solid #ddd;border-radius:8px;font-size:14px}
button{width:100%;padding:12px;border-radius:10px;border:none;font-size:14px;font-weight:600;cursor:pointer;color:#fff;background:#185fa5}
.err{background:#fdecea;color:#b3261e;padding:10px;border-radius:8px;font-size:13px;margin-bottom:14px}
</style>
</head>
<body>
<div class="box">
  <h1>ASEFYL - Control de Asistencia</h1>
  @if ($errors->any())
    <div class="err">{{ $errors->first() }}</div>
  @endif
  <form method="POST" action="{{ url('/login') }}">
    @csrf
    <label>Correo</label>
    <input type="email" name="email" value="{{ old('email') }}" required autofocus>
    <label>Contraseña</label>
    <input type="password" name="password" required>
    <button type="submit">Ingresar</button>
  </form>
</div>
</body>
</html>
