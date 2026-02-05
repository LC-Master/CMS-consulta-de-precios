export const createVideoThumbnail = (file: File): Promise<File | null> =>
    new Promise((resolve) => {
        const url = URL.createObjectURL(file)
        const video = document.createElement('video')
        video.src = url
        video.preload = 'metadata'
        video.muted = true
        video.playsInline = true

        const cleanup = () => {
            URL.revokeObjectURL(url)
            video.remove()
        }

        video.addEventListener(
            'loadedmetadata',
            () => {
                const seekTo = Math.min(0.5, video.duration / 2)
                video.currentTime = seekTo

                video.addEventListener(
                    'seeked',
                    () => {
                        const canvas = document.createElement('canvas')
                        canvas.width = 320
                        canvas.height = 180
                        const ctx = canvas.getContext('2d')
                        if (!ctx) return resolve(null)

                        ctx.drawImage(video, 0, 0, canvas.width, canvas.height)

                        canvas.toBlob(
                            (blob) => {
                                cleanup()
                                if (!blob) return resolve(null)
                                resolve(
                                    new File([blob], `${file.name}-thumb.jpg`, {
                                        type: 'image/jpeg',
                                    })
                                )
                            },
                            'image/jpeg',
                            0.8
                        )
                    },
                    { once: true }
                )
            },
            { once: true }
        )

        video.addEventListener('error', () => {
            cleanup()
            resolve(null)
        })
    })