import { CampaignEditProps } from '@/types/campaign/page.type';
import { MediaItem } from '@/types/media/index.type';
import { useEffect, useRef } from 'react';

export default function useLoadEdit(
    campaign: CampaignEditProps['campaign'],
    setAm: React.Dispatch<React.SetStateAction<MediaItem[]>>,
    setPm: React.Dispatch<React.SetStateAction<MediaItem[]>>,
) {
    const hasLoaded = useRef(false);

    useEffect(() => {
        if (hasLoaded.current) return;

        if (campaign && campaign.media) {
            const sortedMedia = [...campaign.media].sort((a, b) =>
                (a.position || '').localeCompare(b.position || '')
            );

            setAm(sortedMedia.filter((item: MediaItem) => item.slot === 'am'));
            setPm(sortedMedia.filter((item: MediaItem) => item.slot === 'pm'));

            hasLoaded.current = true;
        }
    }, [campaign, setAm, setPm]); 
}