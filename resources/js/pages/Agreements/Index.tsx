import { useState } from 'react'
import { InfiniteScroll, router } from '@inertiajs/react'
import { Search, Eye, Pencil } from 'lucide-react'
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
    is_active: string;
    [key: string]: unknown;
}

interface Props {
    agreements: { data: Agreement[] };
    filters: { search?: string; status?: string };
}

const statusOptions = [
    { value: '', label: 'Todos' },
    { value: '1', label: 'Activo' },
    { value: '0', label: 'Inactivo' },
];

export default function AgreementsIndex({ agreements, filters = {} }: Props) {
    const [search, setSearch] = useState(filters.search || '')
    const [status, setStatus] = useState(filters.status || '')

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
                
                <div className="flex flex-col sm:flex-row gap-4 mt-4 bg-white p-4 rounded-lg shadow-sm border border-gray-200">
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
                                                {/* Textos con ajuste de línea (Text Wrap) */}
                                                <td className="px-4 py-3 text-sm text-gray-900 whitespace-normal max-w-[200px]">{agreement.name}</td>
                                                <td className="px-4 py-3 text-sm text-gray-900 whitespace-normal max-w-[200px]">{agreement.legal_name}</td>
                                                
                                                <td className="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">{agreement.tax_id}</td>
                                                <td className="px-4 py-3 text-sm text-gray-900 whitespace-normal max-w-[150px]">{agreement.contact_person}</td>
                                                <td className="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">{agreement.contact_phone}</td>
                                                
                                                <td className="px-4 py-3 text-sm">
                                                    <span className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                                                        Number(agreement.is_active) === 1 
                                                        ? 'bg-green-100 text-green-800' 
                                                        : 'bg-red-100 text-red-800'
                                                    }`}>
                                                        {Number(agreement.is_active) === 1 ? 'Activo' : 'Inactivo'}
                                                    </span>
                                                </td>
                                                
                                                <td className="px-4 py-3 text-sm">
                                                    {/* Botones alineados horizontalmente */}
                                                    <div className="flex items-center gap-2">
                                                        
                                                        {/* Botón Ver: Color #00953b */}
                                                        <a
                                                            href={`/agreement/${agreement.id}`}
                                                            title="Ver detalles"
                                                            className="inline-flex items-center justify-center p-2 text-sm font-medium text-white bg-[#00953b] rounded-md hover:brightness-90 transition-all shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#00953b]"
                                                        >
                                                            <Eye className="w-4 h-4" />
                                                        </a>

                                                        {/* Botón Editar: Color #007853 */}
                                                        <a
                                                            href={`/agreement/${agreement.id}/edit`}
                                                            title="Editar convenio"
                                                            className="inline-flex items-center justify-center p-2 text-sm font-medium text-white bg-[#007853] rounded-md hover:brightness-90 transition-all shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#007853]"
                                                        >
                                                            <Pencil className="w-4 h-4" />
                                                        </a>
                                                    </div>
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