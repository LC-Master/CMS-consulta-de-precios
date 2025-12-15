import { useForm } from "@inertiajs/react"
import React from "react"

export default function Show() {
    const { post, data, setData, progress } = useForm({
        file: null as File | null,
    })
    const submit = (e: React.FormEvent) => {
        e.preventDefault()
        post('/video-upload')
    }

    return (
        <form
            className="w-full h-2 flex flex-col gap-2 m-2"
            action="POST"
            onDragOver={(e: React.DragEvent<HTMLFormElement>) => e.preventDefault()}
            onDrop={(e: React.DragEvent<HTMLFormElement>) => {
                e.preventDefault()
                setData("file", e.dataTransfer.files[0])
            }}
            onSubmit={submit}
        >
            <label htmlFor="file">File:</label>
            <input
                type="file"
                name="file"
                id="file"
                onChange={(e) => setData("file", e.target.files ? e.target.files[0] : null)}
            />
            <button type="submit">Submit</button>
            {progress && (
                <progress className="w-3 " value={progress.percentage} max="100">
                    {progress.percentage}%
                </progress>
            )}
            <div className="mt-2">
                {data.file ? (
                    <div className="p-2 border rounded">
                        <p><strong>Nombre:</strong> {data.file.name}</p>
                        <p><strong>Tamaño:</strong> {Math.round(data.file.size / 1024)} KB</p>
                        <p><strong>Tipo:</strong> {data.file.type || "desconocido"}</p>
                        {data.file.type && data.file.type.startsWith("image/") && (
                            <img
                                src={URL.createObjectURL(data.file)}
                                alt="preview"
                                className="mt-2 max-w-xs max-h-40 object-contain"
                            />
                        )}
                    </div>
                ) : (
                    <p className="text-sm text-gray-500">Arrastra un archivo aquí o usa el selector.</p>
                )}
            </div>
        </form>
    )
}