import AppLayout from '@/layouts/app-layout'
import { Tab, TabGroup, TabList, TabPanel, TabPanels } from '@headlessui/react'
import { Form } from '@inertiajs/react'
import { useState } from "react"
export default function CampaignCreate() {
    const [selectedIndex, setSelectedIndex] = useState(0)
    const steps = [
        { name: 'Paso 1', title: 'Campaña' },
        { name: 'Paso 2', title: 'Configuración de Opciones' },
        { name: 'Paso 3', title: 'Confirmación e Instalación' },
    ]
    const goToNextStep = () => {
        if (selectedIndex < steps.length - 1) {
            setSelectedIndex(selectedIndex + 1)
           
        }
    }

    const goToPreviousStep = () => {
        if (selectedIndex > 0) {
            setSelectedIndex(selectedIndex - 1)
        }
    }

    const handleFinish = () => {
        alert('¡Instalación simulada completada con éxito!')
    }
    return (
        <AppLayout>
            <div className="max-w-x mt-4 mx-auto p-6 bg-white shadow-lg rounded-xl">

                <TabGroup
                    selectedIndex={selectedIndex}
                    onChange={setSelectedIndex}
                    manual
                >
                    <TabList className="hidden">
                        {steps.map((step) => (
                            <Tab
                                key={step.name}
                            >
                                {step.name}
                            </Tab>
                        ))}
                    </TabList>

                    <TabPanels className="w-[900px] h-[400px]">
                        <TabPanel className="flex flex-col p-4 rounded-xl bg-gray-50 w-full h-full shadow-md">
                            <h3 className="text-xl font-bold mb-3 text-locatel-oscuro shrink-0">{steps[0].title}</h3>
                            <Form className="grid grid-cols-2 gap-4">
                                <div className="flex flex-col col-span-2">
                                    <label htmlFor="name" className="text-sm font-medium mb-1 text-gray-700">Nombre de la Campaña</label>
                                    <input 
                                        type="text" 
                                        id="name" 
                                        name="name" 
                                        placeholder="Ej: Campaña de Verano"
                                        className="block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-locatel-claro focus:border-locatel-claro"
                                    />
                                </div>

                                <div className="flex flex-col">
                                    <label htmlFor="startAt" className="text-sm font-medium mb-1 text-gray-700">Fecha de Inicio</label>
                                    <input 
                                        type="date" 
                                        id="startAt" 
                                        name="startAt" 
                                        className="block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-locatel-claro focus:border-locatel-claro"
                                    />
                                </div>

                                <div className="flex flex-col">
                                    <label htmlFor="endAt" className="text-sm font-medium mb-1 text-gray-700">Fecha de Fin</label>
                                    <input 
                                        type="date" 
                                        id="endAt" 
                                        name="endAt" 
                                        className="block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-locatel-claro focus:border-locatel-claro"
                                    />
                                </div>

                                <div className="flex flex-col">
                                    <label htmlFor="status" className="text-sm font-medium mb-1 text-gray-700">Estado</label>
                                    <select 
                                        id="status" 
                                        name="status" 
                                        className="block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-locatel-claro focus:border-locatel-claro"
                                    >
                                        <option value="active">Activo</option>
                                        <option value="inactive">Inactivo</option>
                                    </select>
                                </div>

                                <div className="flex flex-col">
                                    <label htmlFor="agreement" className="text-sm font-medium mb-1 text-gray-700">Convenio</label>
                                    <select 
                                        id="agreement" 
                                        name="agreement" 
                                        className="block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-locatel-claro focus:border-locatel-claro"
                                    >
                                        <option value="active">Activo</option>
                                        <option value="inactive">Inactivo</option>
                                    </select>
                                </div>

                                <div className="flex flex-col">
                                    <label htmlFor="department" className="text-sm font-medium mb-1 text-gray-700">Departamento</label>
                                    <select 
                                        id="department" 
                                        name="department" 
                                        className="block w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-locatel-claro focus:border-locatel-claro"
                                    >
                                        <option value="medicine">Medicina</option>
                                        <option value="nursing">Enfermería</option>
                                    </select>
                                </div>
                            </Form>
                        </TabPanel>

                        <TabPanel className="p-4 rounded-xl bg-gray-50 w-full h-full">
                            <h3 className="text-xl font-semibold mb-3">{steps[1].title}</h3>
                            <label className="block mb-2">
                                Ruta de Instalación:
                                <input type="text" defaultValue="/aplicacion/" className="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2" />
                            </label>
                        </TabPanel>

                        <TabPanel className="p-4 rounded-xl bg-gray-50 w-full h-full">
                            <h3 className="text-xl font-semibold mb-3">{steps[2].title}</h3>
                            <p>Revisa la configuración. Al hacer clic en "Finalizar", comenzará la instalación.</p>
                            <ul className="list-disc list-inside mt-3">
                                <li>Ruta: /aplicacion/</li>
                                <li>Modo: Completo</li>
                            </ul>
                        </TabPanel>
                    </TabPanels>

                    <div className="mt-6 flex justify-between">
                        <button
                            onClick={goToPreviousStep}
                            disabled={selectedIndex === 0}
                            className={`px-4 py-2 text-sm font-medium rounded-md transition-colors 
              ${selectedIndex === 0
                                    ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                                    : 'bg-locatel-claro text-white hover:bg-locatel-oscuro'
                                }`
                            }
                        >
                            &larr; Anterior
                        </button>

                        {selectedIndex < steps.length - 1 ? (
                            <button
                                onClick={goToNextStep}
                                className="px-4 py-2 text-sm font-medium rounded-md bg-locatel-claro hover:bg-locatel-oscuro text-white transition-colors"
                            >
                                Siguiente &rarr;
                            </button>
                        ) : (
                            <button
                                onClick={handleFinish}
                                className="px-4 py-2 text-sm font-medium rounded-md bg-locatel-oro text-white hover:bg-locatel-naranja transition-colors"
                            >
                                Finalizar
                            </button>
                        )}
                    </div>
                </TabGroup>
            </div>
        </AppLayout >
    )
}