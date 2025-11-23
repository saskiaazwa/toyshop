<?php session_start(); if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'user') { header('Location: login.php'); exit; } ?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>User - Toy Universe</title><link rel="stylesheet" href="assets/style.css?v=1"></head><body>
<div class="container"><header class="header"><div class="brand"><strong>Toy Universe</strong><span class="small"> Hello, <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></span></div><div class="topbar"><a href="cart.php" class="btn ghost">Cart 🛒</a><a href="logout.php" class="btn ghost">Logout</a>
</div></header>
<h3>Products</h3><div id="grid" class="card-grid"></div></div>
<script>
async function load(){ const res = await fetch('api/products.php'); const data = await res.json(); const grid = document.getElementById('grid');
grid.innerHTML=''; data.forEach(p=>{ const el=document.createElement('div'); el.className='card';
el.innerHTML = `
    <img src="${p.image_url||'assets/img/toy1.svg'}" alt="">
    <h3>${escape(p.name)}</h3>
    <p class="small">${escape(p.description||'')}</p>
    <div class="admin-actions">
        <span class="badge">$${p.price}</span>
        <form action="add_to_cart.php" method="POST" style="display:inline;">
            <input type="hidden" name="product_id" value="${p.id}">
            <button type="submit" class="btn small">Add to Cart</button>
        </form>
    </div>
`;
grid.appendChild(el); }); } function escape(s){ return String(s).replaceAll('&','&amp;')
.replaceAll('<','&lt;').replaceAll('>','&gt;'); } load();</script></body></html>