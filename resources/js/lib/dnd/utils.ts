import { MediaItem } from "@/types/campaign/index.types";

type BoardSections = {
    [key: string]: MediaItem[];
};

export function findBoardSectionContainer(
    boardSections: BoardSections,
    id?: string | number
) {
    if (id === undefined || id === null) {
        return undefined;
    }

    const key = typeof id === "string" ? id : String(id);

    if (key in boardSections) {
        return key;
    }

    const container = Object.keys(boardSections).find((key) =>
        boardSections[k].find((item) => item.id === key)
    );

    return container;
}

export function getTaskById(tasks: MediaItem[], id: string) {
    return tasks.find((task) => task.id === id);
}
