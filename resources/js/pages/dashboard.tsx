import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import { breadcrumbs } from '@/helpers/breadcrumbs';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { InitialStats } from '@/types/dashboard/index.type';
import { Head } from '@inertiajs/react';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    Title,
    Tooltip,
    Legend,
} from 'chart.js';
import { Line, Bar } from 'react-chartjs-2';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, BarElement, Title, Tooltip, Legend);

export default function Dashboard({ initialStats }: { initialStats: InitialStats }) {

    return (
        <AppLayout breadcrumbs={breadcrumbs('Panel de control', dashboard().url)}>
            <Head title="Panel de control" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                    <div className="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                        <div className="text-sm text-muted-foreground">Campañas totales</div>
                        <div className="mt-2 text-3xl font-semibold">{initialStats ? initialStats.totals.campaigns_total : '—'}</div>
                        <div className="text-xs text-muted-foreground">El total de campañas en el sistema</div>
                    </div>
                    <div className="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                        <div className="text-sm text-muted-foreground">Medios</div>
                        <div className="mt-2 text-3xl font-semibold">{initialStats ? initialStats.totals.media_total : '—'}</div>
                        <div className="text-xs text-muted-foreground">Archivos subidos en el CMS</div>
                    </div>
                    <div className="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                        <div className="text-sm text-muted-foreground">Usuarios</div>
                        <div className="mt-2 text-3xl font-semibold">{initialStats ? initialStats.totals.users_total : '—'}</div>
                        <div className="text-xs text-muted-foreground">Cuentas de usuario registradas</div>
                    </div>
                </div>

                <div className="grid gap-4 md:grid-cols-3">
                    <div className="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                        <div className="text-sm text-muted-foreground">Campañas activas</div>
                        <div className="mt-2 text-2xl font-semibold">{initialStats ? initialStats.totals.campaigns_active ?? '—' : '—'}</div>
                        <div className="text-xs text-muted-foreground">En curso ahora mismo</div>
                    </div>
                    <div className="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                        <div className="text-sm text-muted-foreground">Campañas pendientes</div>
                        <div className="mt-2 text-2xl font-semibold">{initialStats ? initialStats.totals.campaigns_pending ?? '—' : '—'}</div>
                        <div className="text-xs text-muted-foreground">Por iniciar</div>
                    </div>
                    <div className="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                        <div className="text-sm text-muted-foreground">Duración media (días)</div>
                        <div className="mt-2 text-2xl font-semibold">{initialStats ? (Math.round(Number(initialStats.totals.avg_campaign_days)) ?? '—') : '—'}</div>
                        <div className="text-xs text-muted-foreground">Promedio de duración de campañas finalizadas</div>
                    </div>
                </div>

                <div className="grid gap-4 md:grid-cols-2">
                    <div className="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                        <h3 className="mb-2 text-sm font-medium">Tendencia de campañas (últimos 6 meses)</h3>
                        {initialStats ? (
                            <Line
                                options={{ responsive: true, plugins: { legend: { display: false } } }}
                                data={{ labels: initialStats.labels, datasets: [{ label: 'Campañas', data: initialStats.campaigns, borderColor: '#4f46e5', backgroundColor: 'rgba(79,70,229,0.1)' }] }}
                            />
                        ) : (
                            <div className="min-h-50 flex items-center justify-center text-muted-foreground">Cargando...</div>
                        )}
                    </div>

                    <div className="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                        <h3 className="mb-2 text-sm font-medium">Subidas de medios (últimos 6 meses)</h3>
                        {initialStats ? (
                            <Bar
                                options={{ responsive: true, plugins: { legend: { display: false } } }}
                                data={{ labels: initialStats.labels, datasets: [{ label: 'Medios', data: initialStats.media, backgroundColor: '#06b6d4' }] }}
                            />
                        ) : (
                            <div className="min-h-50 flex items-center justify-center text-muted-foreground">Cargando...</div>
                        )}
                    </div>
                </div>

                <div className="grid gap-4 md:grid-cols-2">
                    <div className="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                        <h3 className="mb-2 text-sm font-medium">Top tipos de medios</h3>
                        {initialStats && initialStats.top_media && initialStats.top_media.length ? (
                            <ul className="space-y-2">
                                {initialStats.top_media.map((m) => (
                                    <li key={m.mime_type} className="flex justify-between">
                                        <span className="text-sm">{m.mime_type}</span>
                                        <span className="text-sm font-semibold">{m.count}</span>
                                    </li>
                                ))}
                            </ul>
                        ) : (
                            <div className="text-xs text-muted-foreground">Sin datos</div>
                        )}
                    </div>

                    <div className="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                        <h3 className="mb-2 text-sm font-medium">Campañas recientes</h3>
                        {initialStats && initialStats.recent_campaigns && initialStats.recent_campaigns.length ? (
                            <table className="w-full text-sm">
                                <thead>
                                    <tr className="text-left text-xs text-muted-foreground">
                                        <th>Título</th>
                                        <th>Creada</th>
                                        <th>Usuario</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {initialStats.recent_campaigns.map((c) => (
                                        <tr key={c.id} className="border-t">
                                            <td className="py-2">{c.title}</td>
                                            <td className="py-2 text-xs text-muted-foreground">{c.created_at}</td>
                                            <td className="py-2 text-xs text-muted-foreground">{c.user ?? '—'}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        ) : (
                            <div className="text-xs text-muted-foreground">Sin campañas recientes</div>
                        )}
                    </div>
                </div>

                <div className="relative min-h-screen flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border p-4">
                    <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                </div>
            </div>
        </AppLayout>
    );
}
