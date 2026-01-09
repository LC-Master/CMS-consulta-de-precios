import { FiltersProps } from '@/types/campaign/filter.types'
import { RefreshCcw, Search } from 'lucide-react'
import Select from 'react-select'
import { Button } from './ui/button'
import { Label } from './ui/label'


export function Filter({ filters, className }: FiltersProps) {
  return (
    <div
      className={
        className ??
        'flex flex-col sm:flex-row gap-4 mt-4 bg-white p-4 rounded-lg shadow-sm border border-gray-200'
      }
    >
      {filters.map((filter) => {
        if (filter.type === 'search') {
          return (
            <div key={filter.key} className="flex-1">
              <Label htmlFor={`search-${filter.key}`} className="block text-sm font-medium text-gray-700 mb-1">
                {filter.label ?? 'Buscar...'}
              </Label>
              <div className="relative">
                <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-4 h-4 pointer-events-none" />
                <input
                  id={`search-${filter.key}`}
                  type="text"
                  placeholder={filter.placeholder ?? 'Buscar...'}
                  value={filter.value}
                  onChange={(e) => filter.onChange(e.target.value)}
                  className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-locatel-oscuro focus:border-locatel-oscuro outline-none transition-all"
                />
              </div>
            </div>
          )
        }
        if (filter.type === 'select') {
          return (
            <div key={filter.key} className="w-full sm:w-48">
              <Label htmlFor={`select-${filter.key}`} className="block text-sm font-medium text-gray-700 mb-1">
                {filter.label ?? 'Selecciona una opción'}
              </Label>
              <Select
                options={filter.options}
                value={
                  filter.options.find(o => o.value === filter.value) ??
                  null
                }
                onChange={(opt) => filter.onChange(String(opt?.value ?? ''))}
                placeholder={filter.placeholder}
                isClearable={false}
                classNamePrefix="select"
                styles={{
                  container: (provided) => ({
                    ...provided,
                    width: '100%',
                  }),
                  control: (provided, state) => ({
                    ...provided,
                    minHeight: '2.5rem',
                    borderRadius: '0.375rem',
                    borderColor: state.isFocused ? '#047857' : '#D1D5DB',
                    boxShadow: state.isFocused ? '0 0 0 2px rgba(4,120,87,0.15)' : 'none',
                    '&:hover': {
                      borderColor: state.isFocused ? '#047857' : '#064E3B',
                    },
                    paddingLeft: 0,
                  }),
                  valueContainer: (provided) => ({
                    ...provided,
                    padding: '0 1rem',
                    height: '2.5rem',
                    alignItems: 'center',
                  }),
                  placeholder: (provided) => ({
                    ...provided,
                    color: '#6B7280',
                  }),
                  singleValue: (provided) => ({
                    ...provided,
                    color: '#111827',
                  }),
                  indicatorsContainer: (provided) => ({
                    ...provided,
                    paddingRight: '0.5rem',
                  }),
                  menu: (provided) => ({
                    ...provided,
                    zIndex: 50,
                  }),
                  option: (provided, state) => ({
                    ...provided,
                    backgroundColor: state.isSelected
                      ? '#047857'
                      : state.isFocused
                        ? '#ECFDF5'
                        : '#FFFFFF',
                    color: state.isSelected ? '#FFFFFF' : '#111827',
                    cursor: 'pointer',
                    '&:active': {
                      backgroundColor: state.isSelected ? '#065F46' : '#D1FAE5',
                    },
                  }),
                }}
              />
            </div>
          )
        }
        if (filter.type === 'date') {
          return (
            <div key={filter.key} className="w-full sm:w-48">
              <Label htmlFor={`date-${filter.key}`} className="block text-sm font-medium text-gray-700 mb-1">
                {filter.label ?? 'Fecha'}
              </Label>
              <input
                id={`date-${filter.key}`}
                type="date"
                value={filter.value}
                onChange={(e) => filter.onChange(e.target.value)}
                className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-locatel-oscuro focus:border-locatel-oscuro outline-none transition-all"
              />
            </div>
          )
        }
        if (filter.type === 'reset') {
          return (
            <div key={filter.key} >
              <Label className="block text-sm font-medium text-gray-700 mb-1 invisible">Reiniciar</Label>
              <Button
                key={filter.key}
                onClick={filter.onReset}
                className="h-10 px-4 py-2 bg-locatel-medio text-white rounded-md hover:bg-locatel-oscuro transition"
              >
                <RefreshCcw className="inline w-4 h-4" />
              </Button>
            </div>
          )
        }
        return null
      })}
    </div>
  )
}
