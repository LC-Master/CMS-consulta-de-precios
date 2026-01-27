import AppLayout from '@/layouts/app-layout'
import { Head, useForm } from '@inertiajs/react'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import useToast from '@/hooks/use-toast'
import { index } from '@/routes/campaign'
import { breadcrumbs } from '@/helpers/breadcrumbs'
import { CircleAlert, FileSpreadsheet, Filter } from 'lucide-react'
import Select from 'react-select'
import { Agreement } from '@/types/agreement/index.types'
import { Department, Option } from '@/types/campaign/index.types'
import { CampaignReportProps } from '@/types/campaign/page.type'
import { Status } from '@/types/status/status.type'
import { Label } from '@/components/ui/label'
import { makeOptions } from '@/helpers/makeOptions'
import { list } from '@/routes/campaign/export'
import InputError from '@/components/input-error'
import { processZodErrors } from '@/helpers/formattingError'
import { Spinner } from '@/components/ui/spinner'
import { reportFormSchema } from '@/schemas/reportForm.schema'
import { useState } from 'react'
import { Flash } from '@/types/flash/flash.type'

export default function CampaignReport({ flash, departments, agreements, statuses }: CampaignReportProps) {
    const [flashMessage, setFlashMessage] = useState<Flash>(flash || {})
    const ToastComponent = useToast(flashMessage)
    const [processing, setProcessing] = useState(false)
    const { data, setData, errors, setError } = useForm({
        start_at: '',
        end_at: '',
        department_id: '',
        agreement_id: '',
        status_id: ''
    })
    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();

        const result = reportFormSchema.safeParse(data);
        if (!result.success) {
            processZodErrors(result.error, setError);
            return;
        }

        try {
            setProcessing(true);

            const queryParams = new URLSearchParams(data).toString();
            const response = await fetch(`${list().url}?${queryParams}`);

            if (!response.ok) throw new Error('Error en la descarga');

            const disposition = response.headers.get('Content-Disposition');
            let filename = 'reporte.xlsx';

            if (disposition && disposition.indexOf('filename=') !== -1) {
                const filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                const matches = filenameRegex.exec(disposition);
                if (matches != null && matches[1]) {
                    filename = matches[1].replace(/['"]/g, '');
                }
            }

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');

            a.href = url;
            a.download = filename;

            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);

        } catch {
            setFlashMessage({ error: 'Hubo un error al generar el reporte.' });
        } finally {
            setProcessing(false);
        }
    }

    const optionsStatuses: Option[] = makeOptions<Status>(statuses, s => s.id, s => s.status)
    const optionsDepartment: Option[] = makeOptions<Department>(departments, d => d.id, d => d.name)
    const optionsAgreement: Option[] = makeOptions<Agreement>(agreements, a => a.id, a => a.name)

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
                    <form id="form" onSubmit={handleSubmit} className="space-y-6" noValidate>
                        <div className='flex items-center gap-2 pl-6 pr-6 mb-4 pt-6 pb-2'>
                            <CircleAlert className="text-locatel-medio" />
                            <h2 className='text-bold font-bold text-lg text-gray-900'>
                                Rango de Fechas (Obligatorio)
                            </h2>
                        </div>

                        {/* Fila 1: Fechas */}
                        <div className="grid grid-cols-1 md:grid-cols-2 pl-6 pr-6 gap-6 pb-2">
                            <div>
                                <Label htmlFor="start_at" className="block text-sm font-bold mb-2 ml-1 text-gray-700">Fecha Desde *</Label>
                                <Input
                                    type="date"
                                    id="start_at"
                                    value={data.start_at}
                                    required
                                    onChange={e => setData({ ...data, start_at: e.target.value })}
                                    className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-locatel-medio"
                                />
                                <InputError message={errors.start_at} className="mt-1" />
                            </div>
                            <div>
                                <Label htmlFor="end_at" className="block text-sm font-bold mb-2 ml-1 text-gray-700">Fecha Hasta *</Label>
                                <Input
                                    type="date"
                                    id="end_at"
                                    value={data.end_at}
                                    required
                                    onChange={e => setData({ ...data, end_at: e.target.value })}
                                    className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-locatel-medio"
                                />
                                <InputError message={errors.end_at} className="mt-1" />
                            </div>
                        </div>

                        <div className='flex items-center gap-2 pl-6 pr-6 mb-2 pt-2 border-t border-gray-100 mt-4'>
                            <Filter className="text-locatel-medio w-5 h-5" />
                            <h2 className='text-bold font-bold text-lg text-gray-900'>
                                Filtros Opcionales
                            </h2>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-3 pl-6 pr-6 gap-6 pb-6">
                            <Select
                                options={optionsDepartment}
                                inputId='department_id'
                                name='department_id'
                                placeholder='Selecciona un Departamento'
                                isClearable
                                onChange={option => setData('department_id', option ? option.value : '')}
                                className="basic-single"
                                classNamePrefix="select"
                                styles={{
                                    control: (provided) => ({
                                        ...provided,
                                        borderRadius: '0.375rem',
                                    }),
                                }}
                            />
                            <InputError message={errors.department_id} className="mt-1" />
                            <Select
                                options={optionsAgreement}
                                inputId='agreement_id'
                                name='agreement_id'
                                placeholder='Selecciona un Acuerdo'
                                isClearable
                                onChange={option => setData('agreement_id', option ? option.value : '')}
                                className="basic-single"
                                classNamePrefix="select"
                                styles={{
                                    control: (provided) => ({
                                        ...provided,
                                        borderRadius: '0.375rem',
                                    }),
                                }}
                            />
                            <InputError message={errors.agreement_id} className="mt-1" />
                            <Select
                                options={optionsStatuses}
                                inputId='status_id'
                                name='status_id'
                                placeholder='Selecciona un Estatus'
                                isClearable
                                onChange={option => setData('status_id', option ? option.value : '')}
                                className="basic-single"
                                classNamePrefix="select"
                                styles={{
                                    control: (provided) => ({
                                        ...provided,
                                        borderRadius: '0.375rem',
                                    }),
                                }}
                            />
                            <InputError message={errors.status_id} className="mt-1" />
                        </div>
                    </form>

                    <div className="flex flex-wrap w-full p-6 border-t shadow-t-lg border-gray-200 bg-white justify-center gap-3 rounded-b-lg">
                        <Button
                            type="submit"
                            form="form"
                            className="bg-locatel-medio hover:bg-locatel-oscuro active:bg-locatel-oscuro flex flex-row items-center h-12 text-white rounded-md px-6 py-3 shadow hover:brightness-95 disabled:opacity-50 transition-all"
                            disabled={processing}
                        >
                            {processing ? (
                                <div className="flex items-center">
                                    <Spinner className="mr-2 h-5 w-5" />
                                    <span className="flex items-center">Generando reporte...</span>
                                </div>
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