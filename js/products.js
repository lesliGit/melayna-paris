/* ===================================
   MELAYNA PARIS - Catalogue Produits
   =================================== */

const PRODUCTS = [
  {
    id: 1,
    name: "SABAH",
    category: "robes",
    price: 159.63,
    oldPrice: null,
    badge: null,
    sizes: ["unique"],
    description: "Robe SABAH - Design Élégant et Polyvalente pour Occasions Spéciales - Confortable et Stylée pour une Silhouette Gracieuse",
    images: ["images/sabah-1.jpg", "images/sabah-2.jpg"],
    stock: 199999,
    new: false
  },
  {
    id: 2,
    name: "SAJDA",
    category: "robes",
    price: 19.50,
    oldPrice: null,
    badge: null,
    sizes: ["unique"],
    description: "SAJDA Robe Déesse Grecque Élégante et Confortable Idéale pour Printemps Été Occasions Spéciales Polyvalente et Raffinée",
    images: ["images/sajda-1.jpg"],
    stock: 15,
    new: false
  }
];

const CATEGORIES = [
  { id: "all",      label: "Tout voir",          icon: "fas fa-th" },
  { id: "robes",    label: "Robes",               icon: "fas fa-female" },
  { id: "jupes",    label: "Jupes",               icon: "fas fa-star" },
  { id: "vestes",   label: "Vestes & Manteaux",   icon: "fas fa-tshirt" },
  { id: "pantalons",label: "Pantalons",            icon: "fas fa-stream" },
  { id: "sacs",     label: "Sacs",                icon: "fas fa-shopping-bag" }
];

// Fonctions utilitaires
function getProductById(id) {
  return PRODUCTS.find(p => p.id === parseInt(id));
}

function getProductsByCategory(category) {
  if (category === "all") return PRODUCTS;
  return PRODUCTS.filter(p => p.category === category);
}

function formatPrice(price) {
  return price.toFixed(2).replace(".", ",") + " €";
}

function getBadgeClass(badge) {
  if (badge === "Nouveau") return "badge-new";
  if (badge === "Promo")   return "badge-promo";
  return "";
}

function getCategoryLabel(catId) {
  const cat = CATEGORIES.find(c => c.id === catId);
  return cat ? cat.label : catId;
}
