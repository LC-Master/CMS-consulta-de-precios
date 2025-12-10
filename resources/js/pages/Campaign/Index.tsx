import { useState, useEffect } from 'react'
import { InfiniteScroll, router, usePage } from '@inertiajs/react'
import { Search } from 'lucide-react'
import AppLayout from '@/layouts/app-layout';
import Select from 'react-select';

interface Status {
    id: string;
    status: string;
}

interface Campaign {
    id: number;
    title: string;
    status: Status;
    created_at: string;
    [key: string]: unknown;
}

interface Props {
    campaigns: { data: Campaign[] };
    filters: { search?: string; status?: string };
    statuses: Status[];
}

export default function CampaignsIndex({ campaigns, filters = {}, statuses = [] }: Props) {
    const [search, setSearch] = useState(filters.search || '')
    const [status, setStatus] = useState(filters.status || '')
    console.log(usePage().props)
    useEffect(() => {
        const timer = setTimeout(() => {
            router.get(
                window.location.pathname,
                { search, status },
                { preserveState: true, replace: true, preserveScroll: true }
            )
        }, 300)
        return () => clearTimeout(timer)
    }, [search, status])

    return (
        <AppLayout>
            <div className="space-y-4">
                {/* Filtros */}
                <div className="flex flex-col sm:flex-row gap-4 bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                    <div className="relative flex-1">
                        <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-4 h-4" />
                        <input
                            type="text"
                            placeholder="Buscar por título..."
                            className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#007853] focus:border-[#007853] outline-none transition-all"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                        />
                    </div>
                    <div className="w-full sm:w-48">
                        <Select
                            options={[{ id: '', status: 'Todos' }, ...statuses].map((s) => ({
                                value: s.id,
                                label: s.status,
                            }))}
                            value={status ? { value: status, label: statuses.find((s) => s.id === status)?.status || '' } : { value: '', label: 'Todos' }}
                            onChange={(selectedOption) => {
                                setStatus(selectedOption ? selectedOption.value : '')
                            }}
                            isClearable={false}
                            placeholder="Filtrar por estado"
                            className="basic-multi-select"
                            classNamePrefix="select"
                        />
                    </div>
                </div>

                <div className="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                    <InfiniteScroll data="campaigns">
                        <table className="min-w-full bg-white divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">ID</th>
                                    <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">Título</th>
                                    <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">Estado</th>
                                    <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">Creada</th>
                                    <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">Acciones</th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-100">
                                {Array.isArray(campaigns?.data) && campaigns.data.length > 0 ? (
                                    campaigns.data.map((campaign) => (
                                        <tr key={campaign.id} className="hover:bg-gray-50">
                                            <td className="px-4 py-3 text-sm text-gray-900">{campaign.id}</td>
                                            <td className="px-4 py-3 text-sm text-gray-900">{campaign.title}</td>
                                            <td className="px-4 py-3 text-sm text-gray-700">{campaign.status.status}</td>
                                            <td className="px-4 py-3 text-sm text-gray-700">
                                                {campaign.created_at ? new Date(campaign.created_at).toLocaleString() : '-'}
                                            </td>
                                            <td className="px-4 py-3 text-sm">
                                                <a
                                                    href={`/campaigns/${campaign.id}`}
                                                    className="text-blue-600 hover:underline"
                                                >
                                                    Ver
                                                </a>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan={5} className="px-4 py-6 text-center text-sm text-gray-500">
                                            No hay campañas
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </InfiniteScroll>
                </div>
            </div>
        </AppLayout>
    )
}