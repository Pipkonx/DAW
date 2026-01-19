// Frontend principal: validaciones y comunicación con Laravel
const APP_URL = document.querySelector('meta[name="app-url"]')?.content || '';

const API = {
  register: `${APP_URL}/api/register`,
  login: `${APP_URL}/api/login`,
  logout: `${APP_URL}/api/logout`,
  finanzasAdd: `${APP_URL}/api/finanzas/add`,
  finanzasList: `${APP_URL}/api/finanzas/list`,
  finanzasSummary: `${APP_URL}/api/finanzas/summary`,
  finanzasMonthly: `${APP_URL}/api/finanzas/monthly`,
};

async function handleResponse(response) {
  const contentType = response.headers.get('content-type');
  if (contentType && contentType.includes('application/json')) {
    return response.json();
  }
  
  // Si no es JSON, probablemente sea un error 404 o 500 en HTML
  if (!response.ok) {
    throw new Error(`Error ${response.status}: El servidor no respondió con JSON. Verifica que la URL sea correcta.`);
  }
  return response.text();
}

async function postForm(url, data) {
  const form = new FormData();
  Object.entries(data).forEach(([k, v]) => {
    const key = k === 'csrf_token' ? '_token' : k;
    form.append(key, v);
  });

  try {
    const response = await fetch(url, {
      method: 'POST',
      body: form,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
    return await handleResponse(response);
  } catch (error) {
    console.error('Fetch error:', error);
    return { success: false, error: error.message };
  }
}

async function getJson(url) {
  try {
    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
    return await handleResponse(response);
  } catch (error) {
    console.error('Fetch error:', error);
    return { success: false, error: error.message };
  }
}

function getCsrf(formEl) {
  const input = formEl.querySelector('input[name="_token"]') || formEl.querySelector('input[name="csrf_token"]');
  return input ? input.value : '';
}

// Registro
export async function handleRegister(event) {
  event.preventDefault();
  const form = event.target;
  const nombre = form.nombre.value.trim();
  const email = form.email.value.trim();
  const password = form.password.value.trim();
  const password_confirmation = form.confirmar.value.trim(); // Laravel espera password_confirmation
  const csrf = getCsrf(form);

  if (!nombre || !email || !password || !password_confirmation) {
    alert('Por favor, completa todos los campos.');
    return;
  }
  if (password !== password_confirmation) {
    alert('Las contraseñas no coinciden.');
    return;
  }

  const res = await postForm(API.register, { 
    nombre, 
    email, 
    password, 
    password_confirmation, 
    csrf_token: csrf 
  });

  if (res.success) {
    window.location.href = `${APP_URL}/dashboard`;
  } else {
    alert(res.error || res.message || 'Error en el registro');
  }
}

// Login
export async function handleLogin(event) {
  event.preventDefault();
  const form = event.target;
  const email = form.email.value.trim();
  const password = form.password.value.trim();
  const csrf = getCsrf(form);

  if (!email || !password) {
    alert('Por favor, completa todos los campos.');
    return;
  }

  const res = await postForm(API.login, { email, password, csrf_token: csrf });
  if (res.success) {
    window.location.href = `${APP_URL}/dashboard`;
  } else {
    alert(res.error || res.message || 'Credenciales inválidas');
  }
}

// Dashboard
async function loadMovements() {
  const res = await getJson(API.finanzasList);
  if (!res.success) return;
  const tbody = document.getElementById('movementsBody');
  if (!tbody) return;
  tbody.innerHTML = '';
  res.items.forEach(item => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${new Date(item.fecha_registro).toLocaleDateString()}</td>
      <td>${item.tipo}</td>
      <td>${parseFloat(item.monto).toFixed(2)}</td>
      <td>${item.descripcion || ''}</td>
    `;
    tbody.appendChild(tr);
  });
}

async function loadCharts() {
  const year = new Date().getFullYear();
  // En Laravel las rutas GET pueden recibir parámetros por query string
  const [sumRes, monRes] = await Promise.all([
    getJson(`${API.finanzasSummary}?anio=${year}`),
    getJson(`${API.finanzasMonthly}?anio=${year}`),
  ]);

  if (sumRes.success && monRes.success) {
    const { renderCharts } = await import('./graficos.js');
    renderCharts(monRes.monthly, sumRes.summary);
  }
}

async function handleAddMovement(event) {
  event.preventDefault();
  const form = event.target;
  const csrf = getCsrf(form);
  const tipo = form.tipo.value;
  const monto = parseFloat(form.monto.value);
  const descripcion = form.descripcion.value.trim();
  const fecha = form.fecha.value;

  if (!tipo || !monto || monto <= 0 || !fecha) {
    alert('Datos inválidos');
    return;
  }

  const res = await postForm(API.finanzasAdd, { tipo, monto, descripcion, fecha, csrf_token: csrf });
  if (res.success) {
    await loadMovements();
    await loadCharts();
    form.reset();
    form.fecha.value = new Date().toISOString().slice(0, 10);
  } else {
    alert(res.error || res.message || 'Error al guardar');
  }
}

async function handleLogout(event) {
  event.preventDefault();
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || getCsrf(document.body);
  const res = await postForm(API.logout, { csrf_token: csrf });
  if (res.success) {
    window.location.href = `${APP_URL}/`;
  }
}

document.addEventListener('DOMContentLoaded', () => {
  const loginForm = document.getElementById('loginForm');
  if (loginForm) loginForm.addEventListener('submit', handleLogin);

  const registerForm = document.getElementById('registerForm');
  if (registerForm) registerForm.addEventListener('submit', handleRegister);

  const movForm = document.getElementById('movForm');
  if (movForm) {
    movForm.addEventListener('submit', handleAddMovement);
    loadMovements();
    loadCharts();
  }

  const logoutLink = document.getElementById('logoutLink');
  if (logoutLink) logoutLink.addEventListener('click', handleLogout);
});
