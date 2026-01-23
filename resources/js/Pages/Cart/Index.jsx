import { useEffect, useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import {
    fetchCart,
    updateCartItem,
    removeCartItem,
} from '@/api/cart';
import { checkout } from '@/api/checkout';

export default function Cart() {
    const [cart, setCart] = useState(null);
    const [loading, setLoading] = useState(true);
    const [processing, setProcessing] = useState(false);
    const [toast, setToast] = useState(null);

    const notify = (msg) => {
        setToast(msg);
        setTimeout(() => setToast(null), 2500);
    };

    const loadCart = async () => {
        setLoading(true);
        const res = await fetchCart();
        setCart(res.data.data);
        setLoading(false);
    };

    useEffect(() => {
        loadCart();
    }, []);

    const handleCheckout = async () => {
        setProcessing(true);
        await checkout();
        notify('Order placed successfully');
        await loadCart();
        setProcessing(false);
    };

    if (loading) {
        return (
            <AppLayout title="Cart">
                <p>Loading…</p>
            </AppLayout>
        );
    }

    if (!cart || cart.items.length === 0) {
        return (
            <AppLayout title="Cart">
                <p className="text-gray-500">Cart is empty.</p>
            </AppLayout>
        );
    }

    return (
        <AppLayout title="Cart">
            <h1 className="text-3xl font-bold mb-6">Your cart</h1>

            <div className="grid grid-cols-3 gap-6">
                {/* ITEMS */}
                <div className="col-span-2 space-y-4">
                    {cart.items.map(item => (
                        <div
                            key={item.id}
                            className="border rounded p-4 flex justify-between items-center bg-white"
                        >
                            <div>
                                <div className="font-semibold">
                                    {item.product.name}
                                </div>
                                <div className="text-sm text-gray-500">
                                    ${item.product.price}
                                </div>
                            </div>

                            <div className="flex items-center gap-3">
                                <button
                                    onClick={() =>
                                        updateCartItem(item.id, item.quantity - 1)
                                            .then(loadCart)
                                    }
                                    disabled={item.quantity <= 1}
                                    className="px-2 border rounded"
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
                                    className="px-2 border rounded"
                                >
                                    +
                                </button>
                            </div>

                            <button
                                onClick={() =>
                                    removeCartItem(item.id).then(loadCart)
                                }
                                className="text-red-600"
                            >
                                Remove
                            </button>
                        </div>
                    ))}
                </div>

                {/* SUMMARY */}
                <div className="border rounded p-4 bg-white h-fit">
                    <h2 className="font-semibold text-lg mb-4">
                        Summary
                    </h2>

                    <div className="flex justify-between mb-2">
                        <span>Total</span>
                        <span className="font-bold">
                            ${cart.total_price}
                        </span>
                    </div>

                    <button
                        onClick={handleCheckout}
                        disabled={processing}
                        className="w-full mt-4 bg-black text-white py-2 rounded disabled:opacity-40"
                    >
                        {processing ? 'Processing…' : 'Checkout'}
                    </button>
                </div>
            </div>

            {/* TOAST */}
            {toast && (
                <div className="fixed bottom-6 right-6 bg-green-600 text-white px-4 py-2 rounded shadow">
                    {toast}
                </div>
            )}
        </AppLayout>
    );
}
