import axios from 'axios';

const api = axios.create({
    baseURL: '/api',
    withCredentials: true,
    headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
});

api.interceptors.response.use(
    response => response,
    error => {
        const { response } = error;

        if (!response) {
            alert('Network error. Please try again.');
            return Promise.reject(error);
        }

        if (response.status === 422 && response.data?.message) {
            alert(response.data.message);
        }

        if (response.status === 403) {
            alert(response.data?.message ?? 'Forbidden');
        }

        return Promise.reject(error);
    }
);

export default api;
