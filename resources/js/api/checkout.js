import api from '@/lib/api';

export const checkout = () => {
    return api.post('/checkout');
};
