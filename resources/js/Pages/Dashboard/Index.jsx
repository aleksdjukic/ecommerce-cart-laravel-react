import AppLayout from '@/Layouts/AppLayout';

export default function Dashboard({ stats }) {
    return (
        <AppLayout title="Dashboard">
            <h1 className="text-3xl font-bold mb-6">Dashboard</h1>

            {/* METRICS */}
            <div className="grid grid-cols-4 gap-4 mb-8">
                <Stat title="Revenue Today" value={`$${stats.revenue.today}`} />
                <Stat title="Revenue Month" value={`$${stats.revenue.month}`} />
                <Stat title="Total Orders" value={stats.orders.total} />
                <Stat title="Avg Order" value={`$${Number(stats.average_order_value).toFixed(2)}`} />
            </div>

            {/* LOW STOCK */}
            <Section title="Low stock products">
                {stats.low_stock_products.map(p => (
                    <div key={p.id} className="flex justify-between">
                        <span>{p.name}</span>
                        <span className="text-red-600">{p.stock_quantity}</span>
                    </div>
                ))}
            </Section>

            {/* LATEST ORDERS */}
            <Section title="Latest orders">
                {stats.latest_orders.map(order => (
                    <div key={order.id} className="border p-2 mb-2">
                        <div className="font-semibold">
                            Order #{order.id} — ${order.total_price}
                        </div>
                        <div className="text-sm text-gray-500">
                            {order.items.length} items
                        </div>
                    </div>
                ))}
            </Section>
        </AppLayout>
    );
}

function Stat({ title, value }) {
    return (
        <div className="border p-4 rounded bg-white shadow">
            <div className="text-gray-500 text-sm">{title}</div>
            <div className="text-2xl font-bold">{value}</div>
        </div>
    );
}

function Section({ title, children }) {
    return (
        <div className="mb-6">
            <h2 className="text-xl font-semibold mb-2">{title}</h2>
            <div className="space-y-2">{children}</div>
        </div>
    );
}
