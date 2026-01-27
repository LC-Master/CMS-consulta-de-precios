import ErrorLayout from "@/layouts/error-layout";
import { home } from "@/routes";
import { Link } from "@inertiajs/react";

export default function Error({ status }: { status: number }) {
    const title = {
        404: 'Página no encontrada',
        403: 'Acceso denegado',
    }[status] || 'Error del servidor';

    const description = {
        404: 'Lo sentimos, la página que buscas no se pudo encontrar.',
        403: 'No tienes permiso para acceder a esta página.',
    }[status] || 'Ocurrió un error inesperado';

    return (
        <ErrorLayout>
            <h1 className="text-9xl caret-transparent font-bold text-locatel-medio">{status}</h1>
            <h2 className="text-2xl caret-transparent mt-4 font-semibold">{title}</h2>
            <p className="text-gray-600 caret-transparent mt-2 text-center">{description}</p>
            <Link
                viewTransition
                href={home().url}
                className="mt-6 px-4 py-2 bg-locatel-oscuro border-transparent focus:border-locatel-oscuro border-2 focus:outline-none text-white rounded-md hover:bg-locatel-medio transition-colors"
            >
                Regresar al Inicio
            </Link>
        </ErrorLayout>
    )
}