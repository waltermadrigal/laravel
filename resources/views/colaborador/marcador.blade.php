<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Sistema de Asistencia - ASEFYL</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#f1f0eb;color:#1a1a18;min-height:100vh;display:flex;align-items:center;justify-content:center}
.demo-banner{position:fixed;top:0;left:0;right:0;background:#e24b4a;color:#fff;text-align:center;padding:8px;font-size:13px;font-weight:600}
.mk{max-width:380px;text-align:center;background:#fff;padding:28px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.08)}
.mkt{font-size:38px;font-weight:500;letter-spacing:-1px;margin-bottom:6px}
.mki-field{width:100%;padding:10px;margin-bottom:12px;border:1px solid #ddd;border-radius:8px;font-size:13px}
.mkb{width:100%;padding:12px;border-radius:12px;border:none;font-size:14px;font-weight:500;cursor:pointer;margin-bottom:8px;color:#fff}
.mki{background:#639922}
.mko{background:#e24b4a}
.msg{font-size:13px;margin-top:10px;min-height:18px}
.logout{font-size:12px;color:#888;margin-top:14px;display:inline-block}
</style>
</head>
<body>
@if($demoMode)
<div class="demo-banner">MODO DEMOSTRACION - la firma digital NO se esta validando de verdad</div>
@endif
<div class="mk">
  <div class="mkt" id="clk">--:--:--</div>
  <div style="font-size:12px;color:#888;margin-bottom:14px">{{ auth()->user()->name }}</div>

  @if($demoMode)
  <input type="text" id="deviceCode" class="mki-field" placeholder="Codigo de equipo" value="DEMO-STATION">
  @endif

  <button class="mkb mki" onclick="marcar('in')">Registrar entrada</button>
  <button class="mkb mko" onclick="marcar('out')">Registrar salida</button>
  <div class="msg" id="msg"></div>

  <form method="POST" action="{{ url('/logout') }}">
    @csrf
    <button type="submit" class="logout" style="background:none;border:none;cursor:pointer;text-decoration:underline">Cerrar sesion</button>
  </form>
</div>

<script>
setInterval(() => {
  const n = new Date();
  document.getElementById('clk').textContent = String(n.getHours()).padStart(2,'0') + ':' + String(n.getMinutes()).padStart(2,'0') + ':' + String(n.getSeconds()).padStart(2,'0');
}, 1000);

async function firmarLocal(timestamp) {
  const res = await fetch('http://127.0.0.1:9111/', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ timestamp })
  });
  if (!res.ok) throw new Error('El firmador local no respondio');
  const data = await res.json();
  return data.signature;
}

async function marcar(tipo) {
  const msg = document.getElementById('msg');
  msg.textContent = 'Procesando...';
  const timestamp = new Date().toISOString();
  const deviceCode = document.getElementById('deviceCode')
    ? document.getElementById('deviceCode').value
    : 'ASEFYL-PC-DEFAULT';

  try {
    let signature;
    try {
      signature = await firmarLocal(timestamp);
    } catch (e) {
      msg.textContent = 'Error: el firmador local no esta activo en esta PC.';
      return;
    }

    const endpoint = tipo === 'in' ? '/api/check-in' : '/api/check-out';
    const res = await fetch(endpoint, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
      },
      body: JSON.stringify({ timestamp, signature, device_code: deviceCode })
    });
    const data = await res.json();

    if (!res.ok) {
      msg.textContent = 'Error: ' + (data.error || 'no se pudo registrar la marca.');
      return;
    }

    msg.textContent = 'Marca registrada a las ' + data.time + (data.status ? ' (' + data.status + ')' : '');
  } catch (err) {
    msg.textContent = 'Error inesperado: ' + err.message;
  }
}
</script>
</body>
</html>
