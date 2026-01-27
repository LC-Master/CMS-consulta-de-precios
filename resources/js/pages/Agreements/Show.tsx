import AppLayout from '@/layouts/app-layout'
import { index } from '@/routes/agreement'
import { breadcrumbs } from '@/helpers/breadcrumbs'
import { Agreement } from '@/types/agreement/index.types'
import { ArrowLeft, CircleAlert, FileText } from 'lucide-react'
import { Head, Link } from '@inertiajs/react'

export default function AgreementShow({ agreement }: { agreement: Agreement }) {
    return (
        <AppLayout breadcrumbs={breadcrumbs(agreement.name ?? 'Mostrar Convenio', index().url)}>
            <Head title={agreement.name ?? 'Mostrar Convenio'} />
            <div className="p-6 bg-gray-100 space-y-6">
                <div>
                    <div className='flex items-center gap-4'>
                        <h1 className="text-2xl font-semibold leading-7 text-gray-900">{agreement.name}</h1>
                        {agreement.is_active ? (
                            <span className="px-3 py-1.5 inline-flex text-base font-semibold rounded-full bg-green-100 text-green-800">
                                Activo
                            </span>
                        ) : (
                            <span className="px-3 py-1.5 inline-flex text-base font-semibold rounded-full bg-red-100 text-red-800">
                                Inactivo
                            </span>
                        )}
                    </div>
                    <p className="mt-1 text-gray-600">Información detallada sobre el acuerdo comercial, incluyen nombre, contacto y fechas importantes.</p>
                </div>
                <div className="space-y-4">
                    <div className="shadow-lg w-full rounded-3xl p-6 bg-white">
                        <div className="flex items-center gap-2 mb-4 border-b pb-2">
                            <CircleAlert className="text-gray-500" />
                            <h2 className="text-lg font-semibold text-gray-900">Detalles del convenio</h2>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                            <div>
                                <p className="text-sm text-gray-500">Nombre comercial</p>
                                <p className="mt-1 text-base font-medium text-gray-900">{agreement.name || '—'}</p>
                            </div>
                            <div>
                                <p className="text-sm text-gray-500">Razón social</p>
                                <p className="mt-1 text-base font-medium text-gray-900">{agreement.legal_name || '—'}</p>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                            <div>
                                <p className="text-sm text-gray-500">Persona de contacto</p>
                                <p className="mt-1 text-base text-gray-900">{agreement.contact_person || '—'}</p>
                            </div>
                            <div>
                                <p className="text-sm text-gray-500">Correo electrónico</p>
                                <p className="mt-1 text-base text-gray-900">{agreement.contact_email || '—'}</p>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                            <div>
                                <p className="text-sm text-gray-500">Teléfono</p>
                                <p className="mt-1 text-base text-gray-900">{agreement.contact_phone || '—'}</p>
                            </div>
                            <div>
                                <p className="text-sm text-gray-500">RIF / Identificación fiscal</p>
                                <p className="mt-1 text-base text-gray-900">{agreement.tax_id || '—'}</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div className="shadow-lg w-full rounded-3xl p-6 bg-white">
                            <div className='flex items-center gap-2 mb-4 border-b pb-2'>
                                <FileText />
                                <h2 className='text-lg font-semibold text-gray-900'>Detalles del Acuerdo Comercial</h2>
                            </div>
                            <p className="mt-1 text-gray-900 bg-gray-200 h-40 p-6 rounded-4xl whitespace-pre-wrap">{agreement.observations || 'No hay observaciones'}</p>
                        </div>
                    </div>

                </div>

                <div className="flex flex-wrap justify-center gap-3">

                    <Link
                        viewTransition
                        href={index().url}
                        className="bg-white text-gray-900 shadow-2xl border border-black rounded-md px-6 py-3 hover:brightness-95 inline-flex items-center"
                    >
                        <ArrowLeft className="mr-2" /> Volver
                    </Link>
                </div>
            </div>
        </AppLayout>
    )
}