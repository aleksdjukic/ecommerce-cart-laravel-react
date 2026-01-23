import axios from 'axios';

const api = axios.create({
    baseURL: '/api',
    headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
    withCredentials: true,
});

export default api;

api.interceptors.response.use(
    response => response,
    error => {
        const { response } = error;

        if (!response) {
            alert('Network error. Please try again.');
            return Promise.reject(error);
        }

        const status = response.status;
        const data = response.data;

        if (status === 403) {
            alert(data.message ?? 'Forbidden');
        }

        if (status === 422 && data.message) {
            // business rule error (stock)
            alert(data.message);
        }

        return Promise.reject(error);
    }
);
