// /js/offline-db.js
// DB local para usuarios y acciones pendientes (registro/login) y sesión actual

const GEO_DB_NAME = "geobiblia_offline_db";
const GEO_DB_VERSION = 1;

function openGeoDB() {
  return new Promise((resolve, reject) => {
    const req = indexedDB.open(GEO_DB_NAME, GEO_DB_VERSION);
    req.onupgradeneeded = (e) => {
      const db = e.target.result;
      if (!db.objectStoreNames.contains("usuarios")) {
        // key: email
        db.createObjectStore("usuarios", { keyPath: "email" });
      }
      if (!db.objectStoreNames.contains("pendientes")) {
        // autoIncrement para cola
        db.createObjectStore("pendientes", { keyPath: "id", autoIncrement: true });
      }
      if (!db.objectStoreNames.contains("sesion")) {
        // Una sola clave "actual"
        db.createObjectStore("sesion", { keyPath: "key" });
      }
    };
    req.onsuccess = () => resolve(req.result);
    req.onerror = () => reject(req.error);
  });
}

// Utilidad: hash SHA-256 (hex) para no guardar contraseñas en texto plano
async function sha256Hex(text) {
  const enc = new TextEncoder().encode(text);
  const buf = await crypto.subtle.digest("SHA-256", enc);
  const arr = Array.from(new Uint8Array(buf));
  return arr.map(b => b.toString(16).padStart(2, "0")).join("");
}

// ---- Usuarios (copia local mínima) ----
async function dbGuardarUsuarioLocal({ username, email, passwordHash, rol = "usuario", origen = "offline" }) {
  const db = await openGeoDB();
  const tx = db.transaction("usuarios", "readwrite");
  const store = tx.objectStore("usuarios");
  await store.put({ email, username, passwordHash, rol, origen, updatedAt: Date.now() });
  return tx.complete;
}

async function dbObtenerUsuarioLocal(email) {
  const db = await openGeoDB();
  return new Promise((resolve) => {
    const tx = db.transaction("usuarios", "readonly");
    const store = tx.objectStore("usuarios");
    const req = store.get(email);
    req.onsuccess = () => resolve(req.result || null);
    req.onerror = () => resolve(null);
  });
}

// ---- Cola de acciones pendientes (para sincronizar cuando vuelva internet) ----
async function dbAgregarPendiente(accion) {
  const db = await openGeoDB();
  const tx = db.transaction("pendientes", "readwrite");
  const store = tx.objectStore("pendientes");
  await store.add({ ...accion, createdAt: Date.now() });
  return tx.complete;
}

async function dbListarPendientes() {
  const db = await openGeoDB();
  return new Promise((resolve) => {
    const tx = db.transaction("pendientes", "readonly");
    const store = tx.objectStore("pendientes");
    const req = store.getAll();
    req.onsuccess = () => resolve(req.result || []);
    req.onerror = () => resolve([]);
  });
}

async function dbLimpiarPendientes() {
  const db = await openGeoDB();
  const tx = db.transaction("pendientes", "readwrite");
  await tx.objectStore("pendientes").clear();
  return tx.complete;
}

// ---- Sesión local (para permitir “seguir dentro” offline si ya inició antes) ----
async function dbSetSesionLocal({ email, username, rol }) {
  const db = await openGeoDB();
  const tx = db.transaction("sesion", "readwrite");
  await tx.objectStore("sesion").put({ key: "actual", email, username, rol, ts: Date.now() });
  return tx.complete;
}

async function dbGetSesionLocal() {
  const db = await openGeoDB();
  return new Promise((resolve) => {
    const tx = db.transaction("sesion", "readonly");
    const req = tx.objectStore("sesion").get("actual");
    req.onsuccess = () => resolve(req.result || null);
    req.onerror = () => resolve(null);
  });
}

async function dbClearSesionLocal() {
  const db = await openGeoDB();
  const tx = db.transaction("sesion", "readwrite");
  await tx.objectStore("sesion").delete("actual");
  return tx.complete;
}

// ---- Sincronización (desde la página, más compatible que Background Sync en iOS) ----
async function sincronizarPendientesConServidor(baseUrl = "") {
  const pendientes = await dbListarPendientes();
  if (!navigator.onLine || !pendientes.length) return;

  for (const item of pendientes) {
    try {
      if (item.tipo === "registro") {
        // Enviamos datos al endpoint normal del servidor
        const form = new URLSearchParams();
        form.set("username", item.username);
        form.set("email", item.email);
        // En el servidor tú haces password_hash; aquí mandamos la contraseña original si la guardaste
        // Para minimizar exposición, guardamos passwordHash en local, pero al servidor hay que mandar 'password' real.
        // Por eso en la cola guardaremos passwordPlano SOLO si se registró offline (ver auth-offline.js)
        form.set("password", item.passwordPlano);
        form.set("confirmPassword", item.passwordPlano);

        await fetch(`${baseUrl}/Registrarse.php`, { method: "POST", body: form });
      }

      if (item.tipo === "login") {
        const form = new URLSearchParams();
        form.set("email", item.email);
        form.set("password", item.passwordPlano);
        await fetch(`${baseUrl}/Iniciar_Sesion.php`, { method: "POST", body: form });
      }
    } catch (e) {
      // si falla, dejamos en la cola
      console.warn("Fallo sincronizando acción:", item, e);
      return;
    }
  }
  await dbLimpiarPendientes();
}
