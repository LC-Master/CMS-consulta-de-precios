import AppLayout from '@/layouts/app-layout'
import { BreadcrumbItem } from '@/types'
import Select from 'react-select'
import { Input } from '@/components/ui/input'

interface Center {
    id: string;
    name: string;
    code: string;
}

interface Department {
    id: string;
    name: string;
}

interface Agreement {
    id: string;
    name: string;
}

interface Campaign {
    id: string;
    title: string;
    start_at: string;
    end_at: string;
    department?: Department;
    agreement?: Agreement;
    centers?: Center[];
}

interface Props {
    campaign: Campaign;
}

export default function CampaignShow({ campaign }: Props) {

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Ver campaña',
            href: `/campaign/${campaign.id}`,
        },
    ];

    const formatDate = (dateString?: string) => {
        if (!dateString) return '';
        return dateString.substring(0, 16);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <div className="p-6 space-y-6">
                <div className="space-y-4">

                    {/* Fila 1 */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700">Título</label>
                            <Input
                                type="text"
                                value={campaign.title}
                                disabled
                                className="mt-1 block w-full rounded-lg border border-gray-300 disabled:opacity-100 disabled:text-black  px-3 py-2 bg-gray-100 text-gray-600 cursor-not-allowed"
                            />
                        </div>

                        <div>
                            <label className="block text-sm font-medium">Departamento</label>
                            <Select
                                value={campaign.department ? {
                                    label: campaign.department.name,
                                    value: campaign.department.id
                                } : null}
                                isDisabled={true}
                                className="mt-1 disabled:text-black disabled:opacity-100"
                                classNamePrefix="react-select"
                                placeholder="Sin departamento asignado"
                                styles={{
                                    singleValue: (base, state) => ({
                                        ...base,
                                        color: state.isDisabled ? 'black' : base.color,
                                        opacity: state.isDisabled ? 1 : base.opacity,
                                    }),
                                    control: (base, state) => ({
                                        ...base,
                                        opacity: state.isDisabled ? 1 : base.opacity,
                                        backgroundColor: state.isDisabled ? '#f3f4f6' : base.backgroundColor,
                                        borderColor: state.isDisabled ? '#d1d5db' : base.borderColor,
                                    })
                                }}
                            />
                        </div>
                    </div>

                    {/* Fila 2: Fechas */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700">Fecha y hora (inicio)</label>
                            <Input
                                type="datetime-local"
                                value={formatDate(campaign.start_at)}
                                disabled
                                className="mt-1 disabled:opacity-100 disabled:text-black block w-full rounded-lg border border-gray-300 px-3 py-2 bg-gray-100 text-gray-600 cursor-not-allowed"
                            />
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700">Fecha y hora (fin)</label>
                            <Input
                                type="datetime-local"
                                value={formatDate(campaign.end_at)}
                                disabled
                                className="mt-1 block disabled:opacity-100 disabled:text-black  w-full rounded-lg border border-gray-300 px-3 py-2 bg-gray-100 text-gray-600 cursor-not-allowed"
                            />
                        </div>
                    </div>

                    {/* Fila 3: Centros (Multi Select) */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700">Centros</label>
                        <Select
                            isMulti
                            value={campaign.centers?.map(center => ({
                                label: `${center.name} - ${center.code}`,
                                value: center.id
                            }))}
                            isDisabled={true}
                            className="mt-1"
                            classNamePrefix="react-select"
                            placeholder="Sin centros asignados"
                            styles={{
                                singleValue: (base, state) => ({
                                    ...base,
                                    color: state.isDisabled ? 'black' : base.color,
                                    opacity: state.isDisabled ? 1 : base.opacity,
                                }),
                                control: (base, state) => ({
                                    ...base,
                                    opacity: state.isDisabled ? 1 : base.opacity,
                                    backgroundColor: state.isDisabled ? '#f3f4f6' : base.backgroundColor,
                                    borderColor: state.isDisabled ? '#d1d5db' : base.borderColor,
                                })
                            }}
                        />
                    </div>

                    {/* Fila 4: Acuerdo */}
                    <div>
                        <label className="block text-sm font-medium text-gray-700">Acuerdo</label>
                        <Select
                            value={campaign.agreement ? {
                                label: campaign.agreement.name,
                                value: campaign.agreement.id
                            } : null}
                            isDisabled={true}
                            className="mt-1"
                            classNamePrefix="react-select"
                            placeholder="Sin acuerdo asignado"
                            styles={{
                                singleValue: (base, state) => ({
                                    ...base,
                                    color: state.isDisabled ? 'black' : base.color,
                                    opacity: state.isDisabled ? 1 : base.opacity,
                                }),
                                control: (base, state) => ({
                                    ...base,
                                    opacity: state.isDisabled ? 1 : base.opacity,
                                    backgroundColor: state.isDisabled ? '#f3f4f6' : base.backgroundColor,
                                    borderColor: state.isDisabled ? '#d1d5db' : base.borderColor,
                                })
                            }}
                        />
                    </div>
                </div>

                {/* Botones de Acción */}
                <div className="flex flex-wrap justify-center gap-3">
                    <button
                        type="button"
                        onClick={() => window.history.back()}
                        className="bg-locatel-medio text-white rounded-md px-6 py-3 shadow hover:brightness-95"
                    >
                        Volver
                    </button>
                </div>
            </div>
        </AppLayout>
    )
}