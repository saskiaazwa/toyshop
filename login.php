<?php session_start(); if(isset($_SESSION['user_role'])){ if($_SESSION['user_role']==='admin'){ header('Location: admin.php'); } else { header('Location: user.php'); } exit; } ?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login - Toy Universe</title><link rel="stylesheet" href="assets/style.css?v=1"></head><body>
<div class="container"><header class="header"><div class="brand"><strong>Toy Universe</strong></div></header>
<div class="form"><h3>Login</h3><div id="msg" class="small"></div><input id="email" class="input" placeholder="Email" type="email"><input id="password" class="input" placeholder="Password" type="password"><div><button id="btn" class="btn">Login</button> <a href="register.php" class="small">Register</a></div></div></div>
<script>
document.getElementById('btn').addEventListener('click', async ()=>{
  const email = document.getElementById('email').value.trim(); const password = document.getElementById('password').value; const msg = document.getElementById('msg'); msg.textContent='';
  const res = await fetch('api/auth.php?action=login', {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({email, password})});
  const j = await res.json();
  if (j.ok) { if (j.role==='admin') location.href='admin.php'; else location.href='user.php'; } else { msg.textContent = j.error || 'Login failed'; }
});
</script></body></html>