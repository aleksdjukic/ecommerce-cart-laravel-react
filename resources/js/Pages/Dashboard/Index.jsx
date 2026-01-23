import AppLayout from '@/Layouts/AppLayout';
import SalesChart from '@/Components/SalesChart';

export default function Dashboard({ stats, charts }) {
    return (
        <AppLayout title="Dashboard">
            <h1 className="text-3xl font-bold mb-8">Dashboard</h1>

            {/* METRICS */}
            <div className="grid grid-cols-4 gap-4 mb-10">
                <Stat
                    title="Revenue Today"
                    value={`$${Number(stats.revenue.today).toFixed(2)}`}
                />
                <Stat
                    title="Revenue This Month"
                    value={`$${Number(stats.revenue.month).toFixed(2)}`}
                />
                <Stat
                    title="Total Orders"
                    value={stats.orders.total}
                />
                <Stat
                    title="Avg Order Value"
                    value={`$${Number(stats.average_order_value).toFixed(2)}`}
                />
            </div>

            {/* CHART */}
            <div className="bg-white p-6 rounded shadow mb-10">
                <h2 className="text-xl font-semibold mb-4">
                    Sales – last 7 days
                </h2>

                <SalesChart chart={charts.sales7days} />
            </div>

            <div className="grid grid-cols-2 gap-8">
                {/* LOW STOCK */}
                <Section title="Low stock products">
                    {stats.low_stock_products.length === 0 && (
                        <Empty text="All products are sufficiently stocked." />
                    )}

                    {stats.low_stock_products.map(p => (
                        <div
                            key={p.id}
                            className="flex justify-between items-center border-b pb-2"
                        >
                            <span className="font-medium">{p.name}</span>
                            <span className="text-red-600 font-semibold">
                                {p.stock_quantity}
                            </span>
                        </div>
                    ))}
                </Section>

                {/* LATEST ORDERS */}
                <Section title="Latest orders">
                    {stats.latest_orders.map(order => (
                        <div
                            key={order.id}
                            className="border rounded p-3"
                        >
                            <div className="font-semibold">
                                Order #{order.id} — ${order.total_price}
                            </div>

                            <div className="text-sm text-gray-500">
                                {order.items.length} item(s)
                            </div>
                        </div>
                    ))}
                </Section>
            </div>
        </AppLayout>
    );
}

/* ---------------- UI PARTS ---------------- */

function Stat({ title, value }) {
    return (
        <div className="border p-4 rounded bg-white shadow">
            <div className="text-gray-500 text-sm mb-1">{title}</div>
            <div className="text-2xl font-bold">{value}</div>
        </div>
    );
}

function Section({ title, children }) {
    return (
        <div>
            <h2 className="text-xl font-semibold mb-4">{title}</h2>
            <div className="space-y-3">{children}</div>
        </div>
    );
}

function Empty({ text }) {
    return (
        <div className="text-gray-500 italic">
            {text}
        </div>
    );
}
