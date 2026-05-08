/* ===================================
   MELAYNA PARIS - Gestion du Panier
   =================================== */

const Cart = {
  items: [],

  init() {
    const saved = localStorage.getItem("melayna_cart");
    if (saved) this.items = JSON.parse(saved);
    this.updateBadge();
  },

  save() {
    localStorage.setItem("melayna_cart", JSON.stringify(this.items));
    this.updateBadge();
  },

  add(productId, size, qty = 1) {
    const product = getProductById(productId);
    if (!product) return;
    const key = `${productId}-${size}`;
    const existing = this.items.find(i => i.key === key);
    if (existing) {
      existing.qty += qty;
    } else {
      this.items.push({ key, productId, size, qty, name: product.name, price: product.price });
    }
    this.save();
    showToast(`<i class="fas fa-check"></i> <span>${product.name} ajouté au panier !</span>`);
  },

  remove(key) {
    this.items = this.items.filter(i => i.key !== key);
    this.save();
  },

  updateQty(key, qty) {
    const item = this.items.find(i => i.key === key);
    if (item) {
      item.qty = Math.max(1, qty);
      this.save();
    }
  },

  getTotal() {
    return this.items.reduce((sum, i) => sum + (i.price * i.qty), 0);
  },

  getCount() {
    return this.items.reduce((sum, i) => sum + i.qty, 0);
  },

  clear() {
    this.items = [];
    this.save();
  },

  updateBadge() {
    const badge = document.getElementById("cart-badge");
    if (badge) {
      const count = this.getCount();
      badge.textContent = count;
      badge.style.display = count > 0 ? "flex" : "none";
    }
  }
};

// Wishlist
const Wishlist = {
  items: [],

  init() {
    const saved = localStorage.getItem("melayna_wishlist");
    if (saved) this.items = JSON.parse(saved);
  },

  toggle(productId) {
    const idx = this.items.indexOf(productId);
    if (idx >= 0) {
      this.items.splice(idx, 1);
      showToast(`<i class="fas fa-heart-broken"></i> <span>Retiré des favoris</span>`);
    } else {
      this.items.push(productId);
      showToast(`<i class="fas fa-heart" style="color:var(--primary)"></i> <span>Ajouté aux favoris !</span>`);
    }
    localStorage.setItem("melayna_wishlist", JSON.stringify(this.items));
    return this.items.includes(productId);
  },

  has(productId) {
    return this.items.includes(productId);
  }
};

// Notifications Toast
function showToast(html, duration = 3000) {
  let container = document.querySelector(".toast-container");
  if (!container) {
    container = document.createElement("div");
    container.className = "toast-container";
    document.body.appendChild(container);
  }
  const toast = document.createElement("div");
  toast.className = "toast";
  toast.innerHTML = html;
  container.appendChild(toast);
  setTimeout(() => {
    toast.style.animation = "slideIn 0.3s ease reverse";
    setTimeout(() => toast.remove(), 300);
  }, duration);
}

// Init au chargement
document.addEventListener("DOMContentLoaded", () => {
  Cart.init();
  Wishlist.init();
});
