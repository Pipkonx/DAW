// Frontend principal: validaciones y comunicación con controladores PHP vía fetch

const API = {
  usuario: '/app/controllers/UsuarioController.php',
  finanzas: '/app/controllers/FinanzasController.php',
};

function postForm(url, data) {
  const form = new FormData();
  Object.entries(data).forEach(([k, v]) => form.append(k, v));
  return fetch(url, {
    method: 'POST',
    body: form,
  }).then(r => r.json());
}

function getCsrf(formEl) {
  const input = formEl.querySelector('input[name="csrf_token"]');
  return input ? input.value : '';
}

// Registro
export async function handleRegister(event) {
  event.preventDefault();
  const form = event.target;
  const nombre = form.nombre.value.trim();
  const email = form.email.value.trim();
  const password = form.password.value.trim();
  const confirmar = form.confirmar.value.trim();
  const csrf = getCsrf(form);

  if (!nombre || !email || !password || !confirmar) {
    alert('Por favor, completa todos los campos.');
    return;
  }
  if (password !== confirmar) {
    alert('Las contraseñas no coinciden.');
    return;
  }

  const res = await postForm(API.usuario, { action: 'register', nombre, email, password, csrf_token: csrf });
  if (res.success) {
    window.location.href = 'dashboard.php';
  } else {
    alert(res.error || 'Error en el registro');
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
  const res = await postForm(API.usuario, { action: 'login', email, password, csrf_token: csrf });
  if (res.success) {
    window.location.href = 'dashboard.php';
  } else {
    alert(res.error || 'Credenciales inválidas');
  }
}

// Dashboard
async function loadMovements(csrf) {
  const res = await postForm(API.finanzas, { action: 'list', csrf_token: csrf });
  if (!res.success) return;
  const tbody = document.getElementById('movementsBody');
  if (!tbody) return;
  tbody.innerHTML = '';
  res.items.forEach(item => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${new Date(item.fecha_registro).toLocaleDateString()}</td>
      <td>${item.tipo}</td>
      <td>${item.monto.toFixed(2)}</td>
      <td>${item.descripcion || ''}</td>
    `;
    tbody.appendChild(tr);
  });
}

async function loadCharts(csrf) {
  const year = new Date().getFullYear();
  const [sumRes, monRes] = await Promise.all([
    postForm(API.finanzas, { action: 'summary', anio: year, csrf_token: csrf }),
    postForm(API.finanzas, { action: 'monthly', anio: year, csrf_token: csrf }),
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
  const res = await postForm(API.finanzas, { action: 'add', tipo, monto, descripcion, fecha, csrf_token: csrf });
  if (res.success) {
    await loadMovements(csrf);
    await loadCharts(csrf);
    form.reset();
    form.fecha.value = new Date().toISOString().slice(0, 10);
  } else {
    alert(res.error || 'Error al guardar');
  }
}

async function handleLogout(event) {
  event.preventDefault();
  const csrf = document.querySelector('input[name="csrf_token"]')?.value;
  const res = await postForm(API.usuario, { action: 'logout', csrf_token: csrf || '' });
  if (res.success) {
    window.location.href = 'login.php';
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
    const csrf = getCsrf(movForm);
    loadMovements(csrf);
    loadCharts(csrf);
  }

  const logoutLink = document.getElementById('logoutLink');
  if (logoutLink) logoutLink.addEventListener('click', handleLogout);
});