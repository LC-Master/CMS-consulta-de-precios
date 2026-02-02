import AppLayout from "@/layouts/app-layout"
import { update, index } from "@/routes/agreement"
import { breadcrumbs } from "@/helpers/breadcrumbs"
import { type Agreement } from "@/types/agreement/index.types"
import { useForm, Link, Head } from "@inertiajs/react"
import { Label } from "@/components/ui/label"
import { Spinner } from "@/components/ui/spinner"
import { Save, UserSearch } from "lucide-react"
import { Button } from "@/components/ui/button"
import InputError from "@/components/input-error"
import { Input } from "@/components/ui/input"
import Select from 'react-select'
import { useState, useRef } from 'react'
import axios from 'axios'

interface Supplier {
    id: string;
    SupplierName: string;
    AccountNumber: string;
    ContactName: string;
    EmailAddress: string;
    PhoneNumber: string;
    Notes: string;
}

export default function AgreementsEdit({ agreement, defaultSuppliers = [] }: { agreement: Agreement, defaultSuppliers: Supplier[] }) {
    
    const { data, setData, processing, errors, put, cancel } = useForm({
        supplier_id: agreement.supplier_id ?? '',
        name: agreement.name ?? '',
        legal_name: agreement.legal_name ?? '',
        tax_id: agreement.tax_id ?? '',
        contact_person: agreement.contact_person ?? '',
        contact_email: agreement.contact_email ?? '',
        contact_phone: agreement.contact_phone ?? '',
        is_active: agreement.is_active === true || String(agreement.is_active) === '1' ? true : false,
        observations: agreement.observations ?? '',
    })

    const initialOptions = defaultSuppliers.map(s => ({
        value: s.id,
        label: `${s.SupplierName} - ${s.AccountNumber}`,
        original: s
    }));

    const [options, setOptions] = useState(initialOptions);
    const [isLoading, setIsLoading] = useState(false);
    
    // Referencia para Debounce
    const searchTimeout = useRef<NodeJS.Timeout | null>(null);

    const currentSupplierOption = options.find(op => op.value === data.supplier_id) || null;

    const handleInputChange = (inputValue: string, { action }: any) => {
        if (action !== 'input-change') return;
        
        // 1. Limpieza INICIAL del timeout
        if (searchTimeout.current) {
            clearTimeout(searchTimeout.current);
        }

        // 2. Restauración si está vacío
        if (!inputValue) {
            setOptions(initialOptions);
            return;
        }

        // 3. Programación de nueva búsqueda
        searchTimeout.current = setTimeout(() => {
            setIsLoading(true);
            axios.get('/api/suppliers/search', { params: { query: inputValue } })
                .then((response) => {
                    if (response.data && response.data.length > 0) {
                        setOptions(response.data);
                    } else {
                        setOptions([]);
                    }
                })
                .catch(err => console.error(err))
                .finally(() => setIsLoading(false));
        }, 300);
    };

    const handleSupplierChange = (option: any) => {
        if (!option) return;
        const s = option.original;
        setData(previousData => ({
            ...previousData,
            supplier_id: s.id,
            name: s.SupplierName,
            legal_name: s.SupplierName,
            tax_id: s.AccountNumber,
            contact_person: s.ContactName || '',
            contact_email: s.EmailAddress || '',
            contact_phone: s.PhoneNumber || '',
            observations: s.Notes || '',
        }));
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault()
        put(update({ id: agreement.id }).url)
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs('Editar Acuerdo', index().url)}>
            <Head title="Edición de convenio" />
            <div className="p-6 space-y-6">
                <div className="ml-4 mb-4 flex items-center justify-between">
                    <div>
                        <h1 className="text-4xl font-semibold leading-tight text-gray-800">Editar Acuerdo Comercial</h1>
                        <p className="text-gray-600 mt-1">Modifique los detalles del acuerdo comercial según sea necesario.</p>
                    </div>
                    <div className="flex bg-gray-50 shadow-sm mr-6 w-auto rounded-2xl items-center gap-2 p-1">
                        <Label className="pl-3 text-gray-600 font-medium text-sm">Estado:</Label>

                        <button
                            type="button"
                            role="switch"
                            aria-checked={data.is_active}
                            onClick={() => setData('is_active', !data.is_active)}
                            className={`relative inline-flex items-center h-6 w-11 shrink-0 transition-colors duration-200 rounded-full focus:outline-none ${data.is_active ? 'bg-green-500' : 'bg-gray-300'}`}
                        >
                            <span className={`inline-block w-4 h-4 bg-white rounded-full shadow transform transition-transform duration-200 ${data.is_active ? 'translate-x-5' : 'translate-x-1'}`} />
                        </button>

                        <span className={`pr-3 text-sm font-medium ${data.is_active ? 'text-green-600' : 'text-red-600'}`}>
                            {data.is_active ? 'Activo' : 'Inactivo'}
                        </span>
                    </div>
                </div>
                
                <div className="space-y-4 w-full pt-10 rounded-3xl p-6 bg-white shadow-[0_0_20px_rgba(0,0,0,0.08)]">
                    <form id="form" method="post" onSubmit={handleSubmit} className="space-y-4 ">
                        
                        <div className="bg-green-50 p-4 rounded-xl border border-green-100 mb-6">
                            <Label className="block text-sm font-bold mb-2 text-green-800 flex items-center gap-2">
                                <UserSearch className="w-4 h-4" /> Proveedor Maestro (Actualizar datos)
                            </Label>
                            <Select
                                options={options}
                                onInputChange={handleInputChange}
                                value={currentSupplierOption}
                                onChange={handleSupplierChange}
                                isLoading={isLoading}
                                placeholder="Buscar proveedor para actualizar..."
                                classNamePrefix="react-select"
                                isClearable
                                isSearchable
                                filterOption={() => true} // CORRECCIÓN CRÍTICA
                                noOptionsMessage={() => isLoading ? "Buscando..." : "No se encontraron proveedores"}
                            />
                            <p className="text-xs text-green-700 mt-2">
                                * Seleccionar un proveedor sobrescribirá los datos actuales.
                            </p>
                            <InputError message={errors.supplier_id} />
                        </div>

                        {/* RESTO DE CAMPOS IGUALES... */}
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <Label className="block text-sm font-bold mb-3 text-gray-700">Nombre Comercial. *</Label>
                                <Input type="text" id="name" value={data.name} required onChange={e => setData('name', e.target.value)} className={`mt-1 block w-full rounded-lg ${data.supplier_id ? 'bg-gray-50' : ''}`} readOnly={!!data.supplier_id} />
                                <InputError message={errors.name} />
                            </div>
                            <div>
                                <Label className="block text-sm font-bold mb-3 text-gray-700">Razón Social. *</Label>
                                <Input type="text" id="legal_name" value={data.legal_name} required onChange={e => setData('legal_name', e.target.value)} className="mt-1 block w-full rounded-lg" />
                                <InputError message={errors.legal_name} />
                            </div>
                        </div>
                        
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <Label className="block text-sm font-bold mb-3 text-gray-700">RIF / Identificación Fiscal. *</Label>
                                <Input type="text" id="tax_id" value={data.tax_id} required onChange={e => setData('tax_id', e.target.value)} className="mt-1 block w-full rounded-lg" />
                                <InputError message={errors.tax_id} />
                            </div>
                            <div>
                                <Label className="block text-sm font-bold mb-3 text-gray-700">Persona de Contacto. *</Label>
                                <Input type="text" id="contact_person" value={data.contact_person} required onChange={e => setData('contact_person', e.target.value)} className="mt-1 block w-full rounded-lg" />
                                <InputError message={errors.contact_person} />
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <Label className="block text-sm font-bold mb-3 text-gray-700">Correo Electrónico. *</Label>
                                <Input type="email" id="contact_email" value={data.contact_email} required onChange={e => setData('contact_email', e.target.value)} className="mt-1 block w-full rounded-lg" />
                                <InputError message={errors.contact_email} />
                            </div>
                            <div>
                                <Label className="block text-sm font-bold mb-3 text-gray-700">Teléfono. *</Label>
                                <Input type="text" id="contact_phone" value={data.contact_phone} required onChange={e => setData('contact_phone', e.target.value)} className="mt-1 block w-full rounded-lg" />
                                <InputError message={errors.contact_phone} />
                            </div>
                        </div>

                        <div className="grid grid-cols-1 gap-4">
                            <div className="md:col-span-2">
                                <Label className="block text-sm font-bold mb-3 text-gray-700">Detalles del Acuerdo Comercial</Label>
                                <textarea id="observations" value={data.observations} rows={4} required onChange={e => setData('observations', e.target.value)} className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2" />
                                <InputError message={errors.observations} />
                            </div>
                        </div>

                    </form>

                    <div className="flex flex-wrap justify-center border-t border-gray-200 pt-20 mt-25 gap-3">
                        <Button
                            form="form"
                            className="bg-locatel-medio flex flex-row h-12 gap-2 items-center text-white rounded-md px-6 py-3 shadow"
                            disabled={processing}
                        >
                            {processing ? (<><Spinner /> Guardando....</>) : <><Save /> Guardar</>}
                        </Button>
                        <Link href={index().url} className="bg-red-500 text-white rounded-md px-6 py-3 shadow flex items-center">Cancelar</Link>
                    </div>
                </div>
            </div>
        </AppLayout>
    )
}