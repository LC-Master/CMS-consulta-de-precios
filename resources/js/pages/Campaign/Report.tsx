import AppLayout from '@/layouts/app-layout'
import { Head } from '@inertiajs/react'
import { Input } from '@/components/ui/input'
import React, { useState } from 'react'
import { Button } from '@/components/ui/button'
import useToast from '@/hooks/use-toast'
import { index } from '@/routes/campaign'
import { breadcrumbs } from '@/helpers/breadcrumbs'
import { CircleAlert, FileSpreadsheet, Filter } from 'lucide-react'

// Tipos para las props que vienen del controlador
interface Option {
    id: string;
    name?: string;
    status?: string;
}

interface CampaignReportProps {
    flash: any;
    departments: Option[];
    agreements: Option[];
    statuses: Option[];
}

const exportRouteUrl = '/campaigns/export'; 

export default function CampaignReport({ flash, departments, agreements, statuses }: CampaignReportProps) {
    const ToastComponent = useToast(flash)
    
    // Estado local para el formulario (incluyendo nuevos filtros)
    const [data, setData] = useState({
        start_at: '',
        end_at: '',
        department_id: '',
        agreement_id: '',
        status_id: ''
    });

    const [loading, setLoading] = useState(false);
    const [errors, setErrors] = useState({ start_at: '', end_at: '' });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setErrors({ start_at: '', end_at: '' });

        if (!data.start_at) {
            setErrors(prev => ({ ...prev, start_at: 'La fecha de inicio es requerida.' }));
            return;
        }
        if (!data.end_at) {
            setErrors(prev => ({ ...prev, end_at: 'La fecha fin es requerida.' }));
            return;
        }
        if (new Date(data.start_at) > new Date(data.end_at)) {
            setErrors(prev => ({ ...prev, end_at: 'La fecha fin debe ser posterior a la de inicio.' }));
            return;
        }

        setLoading(true);

        // Construimos query params incluyendo los filtros opcionales
        // Filtramos claves vacías para limpiar la URL
        const params = new URLSearchParams();
        params.append('start_at', data.start_at);
        params.append('end_at', data.end_at);
        
        if(data.department_id) params.append('department_id', data.department_id);
        if(data.agreement_id) params.append('agreement_id', data.agreement_id);
        if(data.status_id) params.append('status_id', data.status_id);

        window.location.href = `${exportRouteUrl}?${params.toString()}`;

        setTimeout(() => setLoading(false), 2000);
    }

    // Componente reutilizable para selects simples
    const SelectField = ({ label, id, value, options, onChange, displayKey = 'name' }: any) => (
        <div>
            <label htmlFor={id} className="block text-sm font-bold mb-2 ml-1 text-gray-700">{label}</label>
            <div className="relative">
                <select
                    id={id}
                    value={value}
                    onChange={(e) => onChange(e.target.value)}
                    className="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-locatel-medio appearance-none cursor-pointer"
                >
                    <option value="">Todos</option>
                    {options.map((opt: any) => (
                        <option key={opt.id} value={opt.id}>
                            {opt[displayKey]}
                        </option>
                    ))}
                </select>
                {/* Icono de flecha para el select nativo */}
                <div className="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <svg className="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                    </svg>
                </div>
            </div>
        </div>
    );

    return (
        <AppLayout breadcrumbs={breadcrumbs('Generar Reporte', index().url)}>
            {ToastComponent.ToastContainer()}
            <Head title="Reporte de Campañas" />
            <div className="p-6 space-y-6">
                <div className='ml-2'>
                    <h1 className="text-3xl font-bold">Generar reporte de campañas</h1>
                    <p className='text-gray-600 '>Utiliza los filtros a continuación para personalizar tu reporte en Excel.</p>
                </div>
                
                <div className="shadow-[0_0_20px_rgba(0,0,0,0.08)] rounded-lg bg-white w-full">
                    <form id="report-form" onSubmit={handleSubmit} className="space-y-6" noValidate>
                        <div className='flex items-center gap-2 pl-6 pr-6 mb-4 pt-6 pb-2'>
                            <CircleAlert className="text-locatel-medio" />
                            <h2 className='text-bold font-bold text-lg text-gray-900'>
                                Rango de Fechas (Obligatorio)
                            </h2>
                        </div>

                        {/* Fila 1: Fechas */}
                        <div className="grid grid-cols-1 md:grid-cols-2 pl-6 pr-6 gap-6 pb-2">
                            <div>
                                <label htmlFor="start_at" className="block text-sm font-bold mb-2 ml-1 text-gray-700">Fecha Desde *</label>
                                <Input
                                    type="date"
                                    id="start_at"
                                    value={data.start_at}
                                    required
                                    onChange={e => setData({ ...data, start_at: e.target.value })}
                                    className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-locatel-medio"
                                />
                                {errors.start_at && <p className="text-red-500 text-sm mt-1">{errors.start_at}</p>}
                            </div>
                            <div>
                                <label htmlFor="end_at" className="block text-sm font-bold mb-2 ml-1 text-gray-700">Fecha Hasta *</label>
                                <Input
                                    type="date"
                                    id="end_at"
                                    value={data.end_at}
                                    required
                                    onChange={e => setData({ ...data, end_at: e.target.value })}
                                    className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-locatel-medio"
                                />
                                {errors.end_at && <p className="text-red-500 text-sm mt-1">{errors.end_at}</p>}
                            </div>
                        </div>

                        <div className='flex items-center gap-2 pl-6 pr-6 mb-2 pt-2 border-t border-gray-100 mt-4'>
                            <Filter className="text-locatel-medio w-5 h-5" />
                            <h2 className='text-bold font-bold text-lg text-gray-900'>
                                Filtros Opcionales
                            </h2>
                        </div>

                        {/* Fila 2: Filtros Opcionales */}
                        <div className="grid grid-cols-1 md:grid-cols-3 pl-6 pr-6 gap-6 pb-6">
                            <SelectField 
                                label="Departamento" 
                                id="department" 
                                value={data.department_id} 
                                options={departments} 
                                onChange={(val: string) => setData({...data, department_id: val})} 
                            />
                            
                            <SelectField 
                                label="Acuerdo / Convenio" 
                                id="agreement" 
                                value={data.agreement_id} 
                                options={agreements} 
                                onChange={(val: string) => setData({...data, agreement_id: val})} 
                            />

                            <SelectField 
                                label="Estatus" 
                                id="status" 
                                value={data.status_id} 
                                options={statuses} 
                                displayKey="status"
                                onChange={(val: string) => setData({...data, status_id: val})} 
                            />
                        </div>
                    </form>

                    <div className="flex flex-wrap w-full p-6 border-t shadow-t-lg border-gray-200 bg-[#fcfcfc] justify-center gap-3 rounded-b-lg">
                        <Button
                            type="submit"
                            form="report-form"
                            className="bg-locatel-medio hover:bg-locatel-oscuro active:bg-locatel-oscuro flex flex-row items-center h-12 text-white rounded-md px-6 py-3 shadow hover:brightness-95 disabled:opacity-50 transition-all"
                            disabled={loading}
                        >
                            {loading ? (
                                <span className="flex items-center">Generando reporte...</span>
                            ) : (
                                <><FileSpreadsheet className="mr-2 h-5 w-5" /> Descargar Reporte Excel</>
                            )}
                        </Button>
                    </div>
                </div>
            </div>
        </AppLayout >
    )
}