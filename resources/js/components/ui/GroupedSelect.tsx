import { RegionData } from '@/types/store/index.type';
import { useMemo, useState, useEffect, CSSProperties } from 'react';
import Select, { MultiValue, ActionMeta, StylesConfig } from 'react-select';

interface SelectOption {
    value: string;
    label: string;
    isAll?: boolean;
    isRegionAll?: boolean;
    storeIds?: string[];
    region?: string;
}

interface GroupedOption {
    label: string;
    options: SelectOption[];
    quantity?: number;
}

const groupStyles = {
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'space-between',
};

const groupBadgeStyles: CSSProperties = {
    backgroundColor: '#0052CC',
    borderRadius: '4px',
    color: '#fff',
    display: 'inline-block',
    fontSize: '10px',
    fontWeight: 'bold',
    padding: '2px 8px',
};

const customStyles: StylesConfig<SelectOption, true, GroupedOption> = {
    control: (base) => ({
        ...base,
        borderRadius: '0.375rem',
        borderColor: '#ccc',
        minHeight: '30px',
        boxShadow: 'none',
        '&:hover': { borderColor: '#0052CC' }
    }),
    option: (base, state) => ({
        ...base,
        fontSize: '13px',
        padding: '10px 15px',
        backgroundColor: state.isFocused ? '#f0f5ff' : 'white',
        color: state.isFocused ? '#0052CC' : '#333',
        cursor: 'pointer'
    }),
    multiValue: (base) => ({
        ...base,
        backgroundColor: '#e7effc',
        border: '1px solid #0052CC',
        borderRadius: '4px'
    }),
    multiValueLabel: (base) => ({
        ...base,
        color: '#0052CC',
        fontWeight: '600'
    })
};

const formatGroupLabel = (data: GroupedOption) => (
    <div style={groupStyles}>
        <span>{data.label}</span>
        <span style={groupBadgeStyles}>{data.quantity !== undefined ? data.quantity : data.options.length - 1} SUCURSALES</span>
    </div>
);

