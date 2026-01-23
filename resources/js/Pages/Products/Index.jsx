import { useEffect, useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import axios from 'axios';

export default function Products() {
    const [products, setProducts] = useState([]);

    useEffect(() => {
        axios.get('/api/products')
            .then(res => setProducts(res.data.data));
    }, []);

    const addToCart = (productId) => {
        axios.post('/api/cart/items', { product_id: productId })
            .then(() => alert('Added to cart'))
            .catch(err => alert(err.response.data.message));
    };

    return (
        <AppLayout>
            <h1 className="text-2xl font-bold mb-4">Products</h1>

            <div className="grid grid-cols-3 gap-4">
                {products.map(p => (
                    <div key={p.id} className="border p-4 rounded">
                        <h2 className="font-semibold">{p.name}</h2>
                        <p>${p.price}</p>
                        <p>Stock: {p.stock_quantity}</p>

                        <button
                            onClick={() => addToCart(p.id)}
                            className="mt-2 px-3 py-1 bg-blue-600 text-white rounded"
                        >
                            Add to cart
                        </button>
                    </div>
                ))}
            </div>
        </AppLayout>
    );
}
