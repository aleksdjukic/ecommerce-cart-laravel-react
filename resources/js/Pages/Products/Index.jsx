import { useEffect, useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { fetchProducts } from '@/api/products';
import { addToCart } from '@/api/cart';

export default function Products() {
    const [products, setProducts] = useState([]);

    useEffect(() => {
        fetchProducts().then(response => {
            setProducts(response.data.data);
        });
    }, []);

    const handleAddToCart = (product) => {
        addToCart(product.id);
    };

    return (
        <AppLayout title="Products">
            <div className="space-y-4">
                {products.map(product => (
                    <div
                        key={product.id}
                        className="flex justify-between border p-4"
                    >
                        <div>
                            <h2 className="font-bold">{product.name}</h2>
                            <p>${product.price}</p>
                            <p>Stock: {product.stock_quantity}</p>
                        </div>

                        <button
                            disabled={product.stock_quantity === 0}
                            onClick={() => handleAddToCart(product)}
                            className="bg-blue-500 text-white px-4 py-2 disabled:opacity-50"
                        >
                            Add to cart
                        </button>
                    </div>
                ))}
            </div>
        </AppLayout>
    );
}