const GroupedSelect = ({
    dataFromBackend,
    initialSelectedIds = [],
    onSelectionChange
}: {
    dataFromBackend: RegionData[],
    initialSelectedIds?: string[],
    onSelectionChange: (ids: string[]) => void
}) => {
    const [selectedValues, setSelectedValues] = useState<SelectOption[]>([]);

    const options = useMemo(() => {
        if (!dataFromBackend) return [];

        const totalStores = dataFromBackend.reduce((acc, curr) => acc + curr.stores.length, 0);

        const globalGroup: GroupedOption = {
            label: "ADMINISTRACIÓN SISTEMA",
            quantity: totalStores,
            options: [
                {
                    value: 'GLOBAL_ALL',
                    label: 'SELECCIONAR TODAS LAS SUCURSALES (NACIONAL)',
                    isAll: true
                }
            ]
        };

        const regionGroups: GroupedOption[] = dataFromBackend.map(item => ({
            label: item.region.toUpperCase(),
            quantity: item.stores.length,
            options: [
                {
                    value: `REGION_ALL_${item.region}`,
                    label: `SELECCIONAR TODA LA REGIÓN: ${item.region}`,
                    isRegionAll: true,
                    region: item.region,
                    storeIds: item.stores.map(s => s.id)
                },
                ...item.stores.map(store => ({
                    value: store.id,
                    label: `${store.name} (${store.store_code})`,
                    region: item.region,
                    ...store
                }))
            ]
        }));

        return [globalGroup, ...regionGroups];
    }, [dataFromBackend]);

    useEffect(() => {
        if (!dataFromBackend || dataFromBackend.length === 0) return;

        const calculateSelection = () => {
            if (!initialSelectedIds || initialSelectedIds.length === 0) {
                return [];
            }

            const allStoreIds = dataFromBackend.flatMap(r => r.stores.map(s => s.id));
            const isGlobalAll = allStoreIds.length > 0 && allStoreIds.every(id => initialSelectedIds.includes(id));

            if (isGlobalAll) {
                const globalOpt = options.find(g => g.options.some(o => o.isAll))?.options.find(o => o.isAll);
                if (globalOpt) {
                    return [globalOpt];
                }
            }

            const nextSelection: SelectOption[] = [];

            dataFromBackend.forEach(region => {
                const regionIds = region.stores.map(s => s.id);
                const isRegionAll = regionIds.length > 0 && regionIds.every(id => initialSelectedIds.includes(id));

                const regionGroup = options.find(g => g.label === region.region.toUpperCase());
                if (!regionGroup) return;

                if (isRegionAll) {
                    const regionAllOpt = regionGroup.options.find(o => o.isRegionAll);
                    if (regionAllOpt) nextSelection.push(regionAllOpt);
                } else {
                    const storesInRegion = region.stores.filter(s => initialSelectedIds.includes(s.id));
                    storesInRegion.forEach(store => {
                        const storeOpt = regionGroup.options.find(o => o.value === store.id);
                        if (storeOpt) nextSelection.push(storeOpt);
                    });
                }
            });

            return nextSelection;
        };

        const newSelection = calculateSelection();

        const timeoutId = setTimeout(() => {
            setSelectedValues(newSelection);
        }, 0);

        return () => clearTimeout(timeoutId);

    }, [initialSelectedIds, dataFromBackend, options]);

    const handleChange = (newValue: MultiValue<SelectOption>, actionMeta: ActionMeta<SelectOption>) => {
        let selectionArray = [...newValue] as SelectOption[];
        const action = actionMeta.action;
        const option = actionMeta.option;

        if (action === 'select-option' && option) {
            if (option.isAll) {
                selectionArray = [option];
            }
            else if (option.isRegionAll) {
                selectionArray = selectionArray.filter(o =>
                    o.value === option.value ||
                    (!o.isAll &&
                        o.region !== option.region
                    )
                );
            }
            else {
                const hasGlobal = selectionArray.some(o => o.isAll);
                const regionAllOption = selectionArray.find(o => o.isRegionAll && o.region === option.region);

                if (hasGlobal) {
                    const allStores: SelectOption[] = [];
                    dataFromBackend.forEach(r => {
                        r.stores.forEach(s => {
                            if (s.id !== option.value) {
                                allStores.push({
                                    value: s.id,
                                    label: `${s.name} (${s.store_code})`,
                                    region: r.region,
                                    ...s
                                });
                            }
                        });
                    });
                    selectionArray = allStores;
                }
                else if (regionAllOption) {
                    const regionStores = dataFromBackend.find(r => r.region === option.region)?.stores || [];
                    const expandedStores = regionStores
                        .filter(s => s.id !== option.value)
                        .map(s => ({
                            value: s.id,
                            label: `${s.name} (${s.store_code})`,
                            region: option.region,
                            ...s
                        }));
                    selectionArray = selectionArray.filter(o => o.value !== regionAllOption.value && o.value !== option.value);
                    selectionArray = [...selectionArray, ...expandedStores];
                }
            }
        }

        setSelectedValues(selectionArray);

        let finalIds: string[] = [];
        selectionArray.forEach(opt => {
            if (opt.isAll) {
                finalIds = dataFromBackend.flatMap(r => r.stores.map(s => s.id));
            } else if (opt.isRegionAll && opt.storeIds) {
                finalIds = [...finalIds, ...opt.storeIds];
            } else {
                finalIds.push(opt.value);
            }
        });

        onSelectionChange([...new Set(finalIds)]);
    };

    return (
        <div className="select-container" style={{ width: '100%', maxWidth: '600px' }}>
            <Select<SelectOption, true, GroupedOption>
                isMulti
                value={selectedValues}
                options={options}
                formatGroupLabel={formatGroupLabel}
                onChange={handleChange}
                placeholder="Búsqueda de sucursales..."
                isClearable
                closeMenuOnSelect={false}
                styles={customStyles}
                noOptionsMessage={() => "No se encontraron resultados"}
            />
        </div>
    );
};

export default GroupedSelect;