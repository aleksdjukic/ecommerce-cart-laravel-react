import { useEffect, useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import axios from 'axios';

export default function Cart() {
    const [cart, setCart] = useState(null);

    const loadCart = () => {
        axios.get('/api/cart')
            .then(res => setCart(res.data.data));
    };

    useEffect(loadCart, []);

    const updateQty = (itemId, qty) => {
        axios.patch(`/api/cart/items/${itemId}`, { quantity: qty })
            .then(loadCart)
            .catch(err => alert(err.response.data.message));
    };

    const removeItem = (itemId) => {
        axios.delete(`/api/cart/items/${itemId}`)
            .then(loadCart);
    };

    if (!cart) return null;

    return (
        <AppLayout>
            <h1 className="text-2xl font-bold mb-4">Cart</h1>

            {cart.items.map(item => (
                <div key={item.id} className="flex gap-4 mb-2">
                    <div>{item.product.name}</div>
                    <div>Qty: {item.quantity}</div>

                    <button onClick={() => updateQty(item.id, item.quantity + 1)}>+</button>
                    <button onClick={() => updateQty(item.id, item.quantity - 1)}>-</button>
                    <button onClick={() => removeItem(item.id)}>Remove</button>
                </div>
            ))}
        </AppLayout>
    );
}
