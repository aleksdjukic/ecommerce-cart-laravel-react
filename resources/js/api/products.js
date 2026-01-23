import api from '@/lib/api';

export const fetchProducts = (page = 1) => {
    return api.get('/products', {
        params: { page },
    });
};
