/* ═══════════════════════════════════════════════════════════
   STORE.JS — Ana Mağaza JavaScript
═══════════════════════════════════════════════════════════ */

'use strict';

// ─── CART ─────────────────────────────────────────────────
var Cart = {
  count: 0,

  init: function() {
    this.loadCount();
  },

  loadCount: function() {
    fetch('/sepet/ozet', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function(r) { return r.json(); })
      .then(function(d) {
        if (d.count !== undefined) Cart.updateUI(d.count);
      })
      .catch(function() {});
  },

  updateUI: function(count) {
    Cart.count = count;
    var el = document.getElementById('cartCount');
    if (el) {
      el.textContent = count > 99 ? '99+' : count;
      el.style.display = count > 0 ? 'flex' : 'none';
    }
  },

  add: function(productId, variationId, qty, btn) {
    if (btn) {
      btn.disabled = true;
      btn.style.opacity = '.6';
    }

    var csrfToken = document.querySelector('input[name="_csrf_token"]');

    fetch('/sepet/ekle', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: '_csrf_token=' + (csrfToken ? csrfToken.value : '') +
            '&product_id=' + productId +
            '&variation_id=' + (variationId || '') +
            '&qty=' + (qty || 1)
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
      if (d.success || d.count !== undefined) {
        Cart.updateUI(d.count || Cart.count + 1);
        Toast.show('Ürün sepete eklendi!', 'success');
        Cart.animateBtn(btn);
      } else {
        Toast.show(d.message || 'Bir hata oluştu.', 'error');
      }
    })
    .catch(function() {
      Toast.show('Bağlantı hatası.', 'error');
    })
    .finally(function() {
      if (btn) {
        btn.disabled = false;
        btn.style.opacity = '1';
      }
    });
  },

  animateBtn: function(btn) {
    if (!btn) return;
    btn.style.background = '#16a34a';
    setTimeout(function() {
      btn.style.background = '';
    }, 1000);
  }
};

// ─── HIZLI SEPETE EKLE ────────────────────────────────────
function quickAddToCart(productId, btn) {
  Cart.add(productId, null, 1, btn);
}

// ─── WİSHLİST ────────────────────────────────────────────
function toggleWishlist(btn, productId) {
  var csrfToken = document.querySelector('input[name="_csrf_token"]');
  var isActive  = btn.classList.contains('active');

  fetch(isActive ? '/favoriler/sil' : '/favoriler/ekle', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: '_csrf_token=' + (csrfToken ? csrfToken.value : '') + '&product_id=' + productId
  })
  .then(function(r) { return r.json(); })
  .then(function(d) {
    if (d.success !== false) {
      btn.classList.toggle('active');
      Toast.show(isActive ? 'Favorilerden çıkarıldı' : 'Favorilere eklendi!', 'success');
    }
  })
  .catch(function() {});
}

// ─── TOAST BİLDİRİMİ ─────────────────────────────────────
var Toast = {
  container: null,

  init: function() {
    this.container = document.createElement('div');
    this.container.id = 'toastContainer';
    this.container.style.cssText = [
      'position:fixed',
      'bottom:24px',
      'left:50%',
      'transform:translateX(-50%)',
      'z-index:9999',
      'display:flex',
      'flex-direction:column',
      'align-items:center',
      'gap:8px',
      'pointer-events:none'
    ].join(';');
    document.body.appendChild(this.container);
  },

  show: function(msg, type) {
    if (!this.container) this.init();

    var el = document.createElement('div');
    el.style.cssText = [
      'background:' + (type === 'success' ? '#15803d' : type === 'error' ? '#dc2626' : '#0e2148'),
      'color:#fff',
      'padding:11px 20px',
      'border-radius:8px',
      'font-size:13.5px',
      'font-weight:500',
      'box-shadow:0 4px 16px rgba(0,0,0,.2)',
      'animation:toastIn .25s ease forwards',
      'font-family:inherit'
    ].join(';');
    el.textContent = msg;

    var style = document.createElement('style');
    style.textContent = '@keyframes toastIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}' +
                        '@keyframes toastOut{from{opacity:1}to{opacity:0;transform:translateY(8px)}}';
    if (!document.getElementById('toastStyle')) {
      style.id = 'toastStyle';
      document.head.appendChild(style);
    }

    this.container.appendChild(el);

    setTimeout(function() {
      el.style.animation = 'toastOut .25s ease forwards';
      setTimeout(function() { el.remove(); }, 250);
    }, 2800);
  }
};

