<?php session_start(); if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') { header('Location: login.php'); exit; } ?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin - Toy Universe</title><link rel="stylesheet" href="assets/style.css?v=1"></head><body>
<div class="container"><header class="header"><div class="brand"><strong>Admin Dashboard</strong><span class="small"> Welcome, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></span></div><div class="topbar"><button id="btnAdd" class="btn">+ Add Product</button><a href="logout.php" class="btn ghost">Logout</a></div></header>
<h3>Manage Products</h3><div id="grid" class="card-grid"></div></div>

<div id="modal" style="display:none" class="modal"><div class="box"><h3 id="m-title">Add Product</h3>
<form id="productForm" enctype="multipart/form-data">
  <input type="hidden" name="action" value="create">
  <input type="hidden" name="id" value="">
  <input class="input" name="name" placeholder="Name" required>
  <input class="input" name="price" placeholder="Price" required>
  <input class="input" type="file" name="image" accept="image/*">
  <textarea class="input" name="description" placeholder="Description"></textarea>
  <div style="display:flex;gap:8px;justify-content:flex-end"><button id="save" class="btn" type="submit">Save</button><button id="cancel" class="btn ghost" type="button">Cancel</button></div>
</form>
</div></div>

<script>
const modal = document.getElementById('modal'); const form = document.getElementById('productForm');
function showModal(title){ document.getElementById('m-title').textContent = title; modal.style.display='flex'; }
function hideModal(){ modal.style.display='none'; form.reset(); form.action.value='create'; form.id.value=''; }
document.getElementById('btnAdd').addEventListener('click', ()=>{ showModal('Add Product'); });
document.getElementById('cancel').addEventListener('click', hideModal);
async function load(){ const res = await fetch('api/products.php'); const data = await res.json(); const grid = document.getElementById('grid'); grid.innerHTML=''; data.forEach(p=>{ const el=document.createElement('div'); el.className='card'; el.innerHTML = `<img src="${p.image_url||'assets/img/toy1.svg'}" alt=""><h3>${escape(p.name)}</h3><p class="small">${escape(p.description||'')}</p><div class="admin-actions"><span class="badge">$${p.price}</span><button class="btn ghost" onclick="edit(${p.id})">Edit</button><button class="btn" onclick="del(${p.id})">Delete</button></div>`; grid.appendChild(el); }); }
async function edit(id){ const res = await fetch('api/products.php?id='+id); const p = await res.json(); showModal('Edit Product'); form.action.value='update'; form.id.value=p.id; form.name.value=p.name; form.price.value=p.price; form.description.value=p.description; }
form.addEventListener('submit', async (e)=>{ e.preventDefault(); const fd = new FormData(form); const res = await fetch('api/products.php', { method: 'POST', body: fd }); const j = await res.json(); if (j.ok) { hideModal(); load(); } else alert(j.error||'Failed'); });
async function del(id){ if(!confirm('Delete product?')) return; const fd = new FormData(); fd.append('action','delete'); fd.append('id', id); const res = await fetch('api/products.php', { method:'POST', body: fd }); const j = await res.json(); if (j.ok) load(); else alert(j.error||'Failed'); }
function escape(s){ return String(s).replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;'); } load();
</script>
</body></html>