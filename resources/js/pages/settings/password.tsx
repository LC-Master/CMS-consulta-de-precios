import PasswordController from '@/actions/App/Http/Controllers/Settings/PasswordController';
import InputError from '@/components/input-error';
import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import { type BreadcrumbItem } from '@/types';
import { Transition } from '@headlessui/react';
import { Form, Head } from '@inertiajs/react';
import { useRef } from 'react';
import { Save } from 'lucide-react'; // Icono para el botón

import HeadingSmall from '@/components/heading-small';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit } from '@/routes/user-password';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Configuración de contraseña',
        href: edit().url,
    },
];

export default function Password() {
    const passwordInput = useRef<HTMLInputElement>(null);
    const currentPasswordInput = useRef<HTMLInputElement>(null);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Configuración de contraseña" />

            <SettingsLayout>
                <div className="max-w-2xl mx-auto space-y-6">
                    <HeadingSmall
                        title="Actualizar contraseña"
                        description="Asegúrate de que tu cuenta esté usando una contraseña larga y aleatoria para mantenerla segura"
                    />

                    {/* Tarjeta contenedora para mejor diseño */}
                    <div className="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                        <Form
                            {...PasswordController.update.form()}
                            options={{
                                preserveScroll: true,
                            }}
                            resetOnError={[
                                'password',
                                'password_confirmation',
                                'current_password',
                            ]}
                            resetOnSuccess
                            onError={(errors) => {
                                if (errors.password) {
                                    passwordInput.current?.focus();
                                }

                                if (errors.current_password) {
                                    currentPasswordInput.current?.focus();
                                }
                            }}
                            className="space-y-6"
                        >
                            {({ errors, processing, recentlySuccessful }) => (
                                <>
                                    <div className="grid gap-2">
                                        <Label htmlFor="current_password">
                                            Contraseña actual
                                        </Label>

                                        <Input
                                            id="current_password"
                                            ref={currentPasswordInput}
                                            name="current_password"
                                            type="password"
                                            className="mt-1 block w-full focus:ring-locatel-medio focus:border-locatel-medio"
                                            autoComplete="current-password"
                                            placeholder="Ingresa tu contraseña actual"
                                        />

                                        <InputError
                                            message={errors.current_password}
                                        />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="password">
                                            Nueva contraseña
                                        </Label>

                                        <Input
                                            id="password"
                                            ref={passwordInput}
                                            name="password"
                                            type="password"
                                            className="mt-1 block w-full focus:ring-locatel-medio focus:border-locatel-medio"
                                            autoComplete="new-password"
                                            placeholder="Ingresa la nueva contraseña"
                                        />

                                        <InputError message={errors.password} />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="password_confirmation">
                                            Confirmar contraseña
                                        </Label>

                                        <Input
                                            id="password_confirmation"
                                            name="password_confirmation"
                                            type="password"
                                            className="mt-1 block w-full focus:ring-locatel-medio focus:border-locatel-medio"
                                            autoComplete="new-password"
                                            placeholder="Repite la nueva contraseña"
                                        />

                                        <InputError
                                            message={errors.password_confirmation}
                                        />
                                    </div>

                                    <div className="flex items-center gap-4 pt-4 border-t border-gray-100">
                                        <Button
                                            disabled={processing}
                                            data-test="update-password-button"
                                            // CLASES ACTUALIZADAS: Verde corporativo
                                            className="bg-locatel-medio hover:bg-locatel-oscuro text-white transition-colors"
                                        >
                                            {processing ? (
                                                'Guardando...'
                                            ) : (
                                                <>
                                                    <Save className="w-4 h-4 mr-2" />
                                                    Guardar contraseña
                                                </>
                                            )}
                                        </Button>

                                        <Transition
                                            show={recentlySuccessful}
                                            enter="transition ease-in-out"
                                            enterFrom="opacity-0"
                                            leave="transition ease-in-out"
                                            leaveTo="opacity-0"
                                        >
                                            <p className="text-sm text-green-600 font-medium flex items-center">
                                                ¡Guardado correctamente!
                                            </p>
                                        </Transition>
                                    </div>
                                </>
                            )}
                        </Form>
                    </div>
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}