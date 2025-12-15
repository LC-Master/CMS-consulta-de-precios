import AppLayout from '@/layouts/app-layout'
import { BreadcrumbItem } from '@/types'
import { useForm } from '@inertiajs/react'

export default function AgreementCreate() {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Crear convenio',
            href: '/agreement', 
        },
    ];

    const { data, setData, processing, errors, post } = useForm({
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

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault()
        post('/agreement') 
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <div className="p-6 space-y-6">
                <form id="form" method="post" onSubmit={handleSubmit} className="space-y-4">
                    {/* Fila 1: Nombres */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label htmlFor="name" className="block text-sm font-medium text-gray-700">Nombre Comercial</label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value={data.name}
                                required
                                placeholder='Ej. Empresa X'
                                onChange={e => setData('name', e.target.value)}
                                className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-locatel-medio"
                            />
                            {errors.name && <p className="text-red-500 text-sm mt-1">{errors.name}</p>}
                        </div>

                        <div>
                            <label htmlFor="legal_name" className="block text-sm font-medium text-gray-700">Razón Social</label>
                            <input
                                type="text"
                                id="legal_name"
                                name="legal_name"
                                value={data.legal_name}
                                required
                                placeholder='Ej. Inversiones Empresa X, C.A.'
                                onChange={e => setData('legal_name', e.target.value)}
                                className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-locatel-medio"
                            />
                            {errors.legal_name && <p className="text-red-500 text-sm mt-1">{errors.legal_name}</p>}
                        </div>
                    </div>

                    {/* Fila 2: Identificación y Contacto */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label htmlFor="tax_id" className="block text-sm font-medium text-gray-700">RIF / Identificación Fiscal</label>
                            <input
                                type="text"
                                id="tax_id"
                                name="tax_id"
                                value={data.tax_id}
                                required
                                placeholder='Ej. J-12345678-9'
                                onChange={e => setData('tax_id', e.target.value)}
                                className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-locatel-medio"
                            />
                            {errors.tax_id && <p className="text-red-500 text-sm mt-1">{errors.tax_id}</p>}
                        </div>

                        <div>
                            <label htmlFor="contact_person" className="block text-sm font-medium text-gray-700">Persona de Contacto</label>
                            <input
                                type="text"
                                id="contact_person"
                                name="contact_person"
                                value={data.contact_person}
                                required
                                placeholder='Nombre del representante'
                                onChange={e => setData('contact_person', e.target.value)}
                                className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-locatel-medio"
                            />
                            {errors.contact_person && <p className="text-red-500 text-sm mt-1">{errors.contact_person}</p>}
                        </div>
                    </div>

                    {/* Fila 3: Comunicación */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label htmlFor="contact_email" className="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                            <input
                                type="email"
                                id="contact_email"
                                name="contact_email"
                                value={data.contact_email}
                                required
                                placeholder='contacto@empresa.com'
                                onChange={e => setData('contact_email', e.target.value)}
                                className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-locatel-medio"
                            />
                            {errors.contact_email && <p className="text-red-500 text-sm mt-1">{errors.contact_email}</p>}
                        </div>

                        <div>
                            <label htmlFor="contact_phone" className="block text-sm font-medium text-gray-700">Teléfono</label>
                            <input
                                type="number"
                                id="contact_phone"
                                name="contact_phone"
                                value={data.contact_phone}
                                required
                                placeholder='4141234567'
                                onChange={e => setData('contact_phone', e.target.value)}
                                className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-locatel-medio"
                            />
                            {errors.contact_phone && <p className="text-red-500 text-sm mt-1">{errors.contact_phone}</p>}
                        </div>
                    </div>

                    {/* Fila 4: Fechas */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label htmlFor="start_date" className="block text-sm font-medium text-gray-700">Fecha Inicio</label>
                            <input
                                type="date"
                                id="start_date"
                                name="start_date"
                                value={data.start_date}
                                required
                                onChange={e => setData('start_date', e.target.value)}
                                className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-locatel-medio"
                            />
                            {errors.start_date && <p className="text-red-500 text-sm mt-1">{errors.start_date}</p>}
                        </div>

                        <div>
                            <label htmlFor="end_date" className="block text-sm font-medium text-gray-700">Fecha Fin</label>
                            <input
                                type="date"
                                id="end_date"
                                name="end_date"
                                value={data.end_date}
                                required
                                onChange={e => setData('end_date', e.target.value)}
                                className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-locatel-medio"
                            />
                            {errors.end_date && <p className="text-red-500 text-sm mt-1">{errors.end_date}</p>}
                        </div>
                   <div>
                    <label htmlFor="observations" className="block text-sm font-medium text-gray-700">
                        Observaciones <span className="text-gray-400 font-normal">(Opcional)</span>
                    </label>
                    <textarea
                        id="observations"
                        name="observations"
                        value={data.observations}
                        rows={4}
                        placeholder="Ingrese notas o detalles adicionales del convenio..."
                        onChange={e => setData('observations', e.target.value)}
                        className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-locatel-medio"
                    />
                    {errors.observations && <p className="text-red-500 text-sm mt-1">{errors.observations}</p>}
                </div>
                    </div>
                </form>

                <div className="flex flex-wrap justify-center gap-3">
                    <button
                        form="form"
                        className="bg-locatel-medio text-white rounded-md px-6 py-3 shadow hover:brightness-95 disabled:opacity-50"
                        disabled={processing}
                    >
                        Guardar
                    </button>

                    <button
                        type="button"
                        onClick={() => (window.location.href = '/agreement')}
                        className="bg-red-500 text-white rounded-md px-6 py-3 shadow hover:brightness-95"
                    >
                        Cancelar
                    </button>
                </div>
            </div>
        </AppLayout>
    )
}