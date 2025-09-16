// Simplified & Cleaned FlipMart+ Storefront Logic
// Uses PRODUCTS array from script2.js

// ---------- Shortcuts ----------
const $ = sel => document.querySelector(sel);
const $$ = sel => Array.from(document.querySelectorAll(sel));
const formatPrice = v => '₹' + Number(v).toLocaleString('en-IN');

// ---------- State ----------
let state = {
  products: PRODUCTS.slice(),
  query: '',
  category: '',
  maxPrice: null,
  sort: 'relevance',
  cart: load('fk_cart_v1', {}),
  wishlist: load('fk_wishlist_v1', []),
  compare: load('fk_compare_v1', []),
  page: 1,
  perPage: 12
};

// ---------- Elements ----------
const productsGrid = $('#productsGrid');
const categoryList = $('#categoryList');
const searchInput = $('#searchInput');
const searchBtn = $('#searchBtn');
const sortSelect = $('#sortSelect');
const cartBtn = $('#cartBtn');
const cartCount = $('#cartCount');
const productModal = $('#productModal');
const modalBody = $('#modalBody');
const modalClose = $('#modalClose');
const cartDrawer = $('#cartDrawer');
const cartItemsEl = $('#cartItems');
const closeCart = $('#closeCart');
const cartSubtotalEl = $('#cartSubtotal');
const checkoutBtn = $('#checkoutBtn');
const clearCartBtn = $('#clearCartBtn');
const noResults = $('#noResults');
const loadMoreBtn = $('#loadMore');
const wishlistPanel = $('#wishlistPanel');
const comparePanel = $('#comparePanel');
const compareModal = $('#compareModal');
const compareBody = $('#compareBody');
const compareClose = $('#compareClose');
const openCompareBtn = $('#openCompare');
const themeToggle = $('#themeToggle');

// ---------- Init ----------
init();
function init(){
  hydrateTheme();
  renderCategories();
  renderProducts(true);
  bindEvents();
  updateCartUI();
  renderWishlistPanel();
  renderComparePanel();
}

// ---------- Storage Helpers ----------
function load(key, fallback){
  try { return JSON.parse(localStorage.getItem(key)) ?? fallback; }
  catch(e){ return fallback; }
}
function save(key, val){ localStorage.setItem(key, JSON.stringify(val)); }

// ---------- Render Categories ----------
function uniqueCategories(){
  return Array.from(new Set(PRODUCTS.map(p => p.category)));
}

function renderCategories(){
  if (!categoryList) return;
  categoryList.innerHTML = '';
  ['All', ...uniqueCategories()].forEach(cat => {
    const li = document.createElement('li');
    const btn = document.createElement('button');
    btn.textContent = cat;
    btn.className = (state.category === '' && cat==='All') || state.category === cat ? 'active' : '';
    btn.addEventListener('click', ()=>{
      state.category = (cat === 'All') ? '' : cat;
      state.page = 1;
      renderProducts(true);
      renderCategories();
    });
    li.appendChild(btn);
    categoryList.appendChild(li);
  });
}

// ---------- Product Filtering & Rendering ----------
function filteredList(){
  let list = PRODUCTS.slice();
  if (state.category) list = list.filter(p => p.category === state.category);
  if (state.maxPrice) list = list.filter(p => p.price <= state.maxPrice);
  if (state.query) {
    const q = state.query.toLowerCase();
    list = list.filter(p => (p.title + ' ' + p.category).toLowerCase().includes(q));
  }
  if (state.sort === 'price-asc') list.sort((a,b)=>a.price-b.price);
  if (state.sort === 'price-desc') list.sort((a,b)=>b.price-a.price);
  if (state.sort === 'rating-desc') list.sort((a,b)=>b.rating-a.rating);
  return list;
}

function renderProducts(reset=false){
  if (reset) { state.page=1; productsGrid.innerHTML=''; }
  const list = filteredList();
  const pageItems = list.slice(0, state.page*state.perPage);

  productsGrid.innerHTML = '';
  if (!pageItems.length) { noResults.hidden = false; return; }
  noResults.hidden = true;
  pageItems.forEach(p => productsGrid.appendChild(productCard(p)));
  loadMoreBtn.style.display = (state.page*state.perPage < list.length) ? 'block' : 'none';
}

function productCard(p){
  const card = document.createElement('article');
  card.className = 'product-card';
  card.innerHTML = `
    <img src="${p.img}" alt="${p.title}" class="product-image" />
    <div class="product-title">${p.title}</div>
    <div class="product-meta">
      <span class="product-price">${formatPrice(p.price)}</span>
      ${p.mrp ? `<span class="product-mrp">${formatPrice(p.mrp)}</span>` : ''}
      <span class="product-rating">⭐ ${p.rating}</span>
    </div>
    <div class="card-actions">
      <button class="btn btn-outline viewBtn">Quick View</button>
      <button class="btn btn-primary addBtn">Add to Cart</button>
    </div>
    <div class="extra-actions">
      <button class="muted wishBtn">${state.wishlist.includes(p.id)?'❤️ In Wishlist':'♡ Wishlist'}</button>
      <button class="muted compareBtn">${state.compare.includes(p.id)?'✓ In Compare':'⇄ Compare'}</button>
    </div>
  `;
  card.querySelector('.viewBtn').addEventListener('click', ()=>openProductModal(p.id));
  card.querySelector('.addBtn').addEventListener('click', ()=>addToCart(p.id,1));
  card.querySelector('.wishBtn').addEventListener('click', ()=>toggleWishlist(p.id));
  card.querySelector('.compareBtn').addEventListener('click', ()=>toggleCompare(p.id));
  return card;
}

