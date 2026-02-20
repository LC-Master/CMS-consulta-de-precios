import ms from 'ms';
import { useEffect, useRef } from 'react';

export function useUpdateEffect(effect: () => void | (() => void), deps: React.DependencyList) {
    const isFirstRender = useRef(true);
    const previousDeps = useRef(deps);

    useEffect(() => {
        if (isFirstRender.current) {
            isFirstRender.current = false;
            previousDeps.current = deps;
            return;
        }

        const isSame = deps.every((dep, i) => dep === previousDeps.current[i]);
        if (isSame) {
            return;
        }

        previousDeps.current = deps;

        const timeout = setTimeout(() => {
            effect();
        }, ms('500ms'));

        return () => clearTimeout(timeout);

        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, deps);
}
