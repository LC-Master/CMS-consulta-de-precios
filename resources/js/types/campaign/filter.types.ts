export interface FiltersProps {
    filters: FilterConfig[];
    className?: string;
}
export type FilterConfig =
    | {
          type: 'search';
          key: string;
          placeholder?: string;
          value: string;
          onChange: (value: string) => void;
      }
    | {
          type: 'select';
          key: string;
          placeholder?: string;
          value: string | number;
          options: { value: string | number; label: string }[];
          onChange: (value: string) => void;
      };
