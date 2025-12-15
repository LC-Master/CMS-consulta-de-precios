import { MediaItem } from "@/types/campaign/index.types";

type BoardSections = {
    [key: string]: MediaItem[];
};

export function findBoardSectionContainer(
    boardSections: BoardSections,
    id: string
) {
    if (id in boardSections) {
        return id;
    }

    const container = Object.keys(boardSections).find((key) =>
        boardSections[key].find((item) => item.id === id)
    );

    return container;
}

export function getTaskById(tasks: MediaItem[], id: string) {
    return tasks.find((task) => task.id === id);
}
