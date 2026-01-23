import { Link } from '@inertiajs/react';

export default function AppLayout({ children }) {
    return (
        <div className="min-h-screen bg-gray-100">
            <nav className="bg-white border-b">
                <div className="max-w-7xl mx-auto px-4 h-14 flex items-center gap-4">
                    <Link href="/products">Products</Link>
                    <Link href="/cart">Cart</Link>
                </div>
            </nav>

            <main className="max-w-7xl mx-auto p-6">
                {children}
            </main>
        </div>
    );
}
