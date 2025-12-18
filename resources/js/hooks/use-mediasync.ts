import { useState } from 'react';

export function useMediaSync<T>(mediaProp: T[] | null) {
    const [mediaList, setMediaList] = useState<T[]>(mediaProp ? [...mediaProp] : []);
    const [prevMedia, setPrevMedia] = useState<T[] | null>(mediaProp);

    if (mediaProp !== prevMedia) {
        setPrevMedia(mediaProp); 
        setMediaList(mediaProp ? [...mediaProp] : []); 
    }

    const [pm, setPm] = useState<T[]>([]);
    const [am, setAm] = useState<T[]>([]);

    return { mediaList, setMediaList, pm, setPm, am, setAm };
}