import { useEffect, useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import {
    fetchCart,
    updateCartItem,
    removeCartItem,
} from '@/api/cart';

export default function Cart() {
    const [cart, setCart] = useState(null);
    const [loading, setLoading] = useState(true);

    const loadCart = async () => {
        setLoading(true);
        const res = await fetchCart();
        setCart(res.data.data);
        setLoading(false);
    };

    useEffect(() => {
        loadCart();
    }, []);

    if (loading) return <div>Loading…</div>;
    if (!cart || cart.items.length === 0) {
        return <AppLayout><p>Cart is empty.</p></AppLayout>;
    }

    return (
        <AppLayout title="Cart">
            <h1 className="text-2xl font-bold mb-4">Cart</h1>

            {cart.items.map(item => (
                <div
                    key={item.id}
                    className="flex items-center gap-4 border p-3 mb-2"
                >
                    <div className="flex-1">
                        <div className="font-semibold">
                            {item.product.name}
                        </div>
                        <div className="text-sm text-gray-500">
                            Stock: {item.product.stock_quantity}
                        </div>
                    </div>

                    <div className="flex items-center gap-2">
                        <button
                            onClick={() =>
                                updateCartItem(item.id, item.quantity - 1)
                                    .then(loadCart)
                            }
                            disabled={item.quantity <= 1}
                        >
                            -
                        </button>

                        <span>{item.quantity}</span>

                        <button
                            onClick={() =>
                                updateCartItem(item.id, item.quantity + 1)
                                    .then(loadCart)
                            }
                            disabled={
                                item.quantity >= item.product.stock_quantity
                            }
                        >
                            +
                        </button>
                    </div>

                    <button
                        className="text-red-600"
                        onClick={() =>
                            removeCartItem(item.id).then(loadCart)
                        }
                    >
                        Remove
                    </button>
                </div>
            ))}
        </AppLayout>
    );
}
