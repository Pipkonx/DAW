const API_URL = 'http://localhost:8000/api.php';

export const fetchClients = async () => {
    const response = await fetch(`${API_URL}?action=getClients`);
    return response.json();
};

export const fetchPolicies = async () => {
    const response = await fetch(`${API_URL}?action=getPolicies`);
    return response.json();
};

export const fetchPayments = async () => {
    const response = await fetch(`${API_URL}?action=getPayments`);
    return response.json();
};

export const addPayment = async (payload) => {
    const response = await fetch(`${API_URL}?action=addPayment`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    });
    return response.json();
};

export const deletePayment = async (id) => {
    const response = await fetch(`${API_URL}?action=deletePayment&id=${id}`);
    return response.json();
};

export const loginUser = async (credentials) => {
    const response = await fetch(`${API_URL}?action=login`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(credentials)
    });
    return response.json();
};
