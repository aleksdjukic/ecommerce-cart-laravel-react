import { useEffect, useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { fetchProducts } from '@/api/products';
import { addToCart } from '@/api/cart';

export default function Products() {
    const [products, setProducts] = useState([]);
    const [page, setPage] = useState(1);
    const [lastPage, setLastPage] = useState(1);
    const [toast, setToast] = useState(null);

    const loadProducts = async (page = 1) => {
        const res = await fetchProducts(page);
        setProducts(res.data.data);
        setLastPage(res.data.meta.last_page);
    };

    useEffect(() => {
        loadProducts(page);
    }, [page]);

    const notify = (msg, type = 'success') => {
        setToast({ msg, type });
        setTimeout(() => setToast(null), 2500);
    };

    const handleAddToCart = async (product) => {
        if (product.stock_quantity === 0) {
            notify('Product is out of stock', 'error');
            return;
        }

        await addToCart(product.id);
        notify('Added to cart');
    };

    return (
        <AppLayout title="Products">
            <h1 className="text-3xl font-bold mb-6">Products</h1>

            {/* GRID */}
            <div className="grid grid-cols-4 gap-6">
                {products.map(product => (
                    <div
                        key={product.id}
                        className="border rounded p-4 shadow bg-white flex flex-col"
                    >
                        <h2 className="font-semibold text-lg mb-2">
                            {product.name}
                        </h2>

                        <div className="text-gray-600 mb-1">
                            ${product.price}
                        </div>

                        <div
                            className={`text-sm mb-4 ${
                                product.stock_quantity === 0
                                    ? 'text-red-600'
                                    : 'text-gray-500'
                            }`}
                        >
                            Stock: {product.stock_quantity}
                        </div>

                        <button
                            disabled={product.stock_quantity === 0}
                            onClick={() => handleAddToCart(product)}
                            className="mt-auto bg-black text-white py-2 rounded disabled:opacity-40"
                        >
                            Add to cart
                        </button>
                    </div>
                ))}
            </div>

            {/* PAGINATION */}
            <div className="flex justify-center gap-2 mt-10">
                <button
                    disabled={page === 1}
                    onClick={() => setPage(p => p - 1)}
                    className="px-3 py-1 border rounded disabled:opacity-40"
                >
                    Prev
                </button>

                <span className="px-3 py-1">
                    Page {page} / {lastPage}
                </span>

                <button
                    disabled={page === lastPage}
                    onClick={() => setPage(p => p + 1)}
                    className="px-3 py-1 border rounded disabled:opacity-40"
                >
                    Next
                </button>
            </div>

            {/* TOAST */}
            {toast && (
                <div
                    className={`fixed bottom-6 right-6 px-4 py-2 rounded text-white shadow
                        ${toast.type === 'error' ? 'bg-red-600' : 'bg-green-600'}
                    `}
                >
                    {toast.msg}
                </div>
            )}
        </AppLayout>
    );
}