// ─── CANLI ARAMA ─────────────────────────────────────────
var Search = {
  timer: null,
  dropdown: null,
  input: null,

  init: function() {
    this.input    = document.getElementById('headerSearchInput');
    this.dropdown = document.getElementById('searchDropdown');
    if (!this.input || !this.dropdown) return;

    var self = this;

    this.input.addEventListener('input', function() {
      clearTimeout(self.timer);
      var q = this.value.trim();
      if (q.length < 2) { self.close(); return; }
      self.timer = setTimeout(function() { self.search(q); }, 300);
    });

    this.input.addEventListener('focus', function() {
      if (this.value.trim().length >= 2) self.search(this.value.trim());
    });

    document.addEventListener('click', function(e) {
      if (!e.target.closest('#headerSearchForm')) self.close();
    });
  },

  search: function(q) {
    var self = this;
    fetch('/arama?q=' + encodeURIComponent(q) + '&ajax=1', {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(d) { self.render(d.results || [], q); })
    .catch(function() {});
  },

  render: function(results, q) {
    if (!results.length) { this.close(); return; }

    var html = results.map(function(p) {
      return '<a href="/urun/' + p.slug + '" class="search-result-item">' +
        (p.cover ? '<img src="/uploads/products/' + p.cover + '" alt="">' : '<div style="width:40px;height:40px;background:#f3f4f6;border-radius:6px"></div>') +
        '<div>' +
          '<div class="search-result-name">' + (p.name || '') + '</div>' +
          '<div style="font-size:12px;color:#8892a8">' + (p.category_name || '') + '</div>' +
        '</div>' +
        '<span class="search-result-price">' + (p.price || '') + '</span>' +
      '</a>';
    }).join('');

    if (results.length >= 5) {
      html += '<a href="/arama?q=' + encodeURIComponent(q) + '" style="display:block;padding:10px 14px;font-size:13px;font-weight:600;color:#ff5c28;text-align:center;border-top:1px solid #e8eaf0">' +
              'Tüm sonuçları gör →</a>';
    }

    this.dropdown.innerHTML = html;
    this.dropdown.classList.add('open');
  },

  close: function() {
    if (this.dropdown) this.dropdown.classList.remove('open');
  }
};

// ─── HEADER SCROLL EFEKTİ ────────────────────────────────
function initHeaderScroll() {
  var header = document.getElementById('siteHeader');
  if (!header) return;

  var lastY = 0;
  window.addEventListener('scroll', function() {
    var y = window.scrollY;
    if (y > 80) header.classList.add('scrolled');
    else header.classList.remove('scrolled');
    lastY = y;
  }, { passive: true });
}

// ─── NAV HAMBURGER ────────────────────────────────────────
function initHamburger() {
  var btn  = document.getElementById('navHamburger');
  var list = document.getElementById('navList');
  if (!btn || !list) return;

  btn.addEventListener('click', function() {
    list.classList.toggle('open');
    btn.classList.toggle('open');
  });
}

// ─── USER MENU ────────────────────────────────────────────
function initUserMenu() {
  var btn      = document.getElementById('userMenuBtn');
  var dropdown = document.getElementById('userDropdown');
  if (!btn || !dropdown) return;

  btn.addEventListener('click', function(e) {
    e.stopPropagation();
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
  });

  document.addEventListener('click', function() {
    dropdown.style.display = 'none';
  });
}

// ─── BÜLTEN ───────────────────────────────────────────────
function initNewsletterForms() {
  ['footerNewsletterForm', 'heroNewsletterForm'].forEach(function(id) {
    var form = document.getElementById(id);
    if (!form) return;

    form.addEventListener('submit', function(e) {
      e.preventDefault();
      var email = form.querySelector('input[name="email"]').value;
      var csrf  = form.querySelector('input[name="_csrf_token"]');

      fetch('/bulten/abone', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: '_csrf_token=' + (csrf ? csrf.value : '') + '&email=' + encodeURIComponent(email)
      })
      .then(function(r) { return r.json(); })
      .then(function(d) {
        Toast.show(d.message || 'Abone oldunuz!', d.success ? 'success' : 'error');
        if (d.success) form.reset();
      })
      .catch(function() { Toast.show('Bir hata oluştu.', 'error'); });
    });
  });
}

// ─── ÇEREZ ONAYI ─────────────────────────────────────────
function acceptCookies() {
  var bar  = document.getElementById('cookieBar');
  var csrf = document.querySelector('input[name="_csrf_token"]');
  if (bar) bar.style.display = 'none';

  fetch('/cerez-onayi', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
    body: '_csrf_token=' + (csrf ? csrf.value : '')
  }).catch(function() {});
}

// ─── INIT ─────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
  Cart.init();
  Toast.init();
  Search.init();
  initHeaderScroll();
  initHamburger();
  initUserMenu();
  initNewsletterForms();
});
