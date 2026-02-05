import AppLayout from '@/layouts/app-layout'
import { index, store } from '@/routes/agreement'
import { breadcrumbs } from '@/helpers/breadcrumbs'
import { useForm, Link, Head } from '@inertiajs/react'
import { Save, UserSearch } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Spinner } from '@/components/ui/spinner'
import Select from 'react-select' 
import { Label } from '@/components/ui/label'
import InputError from '@/components/input-error'
import { Input } from '@/components/ui/input'
import { useState, useRef } from 'react'
import axios from 'axios'

interface Supplier {
    id: number; 
    SupplierName: string;
    AccountNumber: string;
    ContactName: string;
    EmailAddress: string;
    PhoneNumber: string;
    Notes: string;
}

export default function AgreementCreate({ defaultSuppliers = [] }: { defaultSuppliers: Supplier[] }) {

    const { data, setData, processing, errors, post } = useForm({
        supplier_id: '' as string | number, // Acepta numero o string vacio
        name: '',
        legal_name: '',
        tax_id: '',
        contact_person: '',
        contact_email: '',
        contact_phone: '',
        start_date: '',
        end_date: '',
        observations: '',
    })

    const initialOptions = defaultSuppliers.map(s => ({
        value: s.id,
        label: `${s.SupplierName} - ${s.AccountNumber}`,
        original: s
    }));

    const [options, setOptions] = useState(initialOptions);
    const [isLoading, setIsLoading] = useState(false);
    const searchTimeout = useRef<NodeJS.Timeout | null>(null);

    const handleInputChange = (inputValue: string, { action }: any) => {
        if (action !== 'input-change') return;

        if (searchTimeout.current) {
            clearTimeout(searchTimeout.current);
        }

        if (!inputValue) {
            setOptions(initialOptions);
            return;
        }

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
                .catch(err => console.error("Error buscando:", err))
                .finally(() => setIsLoading(false));
        }, 300);
    };

    const handleSupplierChange = (option: any) => {
        if (!option) return;
        const s = option.original;

        setData(previousData => ({
            ...previousData,
            supplier_id: s.id, // Ahora es un numero
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
        post(store().url)
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs('Crear Acuerdo', index().url)}>
            <Head title="Crear Convenio" />
            <div className="p-6 space-y-6">
                <div className="ml-4 mb-4">
                    <h1 className="text-4xl font-semibold leading-tight text-gray-800">Crear Acuerdo Comercial</h1>
                    <p className="text-gray-600 mt-1">Complete los datos del nuevo acuerdo comercial.</p>
                </div>

                <div className="space-y-4 w-full pt-10 rounded-3xl p-6 bg-white shadow-[0_0_20px_rgba(0,0,0,0.08)]">
                    <form id="form" method="post" onSubmit={handleSubmit} className="space-y-4 ">
                        
                        <div className="bg-green-50 p-4 rounded-xl border border-green-100 mb-6">
                            <Label className="block text-sm font-bold mb-2 text-green-800 flex items-center gap-2">
                                <UserSearch className="w-4 h-4" /> Buscar Proveedor Maestro (Autollenado)
                            </Label>
                            
                            <Select
                                options={options}
                                onInputChange={handleInputChange}
                                onChange={handleSupplierChange}
                                isLoading={isLoading}
                                placeholder="Escriba nombre o RIF..."
                                classNamePrefix="react-select"
                                isClearable
                                isSearchable
                                filterOption={() => true}
                                noOptionsMessage={() => isLoading ? "Buscando..." : "No se encontraron proveedores"}
                            />
                            
                            <p className="text-xs text-green-700 mt-2">
                                * Escriba para buscar en la base de datos
                            </p>
                            <InputError message={errors.supplier_id} />
                        </div>

                        {/* Fila 1 */}
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <Label htmlFor="name" className="block text-sm font-bold mb-3 text-gray-700">Nombre Comercial. *</Label>
                                <Input
                                    type="text"
                                    id="name"
                                    value={data.name}
                                    required
                                    placeholder='Ej. Empresa X'
                                    onChange={e => setData('name', e.target.value)}
                                    // Comprobamos si tiene valor (number o string no vacio)
                                    className={`mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-locatel-medio ${data.supplier_id ? 'bg-gray-50' : ''}`}
                                    readOnly={!!data.supplier_id} 
                                />
                                <InputError message={errors.name} />
                            </div>

                            <div>
                                <Label htmlFor="legal_name" className="block text-sm font-bold mb-3 text-gray-700">Razón Social. *</Label>
                                <Input
                                    type="text"
                                    id="legal_name"
                                    value={data.legal_name}
                                    required
                                    placeholder='Ej. Inversiones Empresa X, C.A.'
                                    onChange={e => setData('legal_name', e.target.value)}
                                    className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-locatel-medio"
                                />
                                <InputError message={errors.legal_name} />
                            </div>
                        </div>

                        {/* Fila 2 */}
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <Label htmlFor="tax_id" className="block text-sm font-bold mb-3 text-gray-700">RIF / Identificación Fiscal. *</Label>
                                <Input
                                    type="text"
                                    id="tax_id"
                                    value={data.tax_id}
                                    required
                                    placeholder='Ej. J-12345678-9'
                                    onChange={e => setData('tax_id', e.target.value)}
                                    className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-locatel-medio"
                                />
                                <InputError message={errors.tax_id} />
                            </div>

                            <div>
                                <Label htmlFor="contact_person" className="block text-sm font-bold mb-3 text-gray-700">Persona de Contacto. *</Label>
                                <Input
                                    type="text"
                                    id="contact_person"
                                    value={data.contact_person}
                                    required
                                    placeholder='Nombre del representante'
                                    onChange={e => setData('contact_person', e.target.value)}
                                    className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-locatel-medio"
                                />
                                <InputError message={errors.contact_person} />
                            </div>
                        </div>

                        {/* Fila 3 */}
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <Label htmlFor="contact_email" className="block text-sm font-bold mb-3 text-gray-700">Correo Electrónico. *</Label>
                                <Input
                                    type="email"
                                    id="contact_email"
                                    value={data.contact_email}
                                    required
                                    placeholder='contacto@empresa.com'
                                    onChange={e => setData('contact_email', e.target.value)}
                                    className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-locatel-medio"
                                />
                                <InputError message={errors.contact_email} />
                            </div>

                            <div>
                                <Label htmlFor="contact_phone" className="block text-sm font-bold mb-3 text-gray-700">Teléfono. *</Label>
                                <Input
                                    type="text"
                                    id="contact_phone"
                                    value={data.contact_phone}
                                    required
                                    placeholder='4141234567'
                                    onChange={e => setData('contact_phone', e.target.value)}
                                    className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-locatel-medio"
                                />
                                <InputError message={errors.contact_phone} />
                            </div>
                        </div>

                        {/* Fila 4 */}
                        <div className="grid grid-cols-1 gap-4">
                            <div className="md:col-span-2">
                                <Label htmlFor="observations" className="block text-sm font-bold mb-3 text-gray-700">
                                    Detalles del Acuerdo Comercial
                                </Label>
                                <textarea
                                    id="observations"
                                    value={data.observations}
                                    rows={4}
                                    required
                                    placeholder="Ingrese los detalles del acuerdo comercial..."
                                    onChange={e => setData('observations', e.target.value)}
                                    className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-locatel-medio"
                                />
                                <InputError message={errors.observations} />
                            </div>
                        </div>
                    </form>

                    <div className="flex flex-wrap justify-center border-t border-gray-200 pt-20 mt-25 gap-3">
                        <Button
                            form="form"
                            className="bg-locatel-medio flex flex-row h-12 gap-2 items-center text-white rounded-md px-6 py-3 shadow hover:brightness-95 disabled:opacity-50"
                            disabled={processing}
                        >
                            {processing ? (<><Spinner /> Guardando....</>) : <><Save /> Guardar</>}
                        </Button>

                        <Link
                            viewTransition
                            href={index().url}
                            className="bg-red-500 text-white rounded-md px-6 py-3 shadow hover:brightness-95 flex items-center"
                        >
                            Cancelar
                        </Link>
                    </div>
                </div>
            </div>
        </AppLayout>
    )
}