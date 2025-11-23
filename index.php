<?php session_start(); if(isset($_SESSION['user_role']) && $_SESSION['user_role']==='admin')
{ header('Location: admin.php'); exit; } elseif(isset($_SESSION['user_role']) && $_SESSION['user_role']==='user'){ header('Location: user.php'); exit; } ?>
<!doctype html><html><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Toy Universe</title><link rel="stylesheet" href="assets/style.css?v=2"></head><body>
<header class="container header"><div class="text-xl font-bold"><h2>Toy Universe</h2></div>
<nav class="nav"><a href="#">New Arrivals</a>
<a href="#">Best Sellers</a><a href="#categories">Categories</a>
</nav><div class="topbar"><a href="login.php" class="btn ghost">Login</a></div></header>
<main class="container">
  <section class="hero"><div class="hero-content"><h1>Discover the Magic of Play</h1><p>Explore our curated collection of toys that spark imagination and learning.</p><a class="btn" href="#products">Shop Now</a></div></section>
  <section id="products"><h2>New Arrivals</h2><div id="grid" class="card-grid"></div></section>
   
   <section id="best-sellers" class="mt-3">
  <h2>Best Sellers</h2>
  <div class="promo-banner card-grid">
    <div class="promo-card" style="background-image: url('assets/img/pop_mart_hacipupu_stitch_serie_1751102158_6fa9944b_progressive.jpg');">
      <div class="overlay">
        <h3>Hacipupu Promo<br>Up to 20%</h3>
      </div>
    </div>

    <div class="promo-card" style="background-image: url('assets/img/plushies-banner.jpg');">
      <div class="overlay">
        <h3>Plushies Collection<br>Buy 1 Get 1</h3>
      </div>
    </div>
  </div>
</section>

<style>
  /* Grid promo */
  .promo-banner {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
  }

  .promo-card {
    position: relative;
    height: 250px;
    background-size: cover;
    background-position: center;
    border-radius: 1rem;
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    transition: transform 0.3s ease;
  }

  .promo-card:hover {
    transform: scale(1.02);
  }

  .promo-card .overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.4);
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    color: white;
    font-size: 1.5rem;
    font-weight: bold;
    padding: 1rem;
  }
</style>

  <section id="categories" class="mt-5">
    <h2>Categories</h2>
    <div id="category-grid" class="card-grid">
    </div>
  </section>

</main>
<script>
async function load(){ const res = await fetch('api/products.php'); const data = await res.json(); const grid = document.getElementById('grid');
grid.innerHTML=''; data.forEach(p=>{ const el = document.createElement('div'); el.className='card';
el.innerHTML = `<img src="${p.image_url||'assets/img/toy1.svg'}" alt=""><h3>${escape(p.name)}</h3><p class="small">${escape(p.description||'')}</p><div class="admin-actions"><span class="badge">$
${p.price}</span></div>`; grid.appendChild(el); }); }
function escape(s){ return String(s).replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;'); } load();</script>

<script>
  async function loadCategories() {
    const grid = document.getElementById('category-grid');
    grid.innerHTML = '';

    // Bisa diambil dari database API juga, sementara contoh statis dulu
    const categories = [
      { name: 'Mini Figures', image: 'assets/img/img_candy-store_01.png' },
      { name: 'Dolls & Plushies', image: 'assets/img/Scb9d7ddd7a7a4b5c8ea649983e60d890I.jpg_720x720q80.jpg' },
      { name: 'Outdoor Toys', image: 'assets/img/Best-Outdoor-toys-for-kids.jpg' },
    ];

    categories.forEach(cat => {
      const el = document.createElement('div');
      el.className = 'card category-card';
      el.innerHTML = `
        <img src="${cat.image}" alt="${cat.name}">
        <h3>${cat.name}</h3>
      `;
      grid.appendChild(el);
    });
  }

  // Panggil fungsi ini saat halaman dimuat
  loadCategories();
</script>

</body></html>