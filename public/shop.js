(() => {
  'use strict';
  const app = document.getElementById('app');
  const nav = document.getElementById('account-nav');
  const notice = document.getElementById('notice');
  const dialog = document.getElementById('delete-dialog');
  const key = 'timedoor-shop-session';
  let session = null;
  try { session = JSON.parse(sessionStorage.getItem(key)); } catch { sessionStorage.removeItem(key); }
  let categories = [], brands = [], pendingDelete = null, pageVersion = 0;
  const filters = {keyword:'',sort:'newest',page:1};
  const esc = value => String(value ?? '').replace(/[&<>"']/g,c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const money = value => new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',maximumFractionDigits:0}).format(Number(value));
  function saveSession(value) { session=value; if(value)sessionStorage.setItem(key,JSON.stringify(value));else sessionStorage.removeItem(key); renderNav(); }
  async function api(url,method='GET',body) {
    const headers={Accept:'application/json'};
    if(session?.token)headers.Authorization='Bearer '+session.token;
    if(body)headers['Content-Type']='application/json';
    let response;
    try { response=await fetch('/api'+url,{method,headers,body:body?JSON.stringify(body):undefined,credentials:'omit'}); }
    catch { throw new Error('Server belum dapat dihubungi. Coba lagi sebentar.'); }
    let data=null;
    if(response.status!==204) { try { data=await response.json(); } catch { throw new Error('Respons server belum dapat dibaca. Coba lagi.'); } }
    if(!response.ok) {
      if(response.status===401 && session)saveSession(null);
      const text=data?.errors?Object.values(data.errors).flat().join('\n'):data?.message;
      throw new Error(text || 'Permintaan belum berhasil. Coba lagi.');
    }
    return data;
  }
  function renderNav() {
    nav.innerHTML=session?`<span class="user-name">${esc(session.user.name)}</span><a href="#home">Home</a><button class="text-button" id="logout">Logout</button>`:'<a href="#login">Login</a><a href="#register">Register</a>';
    document.getElementById('logout')?.addEventListener('click',async event=>{
      event.currentTarget.disabled=true;
      try { await api('/logout','POST'); saveSession(null); notice.textContent='Kamu sudah logout.'; if(location.hash==='#home'||!location.hash)render();else location.hash='home'; }
      catch(error) { notice.textContent=error.message; renderNav(); }
    });
  }
  const field=(label,name,type='text',value='',extra='')=>`<div class="field"><label for="${name}">${label}</label><input id="${name}" name="${name}" type="${type}" value="${esc(value)}" ${extra}></div>`;
  function formPanel(title,contents,submit,footer='') {
    app.innerHTML=`<section class="panel"><h1>${title}</h1><form id="main-form"><p class="form-error" role="alert" id="form-error"></p>${contents}<div class="actions"><a class="button secondary" href="#home">Batal</a><button type="submit">${submit}</button></div>${footer}</form></section>`;
    return document.getElementById('main-form');
  }
  function handleForm(form,callback) {
    form.addEventListener('submit',async event=>{
      event.preventDefault();const button=form.querySelector('button[type=submit]');const errors=document.getElementById('form-error');errors.textContent='';button.disabled=true;
      try { await callback(Object.fromEntries(new FormData(form))); }
      catch(error) { errors.textContent=error.message; }
      finally { button.disabled=false; }
    });
  }
  function authForm(register) {
    notice.textContent='';
    const form=formPanel(register?'Register':'Login',
      (register?field('Nama','name','text','','required maxlength="100" autocomplete="name"'):'')+
      field('Email','email','email','','required autocomplete="email"')+
      field('Password','password','password','','required '+(register?'minlength="8" autocomplete="new-password"':'autocomplete="current-password"'))+
      (register?field('Konfirmasi password','password_confirmation','password','','required minlength="8" autocomplete="new-password"'):''),register?'Register':'Login',
      `<p class="form-footer">${register?'Sudah punya akun? <a href="#login">Login</a>':'Belum punya akun? <a href="#register">Register</a>'}</p>`);
    handleForm(form,async data=>{const result=await api(register?'/register':'/login','POST',data);saveSession(result.data);notice.textContent=register?'Akun berhasil dibuat. Kamu sudah login.':'Login berhasil.';location.hash='home';});
  }
  async function home(version) {
    app.innerHTML=`<h1>Daftar produk</h1><div class="toolbar">${session?'<a class="button" href="#create">Tambah produk</a>':''}<form class="search-form" id="search-form"><input aria-label="Cari produk" placeholder="Cari produk…" name="keyword" value="${esc(filters.keyword)}" maxlength="100"><button type="submit">Cari</button><button type="button" class="secondary" id="clear-search">Clear</button></form><label class="sort-label">Urutkan <select id="sort"><option value="newest">Terbaru</option><option value="name_asc">Nama A–Z</option><option value="name_desc">Nama Z–A</option><option value="price_asc">Harga termurah</option><option value="price_desc">Harga termahal</option></select></label></div><div id="catalog"><p role="status">Memuat produk…</p></div>`;
    document.getElementById('sort').value=filters.sort;
    document.getElementById('sort').addEventListener('change',event=>{filters.sort=event.target.value;filters.page=1;render();});
    document.getElementById('search-form').addEventListener('submit',event=>{event.preventDefault();filters.keyword=new FormData(event.currentTarget).get('keyword').trim();filters.page=1;render();});
    document.getElementById('clear-search').addEventListener('click',()=>{filters.keyword='';filters.page=1;render();});
    try {
      const query=new URLSearchParams({...filters,per_page:10});const result=await api('/products?'+query);
      if(version!==pageVersion)return;
      const catalog=document.getElementById('catalog');
      if(!result.data.length){catalog.innerHTML='<p class="empty">Produk tidak ditemukan. Coba kata pencarian lain.</p>';return;}
      catalog.innerHTML=`<div class="products">${result.data.map(product=>`<article class="product"><div class="product-picture" data-picture="${product.id}">Belum ada gambar</div><div class="product-body"><h2 class="product-name">${esc(product.name)}</h2><p class="price">${money(product.price)}</p><p class="stock">Stok: ${esc(product.stock)}</p><p class="meta">${esc(product.category?.name)} / ${esc(product.brand?.name)}</p></div>${session?`<div class="product-actions"><a href="#edit/${product.id}" aria-label="Edit ${esc(product.name)}">Edit</a><button type="button" data-delete="${product.id}" aria-label="Hapus ${esc(product.name)}">Hapus</button></div>`:''}</article>`).join('')}</div><div class="pagination"><button class="secondary" id="previous" ${result.current_page<=1?'disabled':''}>Sebelumnya</button><span>Halaman ${result.current_page} dari ${result.last_page}</span><button class="secondary" id="next" ${result.current_page>=result.last_page?'disabled':''}>Berikutnya</button></div>`;
      for(const product of result.data) {
        try { const url=new URL(product.image_url);if(!['http:','https:'].includes(url.protocol))continue;const picture=catalog.querySelector(`[data-picture="${product.id}"]`);const image=new Image();image.alt=product.name;image.referrerPolicy='no-referrer';image.loading='lazy';image.addEventListener('load',()=>picture.replaceChildren(image));image.src=url.href; } catch {}
      }
      document.getElementById('previous').addEventListener('click',()=>{filters.page--;render();});document.getElementById('next').addEventListener('click',()=>{filters.page++;render();});
      catalog.querySelectorAll('[data-delete]').forEach(button=>button.addEventListener('click',()=>{pendingDelete=result.data.find(p=>String(p.id)===button.dataset.delete);document.getElementById('delete-name').textContent=pendingDelete.name;document.getElementById('delete-error').textContent='';dialog.showModal();}));
    } catch(error) { if(version===pageVersion)document.getElementById('catalog').innerHTML=`<p class="form-error" role="alert">${esc(error.message)}</p>`; }
  }
  async function productForm(id,version) {
    if(!session){location.hash='login';return;}
    notice.textContent='';
    app.innerHTML='<p role="status">Memuat form…</p>';
    const [categoryData,brandData,productData]=await Promise.all([api('/categories'),api('/brands'),id?api('/products/'+id):Promise.resolve({data:{}})]);
    if(version!==pageVersion)return;
    categories=categoryData.data;brands=brandData.data;const product=productData.data;
    const select=(label,name,items,value)=>`<div class="field"><label for="${name}">${label}</label><select id="${name}" name="${name}" required><option value="">Pilih ${label.toLowerCase()}</option>${items.map(item=>`<option value="${item.id}" ${String(item.id)===String(value)?'selected':''}>${esc(item.name)}</option>`).join('')}</select></div>`;
    const form=formPanel(id?'Edit produk':'Tambah produk baru',
      field('Nama produk','name','text',product.name,'required maxlength="100"')+
      field('Link URL gambar (opsional)','image_url','url',product.image_url,'maxlength="2048"')+
      field('Harga','price','number',product.price,'required min="0" max="99999999.99" step="0.01"')+
      field('Stok','stock','number',product.stock,'required min="0" max="2147483647" step="1"')+
      select('Kategori','category_id',categories,product.category_id)+select('Merek','brand_id',brands,product.brand_id),id?'Simpan perubahan':'Simpan');
    handleForm(form,async data=>{if(data.image_url&&!/^https?:\/\//i.test(data.image_url))throw new Error('Gunakan URL gambar yang diawali http:// atau https://.');data.image_url=data.image_url||null;await api(id?'/products/'+id:'/products',id?'PUT':'POST',data);notice.textContent=id?'Produk berhasil diubah.':'Produk berhasil ditambahkan.';filters.page=1;filters.keyword='';filters.sort='newest';location.hash='home';});
  }
  async function render() {
    const version=++pageVersion;renderNav();const route=location.hash.slice(1)||'home';
    try { if(route==='login'||route==='register')authForm(route==='register');else if(route==='create')await productForm(null,version);else if(/^edit\/\d+$/.test(route))await productForm(route.split('/')[1],version);else await home(version); }
    catch(error) { if(version===pageVersion)app.innerHTML=`<p class="form-error" role="alert">${esc(error.message)}</p><a href="#home">Kembali ke produk</a>`; }
  }
  document.getElementById('cancel-delete').addEventListener('click',()=>dialog.close());
  document.getElementById('confirm-delete').addEventListener('click',async event=>{
    if(!pendingDelete)return;event.currentTarget.disabled=true;
    try { await api('/products/'+pendingDelete.id,'DELETE');dialog.close();notice.textContent='Produk berhasil dihapus.';pendingDelete=null;filters.page=1;await render(); }
    catch(error) { document.getElementById('delete-error').textContent=error.message; }
    finally { event.currentTarget.disabled=false; }
  });
  addEventListener('hashchange',render);
  render();
})();
