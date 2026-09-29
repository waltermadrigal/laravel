<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel - ASEFYL</title>
  
</head>
<body style="font-family:sans-serif;padding:24px">
<h1>Panel de administracion</h1>
<p>Usuario: {{ auth()->user()->name }} ({{ auth()->user()->role }})</p>
<p>Este panel es un placeholder - reportes, equipos y permisos van aca (Modulo 3).</p>
<form method="POST" action="{{ url('/logout') }}">
@csrf
<button type="submit">Cerrar sesion
</button>
</form>
</body>
</html>
