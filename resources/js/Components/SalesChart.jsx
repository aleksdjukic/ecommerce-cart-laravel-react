import { Line } from 'react-chartjs-2';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Tooltip,
} from 'chart.js';

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Tooltip
);

export default function SalesChart({ chart }) {
  return (
    <Line
      data={{
        labels: chart.labels,
        datasets: [{
          label: 'Sales (€)',
          data: chart.data,
          borderColor: '#2563eb',
          tension: 0.4,
        }],
      }}
    />
  );
}
