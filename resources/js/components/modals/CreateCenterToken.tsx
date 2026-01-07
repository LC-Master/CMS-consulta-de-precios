import { Center, Option } from "@/types/campaign/index.types";
import Modal from "@/components/Modal";
import Select from 'react-select';
import { useForm } from "@inertiajs/react";
import { Button } from "../ui/button";
import InputError from "../input-error";
import { Input } from "../ui/input";
import { Label } from "../ui/label";
import { store } from "@/routes/centertokens";
import { useState } from "react";
import { Spinner } from "../ui/spinner";

export default function CreateCenterToken({ centers, closeModal }: { closeModal: () => void, centers: Center[] }) {
    const [tokenValue, setTokenValue] = useState<string | null>(null);
    const { post, setData, processing, errors, cancel } = useForm({
        name: '',
        center_id: ''
    });
    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        post(store().url, {
            onSuccess: (page) => {
                if (page?.props?.flash?.error) {
                    setTokenValue(null);
                    closeModal();
                    return;
                }
                setTokenValue(page?.props?.flash?.success?.token ?? null);
            },
            onError: () => {
                setTokenValue(null);
                closeModal();
            }
        });
    }
    return (
        <Modal actionWhenCloseTouchOutside={cancel} className="border-locatel-claro border-2 bg-white" closeModal={closeModal} blur={false} >
            <form onSubmit={handleSubmit} method="post" action={store().url} className="w-full">
                <div className="p-6 space-y-6">
                    <header className="border-b pb-3 mb-4">
                        <h1 className="text-lg font-semibold text-gray-800">Crear nuevo token</h1>
                        <p className="text-sm text-gray-600 mt-1">
                            Tome en cuenta que una vez creado el token, no podrá volver a ver su valor. Guárdelo en un lugar seguro.
                        </p>
                    </header>

                    <div className="space-y-2">
                        <Label htmlFor="name" className="block text-sm font-medium text-gray-700">
                            Nombre del token
                        </Label>
                        <Input
                            type="text"
                            name="name"
                            id="name"
                            className="block w-full border border-gray-200 rounded-md bg-gray-50 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-locatel-oscuro focus:border-transparent"
                            placeholder="Nombre del token"
                            required
                            onChange={(e) => setData('name', e.target.value)}
                        />
                        <InputError message={errors.name} />
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="center_id" className="block text-sm font-medium text-gray-700">
                            Centro asociado
                        </Label>
                        <div className="text-sm">
                            <Select<Option, false>
                                name="center_id"
                                id="center_id"
                                options={centers.map(center => ({ value: center.id, label: `${center.name} (${center.code})` }))}
                                className="basic-multi-select"
                                classNamePrefix="select"
                                onChange={(e) => setData('center_id', e?.value || '')}
                                placeholder="Selecciona un centro"
                                styles={{
                                    option: (provided) => ({ ...provided, padding: '8px 10px', minHeight: 'auto', fontSize: '13px' }),
                                    control: (provided) => ({ ...provided, minHeight: '40px', borderRadius: '6px', borderColor: '#e5e7eb', boxShadow: 'none' }),
                                    valueContainer: (provided) => ({ ...provided, padding: '0 8px' }),
                                    singleValue: (provided) => ({ ...provided, margin: 0 }),
                                    indicatorsContainer: (provided) => ({ ...provided, height: '40px' }),
                                    menu: (provided) => ({ ...provided, marginTop: 6, minWidth: '200px', borderRadius: '6px', overflow: 'hidden' }),
                                    menuList: (provided) => ({ ...provided, maxHeight: '200px', padding: '6px' }),
                                }}
                                required
                            />
                        </div>
                        <InputError message={errors.center_id} />
                        {tokenValue && (
                            <div className="mt-4 p-4 bg-green-50 border border-green-200 rounded-md">
                                <h2 className="text-sm font-medium text-green-800 mb-2">Token creado exitosamente</h2>
                                <p className="text-sm text-green-700 break-all">{tokenValue}</p>
                            </div>
                        )}
                    </div>

                    <footer className="pt-6 border-t flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
                        {tokenValue ? (
                            <Button
                                type="button"
                                onClick={closeModal}
                            >
                                Cerrar
                            </Button>
                        ) : (
                            <>
                                <Button type="button" onClick={() => {
                                    cancel()
                                    closeModal()
                                }} className="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                                    Cancelar
                                </Button>
                                <Button type="submit" disabled={processing} className="bg-locatel-oscuro text-white px-4 py-2 rounded hover:bg-green-800">
                                    {processing ? <><Spinner /> Procesando...</> : <>Crear token</>}
                                </Button>
                            </>
                        )}
                    </footer>
                </div>
            </form>
        </Modal >
    )
}