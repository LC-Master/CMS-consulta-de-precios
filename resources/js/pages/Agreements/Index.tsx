import { useState } from 'react'
import { InfiniteScroll, router } from '@inertiajs/react'
import { Search, Eye } from 'lucide-react'
import AppLayout from '@/layouts/app-layout';
import Select from 'react-select';
import { useUpdateEffect } from '@/hooks/useUpdateEffect';

interface Agreement {
    id: string;
    name: string;
    legal_name: string;
    tax_id: string;
    contact_person: string;
    contact_phone: string;
    is_active: string; // Puede venir como '1', '0', 1, 0
    [key: string]: unknown;
}

interface Props {
    agreements: { data: Agreement[] };
    // Agregamos los filtros a las props
    filters: { search?: string; status?: string };
}

// Opciones estáticas para el filtro de estatus
const statusOptions = [
    { value: '', label: 'Todos' },
    { value: '1', label: 'Activo' },
    { value: '0', label: 'Inactivo' },
];

export default function AgreementsIndex({ agreements, filters = {} }: Props) {
    // 1. Inicializamos estado con los filtros que vienen del backend
    const [search, setSearch] = useState(filters.search || '')
    const [status, setStatus] = useState(filters.status || '')

    // 2. Efecto para recargar la data cuando cambian los filtros
    useUpdateEffect(() => {
        router.get(
            window.location.pathname,
            { search, status },
            { preserveState: true, replace: true, preserveScroll: true }
        )
    }, [search, status])

    return (
        <AppLayout>
            <div className="space-y-4 px-4 pb-4">
                
                {/* 3. Sección de Filtros Agregada */}
                <div className="flex flex-col sm:flex-row gap-4 mt-4 bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                    {/* Input de Búsqueda */}
                    <div className="relative flex-1">
                        <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-4 h-4" />
                        <input
                            type="text"
                            placeholder="Buscar por nombre o RIF..."
                            className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                        />
                    </div>

                    {/* Select de Estatus */}
                    <div className="w-full sm:w-48">
                        <Select
                            options={statusOptions}
                            value={statusOptions.find(option => option.value === status) || statusOptions[0]}
                            onChange={(selectedOption) => {
                                setStatus(selectedOption ? selectedOption.value : '')
                            }}
                            isClearable={false}
                            placeholder="Estatus"
                            className="basic-multi-select"
                            classNamePrefix="select"
                        />
                    </div>
                </div>

                <div className="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                    <InfiniteScroll data="agreements">
                        <table className="min-w-full bg-white divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">Nombre</th>
                                    <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">Nombre Legal</th>
                                    <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">RIF</th>
                                    <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">Persona Contacto</th>
                                    <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">Teléfono</th>
                                    <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">Estatus</th>
                                    <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">Acciones</th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-100">
                                {Array.isArray(agreements?.data) && agreements.data.length > 0 ? (
                                    agreements.data.map((agreement) => {
                                        return (
                                            <tr key={agreement.id} className="hover:bg-gray-50">
                                                <td className="px-4 py-3 text-sm text-gray-900">{agreement.name}</td>
                                                <td className="px-4 py-3 text-sm text-gray-900">{agreement.legal_name}</td>
                                                <td className="px-4 py-3 text-sm text-gray-900">{agreement.tax_id}</td>
                                                <td className="px-4 py-3 text-sm text-gray-900">{agreement.contact_person}</td>
                                                <td className="px-4 py-3 text-sm text-gray-900">{agreement.contact_phone}</td>
                                                
                                                {/* Estatus con Badge */}
                                                <td className="px-4 py-3 text-sm">
                                                    <span className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                                                        Number(agreement.is_active) === 1 
                                                        ? 'bg-green-100 text-green-800' 
                                                        : 'bg-red-100 text-red-800'
                                                    }`}>
                                                        {Number(agreement.is_active) === 1 ? 'Activo' : 'Inactivo'}
                                                    </span>
                                                </td>
                                                
                                                {/* Botón Ver */}
                                                <td className="px-4 py-3 text-sm">
                                                    <a
                                                        href={`/agreement/${agreement.id}`}
                                                        className="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                                    >
                                                        <Eye className="w-4 h-4" />
                                                    </a>
                                                </td>
                                            </tr>
                                        )
                                    })
                                ) : (
                                    <tr>
                                        <td colSpan={7} className="px-4 py-6 text-center text-sm text-gray-500">
                                            No se encontraron convenios.
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