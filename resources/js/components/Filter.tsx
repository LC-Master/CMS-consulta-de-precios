import { FiltersProps } from '@/types/campaign/filter.types'
import { Search } from 'lucide-react'
import Select from 'react-select'


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
            <div key={filter.key} className="relative flex-1">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-4 h-4" />
              <input
                type="text"
                placeholder={filter.placeholder ?? 'Buscar...'}
                value={filter.value}
                onChange={(e) => filter.onChange(e.target.value)}
                className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-locatel-oscuro focus:border-locatel-oscuro outline-none transition-all"
              />
            </div>
          )
        }

        if (filter.type === 'select') {
          return (
            <div key={filter.key} className="w-full sm:w-48">
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
                />
            </div>
          )
        }

        return null
      })}
    </div>
  )
}
