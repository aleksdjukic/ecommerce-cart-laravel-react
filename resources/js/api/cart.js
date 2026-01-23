import api from '@/lib/api';

export const fetchCart = () => {
    return api.get('/cart');
};

export const addToCart = (productId) => {
    return api.post('/cart/items', {
        product_id: productId,
    });
};

export const updateCartItem = (itemId, quantity) => {
    return api.patch(`/cart/items/${itemId}`, {
        quantity,
    });
};

export const removeCartItem = (itemId) => {
    return api.delete(`/cart/items/${itemId}`);
};
