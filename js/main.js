/* ===================================
   MELAYNA PARIS - Script Principal
   =================================== */

document.addEventListener("DOMContentLoaded", () => {

  // ---- Recherche ----
  const searchForm = document.querySelector(".search-bar");
  if (searchForm) {
    searchForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const q = searchForm.querySelector("input").value.trim();
      if (q) window.location.href = `boutique.html?q=${encodeURIComponent(q)}`;
    });
    const btn = searchForm.querySelector(".search-btn");
    if (btn) {
      btn.addEventListener("click", (e) => {
        e.preventDefault();
        const q = searchForm.querySelector("input").value.trim();
        if (q) window.location.href = `boutique.html?q=${encodeURIComponent(q)}`;
      });
    }
  }

  // ---- Marquer lien actif dans la nav ----
  const navLinks = document.querySelectorAll(".nav-item a");
  const currentPage = window.location.pathname.split("/").pop() || "index.html";
  navLinks.forEach(link => {
    const href = link.getAttribute("href");
    if (href === currentPage) link.classList.add("active");
  });

  // ---- Bouton retour haut de page ----
  const backToTop = document.getElementById("back-to-top");
  if (backToTop) {
    window.addEventListener("scroll", () => {
      backToTop.style.opacity = window.scrollY > 400 ? "1" : "0";
      backToTop.style.pointerEvents = window.scrollY > 400 ? "auto" : "none";
    });
    backToTop.addEventListener("click", () => window.scrollTo({ top: 0, behavior: "smooth" }));
  }

  // ---- Render produits (accueil) ----
  const featuredGrid = document.getElementById("featured-products");
  if (featuredGrid && typeof PRODUCTS !== "undefined") {
    renderProductCards(PRODUCTS.slice(0, 4), featuredGrid);
  }

  // ---- Render categories (accueil) ----
  const catGrid = document.getElementById("categories-grid");
  if (catGrid && typeof CATEGORIES !== "undefined") {
    CATEGORIES.filter(c => c.id !== "all").forEach(cat => {
      const card = document.createElement("div");
      card.className = "category-card";
      card.innerHTML = `
        <div class="category-icon"><i class="${cat.icon}"></i></div>
        <h4>${cat.label}</h4>
      `;
      card.addEventListener("click", () => {
        window.location.href = `boutique.html?cat=${cat.id}`;
      });
      catGrid.appendChild(card);
    });
  }
});

// ---- Render carte produit ----
function renderProductCards(products, container) {
  container.innerHTML = "";
  if (products.length === 0) {
    container.innerHTML = `
      <div class="empty-state" style="grid-column:1/-1">
        <i class="fas fa-search"></i>
        <h3>Aucun produit trouvé</h3>
        <p>Essayez d'autres filtres ou termes de recherche.</p>
        <a href="boutique.html" class="btn btn-primary">Voir tous les produits</a>
      </div>`;
    return;
  }
  products.forEach(product => {
    const card = document.createElement("div");
    card.className = "product-card";
    const isWished = typeof Wishlist !== "undefined" && Wishlist.has(product.id);
    card.innerHTML = `
      <div class="product-img">
        ${product.images && product.images.length > 0
          ? `<img src="${product.images[0]}" alt="${product.name}" style="width:100%;height:100%;object-fit:cover;" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
             <div class="product-img-placeholder" style="display:none;"><i class="fas fa-tshirt"></i><span>Photo à venir</span></div>`
          : `<div class="product-img-placeholder"><i class="fas fa-tshirt"></i><span>Photo à venir</span></div>`
        }
        ${product.badge ? `<span class="product-badge ${getBadgeClass(product.badge)}">${product.badge}</span>` : ""}
        <div class="product-actions-overlay">
          <button class="overlay-btn wish-btn ${isWished ? "liked" : ""}" data-id="${product.id}" title="Favoris">
            <i class="fa${isWished ? "s" : "r"} fa-heart"></i>
          </button>
          <button class="overlay-btn" onclick="window.location='produit.html?id=${product.id}'" title="Voir le produit">
            <i class="far fa-eye"></i>
          </button>
        </div>
      </div>
      <div class="product-info">
        <div class="product-category">${getCategoryLabel(product.category)}</div>
        <div class="product-name">${product.name}</div>
        <div class="product-price">
          <span class="price-current">${formatPrice(product.price)}</span>
          ${product.oldPrice ? `<span class="price-old">${formatPrice(product.oldPrice)}</span>` : ""}
        </div>
        <button class="product-add-btn" data-id="${product.id}">
          <i class="fas fa-shopping-bag"></i> Ajouter au panier
        </button>
      </div>
    `;
    // Wishlist
    card.querySelector(".wish-btn").addEventListener("click", (e) => {
      e.stopPropagation();
      const btn = e.currentTarget;
      const liked = Wishlist.toggle(product.id);
      btn.classList.toggle("liked", liked);
      btn.innerHTML = `<i class="fa${liked ? "s" : "r"} fa-heart"></i>`;
    });
    // Ajouter au panier
    card.querySelector(".product-add-btn").addEventListener("click", () => {
      const size = product.sizes[0];
      Cart.add(product.id, size);
    });
    // Clic pour voir produit
    card.querySelector(".product-img").addEventListener("click", () => {
      window.location.href = `produit.html?id=${product.id}`;
    });
    card.querySelector(".product-name").addEventListener("click", () => {
      window.location.href = `produit.html?id=${product.id}`;
    });
    container.appendChild(card);
  });
}
