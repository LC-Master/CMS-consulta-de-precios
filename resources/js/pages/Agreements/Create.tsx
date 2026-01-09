import AppLayout from '@/layouts/app-layout'
import { index, store } from '@/routes/agreement'
import { breadcrumbs } from '@/helpers/breadcrumbs'
import { useForm, Link, Head } from '@inertiajs/react'
import { Save } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Spinner } from '@/components/ui/spinner'

export default function AgreementCreate() {

    const { data, setData, processing, errors, post, cancel } = useForm({
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

        post(store().url)
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs('Crear Convenio', index().url)}>
            <Head title="Crear Convenio" />
            <div className="p-6 space-y-6">
                <div className="ml-4 mb-4">
                    <h1 className="text-4xl font-semibold leading-tight text-gray-800">Crear Convenio</h1>
                    <p className="text-gray-600 mt-1">Complete los datos del nuevo convenio.</p>
                </div>

                <div className="space-y-4 w-full pt-10 rounded-3xl p-6 bg-white shadow-[0_0_20px_rgba(0,0,0,0.08)]">
                    <form id="form" method="post" onSubmit={handleSubmit} className="space-y-4 ">
                        {/* Fila 1: Nombres */}
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label htmlFor="name" className="block text-sm font-bold mb-3 text-gray-700">Nombre Comercial. *</label>
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
                                <label htmlFor="legal_name" className="block text-sm font-bold mb-3 text-gray-700">Razón Social. *</label>
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
                                <label htmlFor="tax_id" className="block text-sm font-bold text-gray-700">RIF / Identificación Fiscal. *</label>
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
                                <label htmlFor="contact_person" className="block text-sm font-bold text-gray-700">Persona de Contacto. *</label>
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
                                <label htmlFor="contact_email" className="block text-sm font-bold mb-3 text-gray-700">Correo Electrónico. *</label>
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
                                <label htmlFor="contact_phone" className="block text-sm font-bold mb-3 text-gray-700">Teléfono. *</label>
                                <input
                                    type="text"
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
                                <label htmlFor="start_date" className="block text-sm font-bold mb-3 text-gray-700">Fecha Inicio. *</label>
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
                                <label htmlFor="end_date" className="block text-sm font-bold mb-3 text-gray-700">Fecha Fin. *</label>
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

                            <div className="md:col-span-2">
                                <label htmlFor="observations" className="block text-sm font-bold mb-3 text-gray-700">
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
                            onClick={() => {
                                if (processing) cancel()
                            }}
                            className="bg-red-500 text-white rounded-md px-6 py-3 shadow hover:brightness-95"
                        >
                            Cancelar
                        </Link>
                    </div>
                </div>
            </div>
        </AppLayout >
    )
}