// ---------- Product Modal ----------
function openProductModal(id){
  const p = PRODUCTS.find(x => x.id === id);
  if (!p) return;
  productModal.setAttribute('aria-hidden','false');
  modalBody.innerHTML = `
    <h2>${p.title}</h2>
    <img src="${p.img}" style="width:100%;max-height:300px;object-fit:cover;border-radius:10px;" />
    <p>${formatPrice(p.price)} ${p.mrp?`<span class="product-mrp">${formatPrice(p.mrp)}</span>`:''}</p>
    <p>⭐ ${p.rating} • ${p.category}</p>
    <div class="modal-actions">
      <button id="modalAdd" class="btn btn-primary">Add to Cart</button>
      <button id="modalBuy" class="btn btn-outline">Buy Now</button>
    </div>
  `;
  $('#modalAdd').addEventListener('click', ()=>addToCart(p.id,1));
  $('#modalBuy').addEventListener('click', ()=>{ addToCart(p.id,1); openCart(); });
}
modalClose.addEventListener('click', ()=>productModal.setAttribute('aria-hidden','true'));

// ---------- Cart ----------
function cartItemsArray(){
  return Object.entries(state.cart).map(([id, qty]) => {
    const p = PRODUCTS.find(x=>x.id===id);
    return p ? {...p, qty} : null;
  }).filter(Boolean);
}

function updateCartUI(){
  const items = cartItemsArray();
  cartItemsEl.innerHTML = items.length ? '' : '<div>Your cart is empty</div>';
  items.forEach(item => {
    const div = document.createElement('div');
    div.className='cart-item';
    div.innerHTML = `
      <img src="${item.img}" alt="${item.title}" />
      <div style="flex:1;">
        <div>${item.title}</div>
        <div>${formatPrice(item.price)} × ${item.qty}</div>
      </div>
      <button data-id="${item.id}" class="remove">Remove</button>
    `;
    cartItemsEl.appendChild(div);
  });
  $$('.remove').forEach(b=>b.addEventListener('click',e=>setCartQty(e.target.dataset.id,0)));
  cartSubtotalEl.textContent = formatPrice(items.reduce((s,it)=>s+it.price*it.qty,0));
  cartCount.textContent = items.reduce((s,it)=>s+it.qty,0);
}

function addToCart(id, qty=1){
  state.cart[id] = (state.cart[id]||0)+qty;
  if (state.cart[id]<=0) delete state.cart[id];
  save('fk_cart_v1', state.cart);
  updateCartUI();
  window.toastManager?.show('Added to cart','success');
}

function setCartQty(id, qty){
  if (qty<=0) delete state.cart[id]; else state.cart[id]=qty;
  save('fk_cart_v1', state.cart);
  updateCartUI();
}

function clearCart(){
  state.cart={}; save('fk_cart_v1', state.cart); updateCartUI();
}

function openCart(){ cartDrawer.classList.add('open'); updateCartUI(); }
function closeCartDrawer(){ cartDrawer.classList.remove('open'); }

// ✅ Updated Checkout (clear + redirect)
checkoutBtn.addEventListener('click', ()=>{
  const items = cartItemsArray();
  if (!items.length) { 
    window.toastManager.show('⚠️ Cart is empty!','warning'); 
    return; 
  }

  clearCart(); // cart empty ho jaayega

  // Redirect to confirmation page
  window.location.href = "order-confirmation.php";
});

// ---------- Wishlist ----------
function toggleWishlist(id){
  const idx = state.wishlist.indexOf(id);
  if (idx>=0) state.wishlist.splice(idx,1); else state.wishlist.push(id);
  save('fk_wishlist_v1', state.wishlist);
  renderProducts(); renderWishlistPanel();
}
function renderWishlistPanel(){
  if (!wishlistPanel) return;
  wishlistPanel.textContent = state.wishlist.length? 'Items in wishlist' : 'Empty wishlist';
}

// ---------- Compare ----------
function toggleCompare(id){
  const set = new Set(state.compare);
  set.has(id) ? set.delete(id) : set.add(id);
  state.compare = [...set];
  save('fk_compare_v1', state.compare);
  renderProducts(); renderComparePanel();
}
function renderComparePanel(){
  if (!comparePanel) return;
  comparePanel.textContent = state.compare.length ? 'Compare ready' : 'Add 2–4 items to compare';
}

// ---------- Theme ----------
function hydrateTheme(){
  const theme = load('fk_theme','dark');
  document.documentElement.setAttribute('data-theme',theme);
  if (themeToggle) themeToggle.textContent = (theme==='light'?'🌞':'🌙');
}
function toggleTheme(){
  const theme = document.documentElement.getAttribute('data-theme')==='light'?'dark':'light';
  document.documentElement.setAttribute('data-theme',theme);
  save('fk_theme',theme);
  if (themeToggle) themeToggle.textContent = (theme==='light'?'🌞':'🌙');
}

// ---------- Events ----------
function bindEvents(){
  searchBtn?.addEventListener('click',()=>{ state.query=searchInput.value.trim(); renderProducts(true); });
  searchInput?.addEventListener('keydown',e=>{ if(e.key==='Enter') searchBtn.click(); });
  sortSelect?.addEventListener('change',e=>{ state.sort=e.target.value; renderProducts(true); });
  loadMoreBtn?.addEventListener('click',()=>{ state.page++; renderProducts(); });
  cartBtn?.addEventListener('click',openCart);
  closeCart?.addEventListener('click',closeCartDrawer);
  clearCartBtn?.addEventListener('click',clearCart);
  openCompareBtn?.addEventListener('click',()=>compareModal.setAttribute('aria-hidden','false'));
  compareClose?.addEventListener('click',()=>compareModal.setAttribute('aria-hidden','true'));
  themeToggle?.addEventListener('click',toggleTheme);
}
